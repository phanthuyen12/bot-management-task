<?php

namespace App\Telegram\Conversations;

use SergiX44\Nutgram\Conversations\Conversation;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use App\Models\TelegramUser;
use App\Models\Team;
use App\Telegram\Concerns\ExitsToHomeMenu;
use App\Telegram\TelegramResponse;
use App\Telegram\Views\MessageTemplates;

class TeamRegistrationConversation extends Conversation
{
    use ExitsToHomeMenu;

    /**
     * Bắt đầu hội thoại - Hiển thị danh sách team
     */
    public function start(Nutgram $bot): void
    {
        $this->setSkipHandlers(true);

        $teams = Team::all();

        if ($teams->isEmpty()) {
            $bot->sendMessage(
                text: "⚠️ Hiện chưa có team nào. Vui lòng liên hệ Admin để tạo team.",
            );
            $this->end();
            return;
        }

        $keyboard = InlineKeyboardMarkup::make();
        foreach ($teams as $team) {
            $keyboard->addRow(
                InlineKeyboardButton::make(
                    "👥 {$team->name}",
                    callback_data: "join_team_{$team->id}"
                )
            );
        }
        $keyboard->addRow(
            InlineKeyboardButton::make('❌ Huỷ', callback_data: 'join_team_cancel')
        );

        $bot->sendMessage(
            text: MessageTemplates::teamRegistrationPrompt(),
            reply_markup: $keyboard,
        );

        $this->next('confirmTeam');
    }

    /**
     * Xử lý lựa chọn team
     */
    public function confirmTeam(Nutgram $bot): void
    {
        if ($bot->isCallbackQuery()) {
            TelegramResponse::answerCallback($bot);
        }

        if (!$bot->isCallbackQuery()) {
            if ($this->shouldExitToHome($bot)) {
                $this->exitToHome($bot);
                return;
            }
            $bot->sendMessage('Vui lòng chọn team từ danh sách phía trên. Gõ /start để về menu chính.');
            return;
        }

        $data = $bot->callbackQuery()->data;

        if ($data === 'menu_home') {
            $this->exitToHome($bot);
            return;
        }

        if ($data === 'join_team_cancel') {
            $bot->sendMessage("✅ Đã huỷ đăng ký team.");
            $this->end();
            return;
        }

        $teamId = str_replace('join_team_', '', $data);
        $team = Team::find($teamId);

        if (!$team) {
            $bot->sendMessage("❌ Team không tồn tại. Vui lòng thử lại.");
            $this->end();
            return;
        }

        $telegramUser = TelegramUser::where('telegram_id', $bot->user()->id)->first();

        if (!$telegramUser) {
            $telegramUser = TelegramUser::create([
                'telegram_id' => $bot->user()->id,
                'username'    => $bot->user()->username,
                'first_name'  => $bot->user()->first_name,
                'last_name'   => $bot->user()->last_name,
            ]);
        }

        $telegramUser->update(['team_id' => $team->id]);

        $bot->sendMessage(
            text: "🎉 Bạn đã tham gia team {$team->name} thành công!\n\nBây giờ bạn có thể bắt đầu báo cáo.",
        );

        $this->end();
    }
}
