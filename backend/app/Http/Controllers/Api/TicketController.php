<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Events\TicketUpdated;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Resources\TicketResource;
use App\Models\SlaRule;
use App\Models\Attachment;
use App\Models\Ticket;
use App\Models\User;
use App\Services\AuditService;
use App\Services\AutoAssignmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TicketController extends Controller
{
    public function __construct(
        private readonly AuditService $audit,
        private readonly AutoAssignmentService $autoAssignment,
    ) {}
    public function index(Request $request)
    {
        $this->authorize("viewAny", Ticket::class);
        $query = Ticket::visibleTo($request->user())
            ->with(["requester", "assignee", "team", "category", "priority"])
            ->latest("updated_at");
        foreach (
            ["status", "priority_id", "category_id", "assignee_id", "team_id"]
            as $filter
        ) {
            $query->when(
                $request->filled($filter),
                fn($q) => $q->where($filter, $request->input($filter)),
            );
        }
        $query->when(
            $request->filled("search"),
            fn($q) => $q->where(
                fn($inner) => $inner
                    ->where("code", "ilike", "%" . $request->search . "%")
                    ->orWhere("title", "ilike", "%" . $request->search . "%"),
            ),
        );
        return TicketResource::collection(
            $query->paginate(min($request->integer("per_page", 20), 100)),
        );
    }
    public function store(StoreTicketRequest $request): TicketResource
    {
        $user = $request->user();
        $ticket = DB::transaction(function () use ($request, $user) {
            $data = $request->validated();
            unset($data["attachments"]);
            $data["organization_id"] = $user->organization_id;
            $data["requester_id"] = $user->id;
            $data["code"] = $this->nextCode();
            $data["status"] = "nuevo";
            $sla = SlaRule::where("organization_id", $user->organization_id)
                ->where("priority_id", $data["priority_id"])
                ->where("active", true)
                ->first();
            $data["sla_due_at"] = $sla
                ? now()->addMinutes($sla->resolution_minutes)
                : null;
            $ticket = Ticket::create($data);
            foreach ($request->file("attachments", []) as $file) {
                $path = $file->store("tickets/" . $ticket->id, "local");
                $ticket
                    ->attachments()
                    ->create([
                        "uploaded_by" => $user->id,
                        "disk" => "local",
                        "path" => $path,
                        "original_name" => $file->getClientOriginalName(),
                        "mime_type" =>
                            $file->getMimeType() ?: "application/octet-stream",
                        "size" => $file->getSize(),
                        "sha256" => hash_file("sha256", $file->getRealPath()),
                    ]);
            }
            $this->autoAssignment->assign($ticket);
            $this->audit->record(
                $ticket,
                $user,
                "ticket.created",
                [],
                [
                    "status" => $ticket->status,
                    "assignee_id" => $ticket->assignee_id,
                ],
            );
            return $ticket;
        });
        TicketUpdated::dispatch($ticket->fresh(), "ticket.created");
        return new TicketResource(
            $ticket->load([
                "requester",
                "category",
                "priority",
                "assignee",
                "team",
                "attachments",
            ]),
        );
    }
    public function show(Request $request, Ticket $ticket): TicketResource
    {
        $this->authorize("view", $ticket);
        $relations = [
            "requester",
            "assignee",
            "team",
            "category",
            "priority",
            "comments",
        ];
        $ticket->load($relations);
        if ($request->user()->hasRole("requester")) {
            $ticket->setRelation(
                "comments",
                $ticket->comments->where("is_internal", false)->values(),
            );
        }
        return new TicketResource($ticket);
    }
    public function update(Request $request, Ticket $ticket): TicketResource
    {
        $this->authorize("update", $ticket);
        $data = $request->validate([
            "title" => "sometimes|string|max:180",
            "description" => "sometimes|string|max:20000",
            "status" => "sometimes|in:nuevo,abierto,pendiente",
        ]);
        $old = $ticket->only(array_keys($data));
        $ticket->update($data);
        $this->audit->record(
            $ticket,
            $request->user(),
            "ticket.updated",
            $old,
            $data,
        );
        return new TicketResource($ticket->fresh());
    }
    public function destroy(Request $request, Ticket $ticket): JsonResponse
    {
        abort_unless($request->user()->hasRole("admin"), 403);
        $this->audit->record($ticket, $request->user(), "ticket.deleted");
        $ticket->delete();
        return response()->json(status: 204);
    }
    public function comment(Request $request, Ticket $ticket): JsonResponse
    {
        $this->authorize("view", $ticket);
        $data = $request->validate([
            "body" => "required|string|max:20000",
            "is_internal" => "sometimes|boolean",
        ]);
        if (
            ($data["is_internal"] ?? false) &&
            !$request->user()->can("addInternalNote", $ticket)
        ) {
            abort(403);
        }
        $comment = $ticket
            ->comments()
            ->create([
                "user_id" => $request->user()->id,
                "body" => $data["body"],
                "is_internal" => $data["is_internal"] ?? false,
            ]);
        if (!$ticket->first_response_at && $request->user()->hasRole("agent")) {
            $ticket->update([
                "first_response_at" => now(),
                "status" => "abierto",
            ]);
        }
        $this->audit->record(
            $ticket,
            $request->user(),
            $data["is_internal"] ?? false ? "note.created" : "comment.created",
        );
        TicketUpdated::dispatch(
            $ticket->fresh(),
            $data["is_internal"] ?? false ? "note.created" : "comment.created",
        );
        return response()->json($comment, 201);
    }
    public function assign(Request $request, Ticket $ticket): TicketResource
    {
        $this->authorize("assign", $ticket);
        $data = $request->validate([
            "assignee_id" => "nullable|exists:users,id",
            "team_id" => "nullable|exists:teams,id",
        ]);
        if (isset($data["assignee_id"])) {
            $agent = User::findOrFail($data["assignee_id"]);
            abort_unless(
                $agent->organization_id === $ticket->organization_id &&
                    $agent->hasRole("agent"),
                422,
                "El usuario no es un agente válido.",
            );
        }
        $old = $ticket->only(["assignee_id", "team_id"]);
        $ticket->update(
            $data + [
                "status" =>
                    $ticket->status === "nuevo" ? "abierto" : $ticket->status,
            ],
        );
        DB::table("ticket_assignments")->insert([
            "ticket_id" => $ticket->id,
            "assigned_by" => $request->user()->id,
            "user_id" => $data["assignee_id"] ?? null,
            "team_id" => $data["team_id"] ?? null,
            "reason" => "manual",
            "created_at" => now(),
        ]);
        $this->audit->record(
            $ticket,
            $request->user(),
            "ticket.assigned",
            $old,
            $data,
        );
        TicketUpdated::dispatch($ticket->fresh(), "ticket.assigned");
        return new TicketResource($ticket->fresh()->load(["assignee", "team"]));
    }
    public function resolve(Request $request, Ticket $ticket): TicketResource
    {
        $this->authorize("resolve", $ticket);
        $data = $request->validate([
            "resolution" => "required|string|min:10|max:10000",
        ]);
        $old = $ticket->only(["status", "resolution"]);
        $ticket->update([
            "status" => "resuelto",
            "resolution" => $data["resolution"],
            "resolved_at" => now(),
        ]);
        $this->audit->record(
            $ticket,
            $request->user(),
            "ticket.resolved",
            $old,
            $ticket->only(["status", "resolution"]),
        );
        TicketUpdated::dispatch($ticket->fresh(), "ticket.resolved");
        return new TicketResource($ticket->fresh());
    }
    public function close(Request $request, Ticket $ticket): TicketResource
    {
        $this->authorize("resolve", $ticket);
        abort_if(
            blank($ticket->resolution),
            422,
            "No se puede cerrar un ticket sin resolución.",
        );
        $old = ["status" => $ticket->status];
        $ticket->update(["status" => "cerrado", "closed_at" => now()]);
        $this->audit->record($ticket, $request->user(), "ticket.closed", $old, [
            "status" => "cerrado",
        ]);
        TicketUpdated::dispatch($ticket->fresh(), "ticket.closed");
        return new TicketResource($ticket->fresh());
    }
    public function downloadAttachment(Request $request, Attachment $attachment)
    {
        $ticket = Ticket::findOrFail($attachment->ticket_id);
        $this->authorize("view", $ticket);
        abort_unless(
            Storage::disk($attachment->disk)->exists($attachment->path),
            404,
        );
        return Storage::disk($attachment->disk)->download(
            $attachment->path,
            $attachment->original_name,
            [
                "Content-Type" => $attachment->mime_type,
                "X-Content-Type-Options" => "nosniff",
            ],
        );
    }
    public function export(Request $request): StreamedResponse
    {
        $this->authorize("viewAny", Ticket::class);
        $tickets = Ticket::visibleTo($request->user())
            ->with(["requester", "assignee"])
            ->cursor();
        return response()->streamDownload(
            function () use ($tickets) {
                $out = fopen("php://output", "w");
                fputcsv($out, [
                    "Código",
                    "Asunto",
                    "Estado",
                    "Solicitante",
                    "Agente",
                    "Creado",
                ]);
                foreach ($tickets as $ticket) {
                    fputcsv($out, [
                        $ticket->code,
                        $ticket->title,
                        $ticket->status,
                        $ticket->requester->name,
                        $ticket->assignee?->name,
                        $ticket->created_at,
                    ]);
                }
                fclose($out);
            },
            "tickets-" . now()->format("Y-m-d") . ".csv",
            ["Content-Type" => "text/csv"],
        );
    }
    private function nextCode(): string
    {
        $next = ((int) Ticket::lockForUpdate()->max("id")) + 1001;
        return "SUP-" . $next;
    }
}
