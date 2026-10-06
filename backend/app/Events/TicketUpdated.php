<?php
namespace App\Events;
use App\Models\Ticket;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
class TicketUpdated implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;
    public function __construct(public Ticket $ticket, public string $action) {}
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel(
                "organizations." . $this->ticket->organization_id,
            ),
            new PrivateChannel("tickets." . $this->ticket->id),
        ];
    }
    public function broadcastAs(): string
    {
        return "ticket.updated";
    }
    public function broadcastWith(): array
    {
        return [
            "ticket_id" => $this->ticket->id,
            "code" => $this->ticket->code,
            "status" => $this->ticket->status,
            "action" => $this->action,
            "updated_at" => $this->ticket->updated_at,
        ];
    }
}
