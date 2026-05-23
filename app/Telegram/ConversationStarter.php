<?php

namespace App\Telegram;

use SergiX44\Nutgram\Conversations\Conversation;
use SergiX44\Nutgram\Nutgram;

class ConversationStarter
{
    /**
     * Kết thúc conversation cũ và bắt đầu conversation mới (tránh xung đột state/cache).
     */
    public static function beginFresh(Nutgram $bot, string $conversationClass): void
    {
        $userId = $bot->userId();
        $chatId = $bot->chatId();

        if ($userId !== null && $chatId !== null) {
            $bot->endConversation($userId, $chatId);
        }

        /** @var class-string<Conversation> $conversationClass */
        $conversationClass::begin($bot, $userId, $chatId);
    }
}
