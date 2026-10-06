<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model; class TicketComment extends Model { protected $fillable=['ticket_id','user_id','body','is_internal']; protected function casts(): array { return ['is_internal'=>'boolean']; } }
