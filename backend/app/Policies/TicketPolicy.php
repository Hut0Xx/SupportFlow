<?php
namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->active;
    }
    public function view(User $user, Ticket $ticket): bool
    {
        return $user->organization_id === $ticket->organization_id &&
            (!$user->hasRole("requester") ||
                $ticket->requester_id === $user->id);
    }
    public function create(User $user): bool
    {
        return $user->active;
    }
    public function update(User $user, Ticket $ticket): bool
    {
        return $user->organization_id === $ticket->organization_id &&
            ($user->hasRole("agent") || $ticket->requester_id === $user->id);
    }
    public function assign(User $user, Ticket $ticket): bool
    {
        return $user->organization_id === $ticket->organization_id &&
            $user->hasRole("agent");
    }
    public function addInternalNote(User $user, Ticket $ticket): bool
    {
        return $this->assign($user, $ticket);
    }
    public function resolve(User $user, Ticket $ticket): bool
    {
        return $this->assign($user, $ticket);
    }
}
