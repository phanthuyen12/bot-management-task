<?php

namespace App\Telegram;

use SergiX44\Nutgram\Nutgram;

class TelegramResponse
{
    /**
     * Trả lời callback ngay để Telegram tắt loading trên nút bấm.
     */
    public static function answerCallback(Nutgram $bot, ?string $text = null): void
    {
        if ($bot->isCallbackQuery()) {
            $bot->answerCallbackQuery(
                text: $text,
                cache_time: $text ? 3 : 0,
            );
        }
    }

    public static function callbackMessageId(Nutgram $bot): ?int
    {
        return $bot->callbackQuery()?->message?->message_id;
    }

    public static function callbackChatId(Nutgram $bot): ?int
    {
        return $bot->callbackQuery()?->message?->chat?->id;
    }
}
