<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'team_leader_telegram_user_id'];

    public function users(): HasMany
    {
        return $this->hasMany(TelegramUser::class);
    }

    public function leader(): BelongsTo
    {
        return $this->belongsTo(TelegramUser::class, 'team_leader_telegram_user_id');
    }

    public function kpis(): HasMany
    {
        return $this->hasMany(Kpi::class);
    }
}
