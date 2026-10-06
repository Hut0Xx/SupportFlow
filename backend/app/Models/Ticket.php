<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    use HasFactory;
    protected $fillable = [
        "organization_id",
        "code",
        "requester_id",
        "category_id",
        "priority_id",
        "team_id",
        "assignee_id",
        "title",
        "description",
        "status",
        "resolution",
        "first_response_at",
        "resolved_at",
        "closed_at",
        "sla_due_at",
    ];
    protected function casts(): array
    {
        return [
            "first_response_at" => "datetime",
            "resolved_at" => "datetime",
            "closed_at" => "datetime",
            "sla_due_at" => "datetime",
        ];
    }
    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, "requester_id");
    }
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, "assignee_id");
    }
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    public function priority(): BelongsTo
    {
        return $this->belongsTo(Priority::class);
    }
    public function comments(): HasMany
    {
        return $this->hasMany(TicketComment::class);
    }
    public function audits(): HasMany
    {
        return $this->hasMany(AuditEvent::class);
    }
    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        return $user->hasRole("requester")
            ? $query->where("requester_id", $user->id)
            : $query->where("organization_id", $user->organization_id);
    }
}
