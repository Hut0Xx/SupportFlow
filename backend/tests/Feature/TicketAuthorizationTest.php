<?php
use App\Models\Ticket;
use App\Models\Role;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

function userWithRole(string $slug): User
{
    $role = Role::firstOrCreate(["slug" => $slug], ["name" => ucfirst($slug)]);
    $user = User::factory()->create();
    $user->roles()->attach($role);
    return $user;
}
it("impide que un solicitante vea tickets ajenos", function () {
    $requester = userWithRole("requester");
    $other = User::factory()->create([
        "organization_id" => $requester->organization_id,
    ]);
    $ticket = Ticket::factory()->create([
        "organization_id" => $requester->organization_id,
        "requester_id" => $other->id,
    ]);
    Sanctum::actingAs($requester);
    $this->getJson("/api/v1/tickets/" . $ticket->id)->assertForbidden();
});
it("impide que un agente elimine tickets", function () {
    $agent = userWithRole("agent");
    $ticket = Ticket::factory()->create([
        "organization_id" => $agent->organization_id,
    ]);
    Sanctum::actingAs($agent);
    $this->deleteJson("/api/v1/tickets/" . $ticket->id)->assertForbidden();
});
it("no cierra un ticket sin resolución", function () {
    $agent = userWithRole("agent");
    $ticket = Ticket::factory()->create([
        "organization_id" => $agent->organization_id,
        "resolution" => null,
    ]);
    Sanctum::actingAs($agent);
    $this->postJson(
        "/api/v1/tickets/" . $ticket->id . "/close",
    )->assertUnprocessable();
});
it("registra en auditoría una resolución", function () {
    $agent = userWithRole("agent");
    $ticket = Ticket::factory()->create([
        "organization_id" => $agent->organization_id,
    ]);
    Sanctum::actingAs($agent);
    $this->postJson("/api/v1/tickets/" . $ticket->id . "/resolve", [
        "resolution" => "Se aplicó y verificó una solución completa.",
    ])->assertOk();
    $this->assertDatabaseHas("audit_events", [
        "ticket_id" => $ticket->id,
        "action" => "ticket.resolved",
    ]);
});
