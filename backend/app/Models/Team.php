<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
class Team extends Model
{
    protected $fillable = ["organization_id", "name", "slug", "active"];
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot("is_lead")
            ->withTimestamps();
    }
}
