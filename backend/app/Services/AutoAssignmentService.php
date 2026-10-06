<?php
namespace App\Services;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\DB;
class AutoAssignmentService
{
    public function assign(Ticket $ticket): void
    {
        $agent = User::where("organization_id", $ticket->organization_id)
            ->where("active", true)
            ->whereHas("roles", fn($q) => $q->where("slug", "agent"))
            ->withCount([
                "ticketsAssigned as active_tickets_count" => fn(
                    $q,
                ) => $q->whereNotIn("status", ["resuelto", "cerrado"]),
            ])
            ->orderBy("active_tickets_count")
            ->orderBy("id")
            ->first();
        if (!$agent) {
            return;
        }
        $teamId = $agent->teams()->value("teams.id");
        $ticket->update([
            "assignee_id" => $agent->id,
            "team_id" => $teamId,
            "status" => "abierto",
        ]);
        DB::table("ticket_assignments")->insert([
            "ticket_id" => $ticket->id,
            "user_id" => $agent->id,
            "team_id" => $teamId,
            "reason" => "auto:least_loaded",
            "created_at" => now(),
        ]);
    }
}
