<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $fillable = [
        "organization_id",
        "name",
        "email",
        "password",
        "active",
        "email_verified_at",
    ];
    protected $hidden = ["password", "remember_token"];
    protected function casts(): array
    {
        return [
            "email_verified_at" => "datetime",
            "password" => "hashed",
            "active" => "boolean",
        ];
    }
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class)
            ->withPivot("is_lead")
            ->withTimestamps();
    }
    public function ticketsAssigned(): HasMany
    {
        return $this->hasMany(Ticket::class, "assignee_id");
    }
    public function hasRole(string $slug): bool
    {
        return $this->roles()->where("slug", $slug)->exists();
    }
}
