<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    protected $fillable = ['name', 'slug', 'description'];

    public function users(): HasMany
    {
        return $this->hasMany(TelegramUser::class);
    }

    public function kpis(): HasMany
    {
        return $this->hasMany(Kpi::class);
    }
}
