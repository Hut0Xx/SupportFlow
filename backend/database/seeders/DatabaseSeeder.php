<?php
namespace Database\Seeders;

use App\Models\Category;
use App\Models\Priority;
use App\Models\Role;
use App\Models\SlaRule;
use App\Models\Team;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $org = DB::table("organizations")->insertGetId([
            "name" => "Lumen Norte",
            "slug" => "lumen-norte",
            "timezone" => "Europe/Madrid",
            "created_at" => now(),
            "updated_at" => now(),
        ]);
        $roles = collect([
            ["name" => "Solicitante", "slug" => "requester"],
            ["name" => "Agente", "slug" => "agent"],
            ["name" => "Administrador", "slug" => "admin"],
        ])->mapWithKeys(fn($r) => [$r["slug"] => Role::create($r)]);
        $password = Hash::make(env("DEMO_PASSWORD", "SupportFlow2026!"));
        $requester = User::create([
            "organization_id" => $org,
            "name" => "Laura Martín",
            "email" => "solicitante@supportflow.local",
            "password" => $password,
            "email_verified_at" => now(),
        ]);
        $requester->roles()->attach($roles["requester"]);
        $agent = User::create([
            "organization_id" => $org,
            "name" => "Marta Ruiz",
            "email" => "agente@supportflow.local",
            "password" => $password,
            "email_verified_at" => now(),
        ]);
        $agent->roles()->attach($roles["agent"]);
        $admin = User::create([
            "organization_id" => $org,
            "name" => "Hugo Coarasa",
            "email" => "admin@supportflow.local",
            "password" => $password,
            "email_verified_at" => now(),
        ]);
        $admin->roles()->attach($roles["admin"]);
        $team = Team::create([
            "organization_id" => $org,
            "name" => "Integraciones",
            "slug" => "integraciones",
        ]);
        $team->users()->attach($agent, ["is_lead" => true]);
        $categories = collect([
            "Integraciones",
            "Cuenta",
            "Datos",
            "Facturación",
            "Seguridad",
            "Rendimiento",
        ])->map(
            fn($name) => Category::create([
                "organization_id" => $org,
                "name" => $name,
                "slug" => str($name)->slug(),
            ]),
        );
        $priorities = collect([
            ["Baja", "baja", 1, "#64748b", 1440, 4320],
            ["Media", "media", 2, "#1677ff", 480, 1440],
            ["Alta", "alta", 3, "#f97316", 120, 480],
            ["Urgente", "urgente", 4, "#e11d48", 60, 240],
        ])->map(function ($p) use ($org) {
            $priority = Priority::create([
                "organization_id" => $org,
                "name" => $p[0],
                "slug" => $p[1],
                "level" => $p[2],
                "color" => $p[3],
            ]);
            SlaRule::create([
                "organization_id" => $org,
                "priority_id" => $priority->id,
                "name" => "SLA " . $p[0],
                "first_response_minutes" => $p[4],
                "resolution_minutes" => $p[5],
            ]);
            return $priority;
        });
        foreach (range(1001, 1048) as $i) {
            $created = now()->subHours(random_int(1, 240));
            $status = fake()->randomElement([
                "nuevo",
                "abierto",
                "abierto",
                "pendiente",
                "resuelto",
                "cerrado",
            ]);
            $priority = $priorities->random();
            Ticket::create([
                "organization_id" => $org,
                "code" => "SUP-" . $i,
                "requester_id" => $requester->id,
                "category_id" => $categories->random()->id,
                "priority_id" => $priority->id,
                "team_id" => $team->id,
                "assignee_id" => $status === "nuevo" ? null : $agent->id,
                "title" => fake()->randomElement([
                    "El webhook de Stripe devuelve 401 desde las 09:15",
                    "La invitación a usuarios caduca antes de 24 horas",
                    "El CSV de marzo no incluye la columna CIF",
                    "La factura 2026-091 aparece cobrada dos veces",
                    "Activar SAML para el dominio ventas.lumen.test",
                    "El informe semanal agota el tiempo a los 30 segundos",
                ]),
                "description" => fake()->paragraphs(2, true),
                "status" => $status,
                "resolution" => in_array($status, ["resuelto", "cerrado"])
                    ? "Se corrigió la configuración y se verificó con el solicitante."
                    : null,
                "first_response_at" =>
                    $status === "nuevo"
                        ? null
                        : $created->copy()->addMinutes(random_int(10, 120)),
                "resolved_at" => in_array($status, ["resuelto", "cerrado"])
                    ? $created->copy()->addHours(random_int(2, 20))
                    : null,
                "closed_at" =>
                    $status === "cerrado"
                        ? $created->copy()->addHours(random_int(20, 48))
                        : null,
                "sla_due_at" => $created
                    ->copy()
                    ->addMinutes(
                        SlaRule::where("priority_id", $priority->id)->value(
                            "resolution_minutes",
                        ),
                    ),
                "created_at" => $created,
                "updated_at" => $created
                    ->copy()
                    ->addMinutes(random_int(20, 500)),
            ]);
        }
    }
}
