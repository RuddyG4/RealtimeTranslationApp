<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TextMessage extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'message',
        'is_original',
        'language_id',
    ];
}