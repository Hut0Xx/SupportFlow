<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
class DashboardController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $base = Ticket::visibleTo($request->user());
        $resolved = (clone $base)->whereNotNull("resolved_at");
        $totalResolved = (clone $resolved)->count();
        $slaMet = (clone $resolved)
            ->whereColumn("resolved_at", "<=", "sla_due_at")
            ->count();
        return response()->json([
            "tickets_by_status" => (clone $base)
                ->selectRaw("status, count(*) as total")
                ->groupBy("status")
                ->pluck("total", "status"),
            "average_first_response_minutes" =>
                (float) ((clone $base)
                    ->whereNotNull("first_response_at")
                    ->selectRaw(
                        "avg(extract(epoch from (first_response_at-created_at))/60) value",
                    )
                    ->value("value") ?? 0),
            "average_resolution_hours" =>
                (float) ((clone $resolved)
                    ->selectRaw(
                        "avg(extract(epoch from (resolved_at-created_at))/3600) value",
                    )
                    ->value("value") ?? 0),
            "sla_compliance" => $totalResolved
                ? round(($slaMet / $totalResolved) * 100, 1)
                : 100,
            "workload" => (clone $base)
                ->whereNotNull("assignee_id")
                ->whereNotIn("status", ["resuelto", "cerrado"])
                ->selectRaw("assignee_id, count(*) as total")
                ->groupBy("assignee_id")
                ->with("assignee:id,name")
                ->get(),
        ]);
    }
}
