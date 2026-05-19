<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Badge extends Model
{
    protected $fillable = ['name', 'code', 'description', 'icon'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(TelegramUser::class, 'user_badges')->withTimestamps();
    }
}
