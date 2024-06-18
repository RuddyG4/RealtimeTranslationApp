<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMember extends Model
{
    use HasFactory;
    
    public $timestamps = false;

    protected $fillable = [
        'chat_id',
        'user_id',
        'last_activity',
        'added_at',
    ];
}
