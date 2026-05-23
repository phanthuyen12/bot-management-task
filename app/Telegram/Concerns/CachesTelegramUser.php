<?php

namespace App\Telegram\Concerns;

use App\Models\TelegramUser;
use SergiX44\Nutgram\Nutgram;

trait CachesTelegramUser
{
    protected ?TelegramUser $cachedTelegramUser = null;

    protected function telegramUser(Nutgram $bot, bool $withTeam = false): ?TelegramUser
    {
        if ($this->cachedTelegramUser !== null) {
            if ($withTeam && !$this->cachedTelegramUser->relationLoaded('team')) {
                $this->cachedTelegramUser->load('team.kpis', 'team.leader', 'team.users');
            }
            return $this->cachedTelegramUser;
        }

        $query = TelegramUser::where('telegram_id', $bot->user()->id);
        if ($withTeam) {
            $query->with('team.kpis', 'team.leader', 'team.users');
        }

        $this->cachedTelegramUser = $query->first();

        return $this->cachedTelegramUser;
    }
}
