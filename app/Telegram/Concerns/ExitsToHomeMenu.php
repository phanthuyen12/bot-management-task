<?php

namespace App\Telegram\Concerns;

use App\Http\Controllers\TelegramBotController;
use SergiX44\Nutgram\Nutgram;

trait ExitsToHomeMenu
{
    protected function shouldExitToHome(Nutgram $bot): bool
    {
        if ($bot->isCallbackQuery()) {
            return false;
        }

        $text = trim($bot->message()?->text ?? '');
        $lower = strtolower($text);

        if ($lower === '/start' || $lower === '/menu') {
            return true;
        }

        return str_starts_with($lower, '/start@') || str_starts_with($lower, '/menu@');
    }

    protected function exitToHome(Nutgram $bot): void
    {
        $this->end();
        app(TelegramBotController::class)->start($bot);
    }
}
