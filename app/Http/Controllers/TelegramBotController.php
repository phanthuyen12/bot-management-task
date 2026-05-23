<?php

namespace App\Http\Controllers;

use SergiX44\Nutgram\Nutgram;
use App\Models\TelegramUser;
use App\Services\ReportService;
use App\Telegram\ConversationStarter;
use App\Telegram\Conversations\DailyReportConversation;
use App\Telegram\Conversations\MonthlyReportConversation;
use App\Telegram\Conversations\ViewReportsConversation;
use App\Telegram\Conversations\WeeklyReportConversation;
use App\Telegram\TelegramResponse;
use App\Telegram\Views\MessageTemplates;

class TelegramBotController extends Controller
{
    public function __construct(protected ReportService $reportService)
    {
    }

    public function start(Nutgram $bot): void
    {
        TelegramResponse::answerCallback($bot);

        $userId = $bot->userId();
        $chatId = $bot->chatId();
        if ($userId !== null && $chatId !== null) {
            $bot->endConversation($userId, $chatId);
        }

        $user = $bot->user();

        TelegramUser::firstOrCreate(
            ['telegram_id' => $user->id],
            [
                'username' => $user->username,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
            ]
        );

        $bot->sendMessage(
            text: MessageTemplates::welcome($user),
            reply_markup: MessageTemplates::homeKeyboard(),
        );
    }

    public function dailyReport(Nutgram $bot): void
    {
        TelegramResponse::answerCallback($bot);

        if (!$this->requireTeamMember($bot)) {
            return;
        }

        ConversationStarter::beginFresh($bot, DailyReportConversation::class);
    }

    public function weeklyReport(Nutgram $bot): void
    {
        TelegramResponse::answerCallback($bot);

        if (!$this->requireTeamMember($bot)) {
            return;
        }

        ConversationStarter::beginFresh($bot, WeeklyReportConversation::class);
    }

    public function monthlyReport(Nutgram $bot): void
    {
        TelegramResponse::answerCallback($bot);

        if (!$this->requireTeamMember($bot)) {
            return;
        }

        ConversationStarter::beginFresh($bot, MonthlyReportConversation::class);
    }

    public function viewReports(Nutgram $bot): void
    {
        TelegramResponse::answerCallback($bot);

        if (!$this->requireRegisteredUser($bot)) {
            return;
        }

        ConversationStarter::beginFresh($bot, ViewReportsConversation::class);
    }

    public function info(Nutgram $bot): void
    {
        TelegramResponse::answerCallback($bot);

        $telegramUser = $this->requireRegisteredUser($bot);
        if (!$telegramUser) {
            return;
        }

        $teamName = $telegramUser->team
            ? $telegramUser->team->name
            : 'Chưa chọn team — gõ /dangky để đăng ký';

        $username = $telegramUser->username ? "@{$telegramUser->username}" : '—';

        $msg = "👤 Thông tin của bạn:\n";
        $msg .= "Họ tên: {$telegramUser->first_name} {$telegramUser->last_name}\n";
        $msg .= "Username: {$username}\n";
        $msg .= "Team: {$teamName}\n";
        $msg .= "💰 Điểm: {$telegramUser->points}\n";
        $msg .= "🔥 Streak: {$telegramUser->streak_count} ngày\n";

        $bot->sendMessage(text: $msg);
    }

    public function leaderboard(Nutgram $bot): void
    {
        TelegramResponse::answerCallback($bot);

        $topUsers = TelegramUser::orderBy('points', 'desc')->take(10)->get();

        if ($topUsers->isEmpty()) {
            $bot->sendMessage('🏆 Chưa có ai trong bảng xếp hạng. Hãy là người đầu tiên báo cáo!');
            return;
        }

        $msg = "🏆 BẢNG XẾP HẠNG\n\n";
        foreach ($topUsers as $index => $u) {
            $medal = match ($index) {
                0 => '🥇',
                1 => '🥈',
                2 => '🥉',
                default => ($index + 1) . '.',
            };
            $msg .= "{$medal} {$u->first_name}";
            if ($u->username) {
                $msg .= " (@{$u->username})";
            }
            $msg .= "\n└ 💰 {$u->points} điểm | 🔥 {$u->streak_count} ngày streak\n\n";
        }

        $bot->sendMessage(text: $msg);
    }

    protected function requireRegisteredUser(Nutgram $bot): ?TelegramUser
    {
        $telegramUser = TelegramUser::where('telegram_id', $bot->user()->id)->first();

        if (!$telegramUser) {
            $bot->sendMessage('Bạn chưa đăng ký. Hãy gửi /start để bắt đầu.');
            return null;
        }

        return $telegramUser;
    }

    protected function requireTeamMember(Nutgram $bot): bool
    {
        $telegramUser = $this->requireRegisteredUser($bot);

        if (!$telegramUser) {
            return false;
        }

        if (!$telegramUser->team_id) {
            $bot->sendMessage(
                "⚠️ Bạn chưa tham gia team nào!\n\nHãy dùng nút Đăng ký team trong menu hoặc gõ /dangky trước khi báo cáo.",
            );
            return false;
        }

        return true;
    }
}
