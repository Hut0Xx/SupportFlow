<?php

use App\Models\Role;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;

function acceptanceUserWithRole(string $slug, array $attributes = []): User
{
    $role = Role::firstOrCreate(['slug' => $slug], ['name' => ucfirst($slug)]);
    $user = User::factory()->create($attributes);
    $user->roles()->attach($role);
    return $user;
}

it('autentica con Sanctum usando credenciales válidas', function () {
    $user = User::factory()->create(['email' => 'demo@example.test', 'password' => Hash::make('valid-password')]);
    $this->postJson('/api/v1/auth/login', ['email' => $user->email, 'password' => 'valid-password'])
        ->assertOk()->assertJsonStructure(['user', 'token']);
});

it('crea un ticket con código, SLA y auditoría', function () {
    $requester = acceptanceUserWithRole('requester');
    $fixture = Ticket::factory()->create(['organization_id' => $requester->organization_id, 'requester_id' => $requester->id]);
    Sanctum::actingAs($requester);
    $response = $this->postJson('/api/v1/tickets', ['title' => 'No puedo acceder al panel', 'description' => 'La pantalla muestra un error al acceder desde esta mañana.', 'category_id' => $fixture->category_id, 'priority_id' => $fixture->priority_id]);
    $response->assertCreated()->assertJsonPath('data.status', 'nuevo');
    $this->assertDatabaseHas('audit_events', ['action' => 'ticket.created', 'actor_id' => $requester->id]);
});

it('permite que un agente asigne y responda un ticket', function () {
    $agent = acceptanceUserWithRole('agent');
    $assignee = acceptanceUserWithRole('agent', ['organization_id' => $agent->organization_id]);
    $ticket = Ticket::factory()->create(['organization_id' => $agent->organization_id]);
    Sanctum::actingAs($agent);
    $this->postJson('/api/v1/tickets/'.$ticket->id.'/assign', ['assignee_id' => $assignee->id])->assertOk();
    $this->postJson('/api/v1/tickets/'.$ticket->id.'/comments', ['body' => 'Estamos revisando tu incidencia.'])->assertCreated();
    expect($ticket->fresh()->assignee_id)->toBe($assignee->id)->and($ticket->fresh()->first_response_at)->not->toBeNull();
});

it('calcula el cumplimiento SLA desde tickets resueltos reales', function () {
    $admin = acceptanceUserWithRole('admin');
    Ticket::factory()->create(['organization_id' => $admin->organization_id, 'status' => 'resuelto', 'resolved_at' => now()->subHour(), 'sla_due_at' => now()->addHour()]);
    Ticket::factory()->create(['organization_id' => $admin->organization_id, 'status' => 'resuelto', 'resolved_at' => now(), 'sla_due_at' => now()->subHour()]);
    Sanctum::actingAs($admin);
    $this->getJson('/api/v1/dashboard')->assertOk()->assertJsonPath('sla_compliance', 50);
});

