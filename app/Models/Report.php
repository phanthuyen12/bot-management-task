<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    protected $fillable = ['telegram_user_id', 'type', 'date', 'data', 'points_earned', 'status'];

    protected $casts = [
        'data' => 'array',
        'date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(TelegramUser::class, 'telegram_user_id');
    }
}
