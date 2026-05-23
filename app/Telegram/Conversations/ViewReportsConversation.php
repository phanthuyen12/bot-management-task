<?php

namespace App\Telegram\Conversations;

use App\Models\Report;
use App\Telegram\Concerns\CachesTelegramUser;
use App\Telegram\Concerns\ExitsToHomeMenu;
use App\Telegram\Services\ReportMessageFormatter;
use App\Telegram\TelegramResponse;
use App\Telegram\Views\MessageTemplates;
use SergiX44\Nutgram\Conversations\Conversation;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class ViewReportsConversation extends Conversation
{
    use CachesTelegramUser;
    use ExitsToHomeMenu;

    public function start(Nutgram $bot): void
    {
        $this->setSkipHandlers(true);
        $this->sendTypesMenu($bot);
        $this->next('handleAction');
    }

    public function handleAction(Nutgram $bot): void
    {
        if ($bot->isCallbackQuery()) {
            TelegramResponse::answerCallback($bot);

            $data = $bot->callbackQuery()->data;

            if ($data === 'menu_home') {
                $this->exitToHome($bot);
                return;
            }

            if ($data === 'reports_back_types') {
                $this->sendTypesMenu($bot, edit: true);
                return;
            }

            if (str_starts_with($data, 'reports_type_')) {
                $type = str_replace('reports_type_', '', $data);
                $this->showList($bot, $type, edit: true);
                return;
            }

            if (str_starts_with($data, 'report_view_')) {
                $id = (int) str_replace('report_view_', '', $data);
                $this->showDetail($bot, $id, edit: true);
                return;
            }

            if (str_starts_with($data, 'reports_back_list_')) {
                $type = str_replace('reports_back_list_', '', $data);
                $this->showList($bot, $type, edit: true);
            }

            return;
        }

        if ($this->shouldExitToHome($bot)) {
            $this->exitToHome($bot);
            return;
        }

        $bot->sendMessage(
            "Vui lòng chọn từ menu bên dưới.\n\nGõ /start để về menu chính.",
            reply_markup: MessageTemplates::reportTypesKeyboard(),
        );
    }

    protected function sendTypesMenu(Nutgram $bot, bool $edit = false): void
    {
        $text = "📋 Xem báo cáo của bạn\n\nChọn loại báo cáo:";
        $this->sendOrEdit($bot, $text, MessageTemplates::reportTypesKeyboard(), $edit);
    }

    protected function showList(Nutgram $bot, string $type, bool $edit = false): void
    {
        $user = $this->telegramUser($bot);

        if (!$user) {
            $bot->sendMessage('Bạn chưa đăng ký. Gõ /start để bắt đầu.');
            $this->end();
            return;
        }

        $reports = Report::query()
            ->where('telegram_user_id', $user->id)
            ->where('type', $type)
            ->orderByDesc('date')
            ->limit(8)
            ->get(['id', 'type', 'date', 'points_earned']);

        $label = ReportMessageFormatter::typeLabel($type);

        if ($reports->isEmpty()) {
            $keyboard = InlineKeyboardMarkup::make()
                ->addRow(InlineKeyboardButton::make('◀️ Loại báo cáo', callback_data: 'reports_back_types'))
                ->addRow(InlineKeyboardButton::make('🏠 Menu chính', callback_data: 'menu_home'));

            $this->sendOrEdit($bot, "📋 {$label}\n\nBạn chưa có báo cáo nào.", $keyboard, $edit);
            return;
        }

        $lines = ["📋 {$label}", 'Chọn báo cáo để xem chi tiết:'];
        $keyboard = InlineKeyboardMarkup::make();

        foreach ($reports as $report) {
            $date = $report->date?->format('d/m') ?? '?';
            $keyboard->addRow(
                InlineKeyboardButton::make("📄 {$date}", callback_data: "report_view_{$report->id}")
            );
        }
        $keyboard->addRow(
            InlineKeyboardButton::make('◀️ Loại báo cáo', callback_data: 'reports_back_types'),
        )->addRow(
            InlineKeyboardButton::make('🏠 Menu chính', callback_data: 'menu_home'),
        );

        $this->sendOrEdit($bot, implode("\n", $lines), $keyboard, $edit);
    }

    protected function showDetail(Nutgram $bot, int $reportId, bool $edit = false): void
    {
        $user = $this->telegramUser($bot);

        $report = Report::query()
            ->where('id', $reportId)
            ->where('telegram_user_id', $user?->id)
            ->first();

        if (!$report) {
            $bot->sendMessage('❌ Không tìm thấy báo cáo.');
            return;
        }

        $keyboard = InlineKeyboardMarkup::make()->addRow(
            InlineKeyboardButton::make('◀️ Danh sách', callback_data: "reports_back_list_{$report->type}"),
            InlineKeyboardButton::make('🏠 Menu chính', callback_data: 'menu_home'),
        );

        $this->sendOrEdit($bot, ReportMessageFormatter::detail($report), $keyboard, $edit);
    }

    protected function sendOrEdit(
        Nutgram $bot,
        string $text,
        InlineKeyboardMarkup $keyboard,
        bool $edit,
    ): void {
        $chatId = TelegramResponse::callbackChatId($bot);
        $messageId = TelegramResponse::callbackMessageId($bot);

        if ($edit && $chatId && $messageId) {
            $bot->editMessageText(
                text: $text,
                chat_id: $chatId,
                message_id: $messageId,
                reply_markup: $keyboard,
            );
            return;
        }

        $bot->sendMessage(text: $text, reply_markup: $keyboard);
    }
}
