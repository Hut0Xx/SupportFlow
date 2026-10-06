<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Priority extends Model
{
    protected $fillable = [
        "organization_id",
        "name",
        "slug",
        "level",
        "color",
        "active",
    ];
}
