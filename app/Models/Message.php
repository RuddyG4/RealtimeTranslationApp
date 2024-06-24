<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Message extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $touches = ['chat'];

    protected $fillable = [
        'chat_id',
        'user_id',
        'type',
        'sent_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
        ];
    }

    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Retrieve the original text message
     */
    public function originalText(): HasOne
    {
        return $this->hasOne(TextMessage::class)->where('is_original', true);
    }
    
    /**
     * Retrieve the text message transleted to the current user language.
     */
    public function translatedText(): HasOne
    {
        return $this->hasOne(TextMessage::class)->where('language_id', auth()->user()->language_id);
        // return $this->hasOne(TextMessage::class, 'id')->where('is_original', true); // For Development only
    }
    
    public function textMessages(): HasMany
    {
        return $this->hasMany(TextMessage::class);
    }
    
    public function audioMessages(): HasMany
    {
        return $this->hasMany(AudioMessage::class);
    }
    
    public function translatedAudio(): HasOne
    {
        return $this->hasOne(AudioMessage::class)->where('language_id', auth()->user()->language_id);
    }

    /**
     * Retrieve the original audio message
     */
    public function originalAudio(): HasOne
    {
        return $this->hasOne(TextMessage::class)->where('is_original', true);
    }
}
