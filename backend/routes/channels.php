<?php
use App\Models\Ticket;
use Illuminate\Support\Facades\Broadcast;
Broadcast::channel('organizations.{organizationId}',fn($user,$organizationId)=>(int)$user->organization_id===(int)$organizationId);
Broadcast::channel('tickets.{ticketId}',fn($user,$ticketId)=>$user->can('view',Ticket::findOrFail($ticketId)));

