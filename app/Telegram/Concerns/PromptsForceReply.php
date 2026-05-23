<?php

namespace App\Telegram\Concerns;

use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ForceReply;
use SergiX44\Nutgram\Telegram\Types\Message\Message;

trait PromptsForceReply
{
    protected ?int $expectedReplyMessageId = null;

    /**
     * Gửi câu hỏi kèm ForceReply — Telegram hiện ô «Trả lời» / «Huỷ trả lời».
     */
    protected function sendForceReplyPrompt(
        Nutgram $bot,
        string $text,
        ?string $placeholder = null,
    ): void {
        $sent = $bot->sendMessage(
            text: $text,
            reply_markup: ForceReply::make(
                force_reply: true,
                input_field_placeholder: $placeholder ?? 'Trả lời tin nhắn này…',
                selective: true,
            ),
        );

        $this->rememberPromptMessageId($bot, $sent);
    }

    protected function rememberPromptMessageId(Nutgram $bot, ?Message $sent): void
    {
        $id = $sent?->message_id;
        $this->expectedReplyMessageId = $id;
        if ($id !== null) {
            $bot->setUserData('prompt_message_id', $id);
        }
    }

    protected function expectedPromptMessageId(Nutgram $bot): ?int
    {
        return $this->expectedReplyMessageId
            ?? $bot->getUserData('prompt_message_id', null, null);
    }

    /**
     * Chấp nhận tin nhắn chỉ khi user Reply đúng câu hỏi bot vừa gửi.
     *
     * @return string|null Nội dung text hợp lệ, null nếu từ chối (đã gửi hướng dẫn).
     */
    protected function acceptForceReplyText(Nutgram $bot, string $repromptHint): ?string
    {
        $message = $bot->message();

        if ($message?->text === null || trim($message->text) === '') {
            $this->sendForceReplyPrompt($bot, $repromptHint);
            return null;
        }

        $text = trim($message->text);
        $expectedId = $this->expectedPromptMessageId($bot);
        $replyToId = $message->reply_to_message?->message_id;

        if ($expectedId === null) {
            return $text;
        }

        if ($replyToId === null) {
            $bot->sendMessage(
                "⚠️ Hãy bấm «Trả lời» trên câu hỏi của bot rồi nhập.\n"
                . "Không gửi tin nhắn mới ngoài luồng.\n"
                . "Muốn thoát: gõ /start",
            );
            return null;
        }

        if ($replyToId !== $expectedId) {
            $bot->sendMessage(
                "⚠️ Bạn đang trả lời sai câu hỏi.\n"
                . "Hãy bấm «Trả lời» đúng tin nhắn gần nhất của bot.",
            );
            return null;
        }

        return $text;
    }
}
