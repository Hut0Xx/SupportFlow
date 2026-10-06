<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model; class SlaRule extends Model { protected $fillable=['organization_id','priority_id','name','first_response_minutes','resolution_minutes','active']; protected function casts(): array { return ['active'=>'boolean']; } }

