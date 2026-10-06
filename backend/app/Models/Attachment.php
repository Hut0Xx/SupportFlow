<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Attachment extends Model { protected $fillable=['ticket_id','comment_id','uploaded_by','disk','path','original_name','mime_type','size','sha256']; protected $hidden=['path','disk']; }

