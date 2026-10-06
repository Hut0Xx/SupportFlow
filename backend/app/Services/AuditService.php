<?php
namespace App\Services;
use App\Models\AuditEvent;
use App\Models\Ticket;
use App\Models\User;
class AuditService { public function record(Ticket $ticket, User $actor, string $action, array $old=[], array $new=[]): void { AuditEvent::create(['organization_id'=>$ticket->organization_id,'ticket_id'=>$ticket->id,'actor_id'=>$actor->id,'action'=>$action,'old_values'=>$old ?: null,'new_values'=>$new ?: null,'ip_address'=>request()->ip(),'user_agent'=>mb_substr((string) request()->userAgent(),0,500),'created_at'=>now()]); } }

