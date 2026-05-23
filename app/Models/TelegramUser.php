<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany as HasManyRelation;

class TelegramUser extends Model
{
    protected $fillable = [
        'telegram_id', 'username', 'first_name', 'last_name', 
        'team_id', 'streak_count', 'best_streak', 'points', 'last_report_date'
    ];

    protected $casts = [
        'last_report_date' => 'date',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function ledTeams(): HasManyRelation
    {
        return $this->hasMany(Team::class, 'team_leader_telegram_user_id');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function displayName(): string
    {
        $fullName = trim(implode(' ', array_filter([$this->first_name, $this->last_name])));

        if ($fullName !== '') {
            return $fullName;
        }

        if (!empty($this->username)) {
            return '@' . $this->username;
        }

        return 'Thành viên #' . $this->id;
    }

    public function badges(): BelongsToMany
    {
        return $this->belongsToMany(Badge::class, 'user_badges')->withTimestamps();
    }
}
