<?php

namespace App\Http\Controllers;

use SergiX44\Nutgram\Nutgram;
use App\Models\TelegramUser;
use App\Services\ReportService;
use App\Models\Team;

class TelegramBotController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Khởi động bot (Command /start).
     */
    public function start(Nutgram $bot)
    {
        $user = $bot->user();

        $telegramUser = TelegramUser::firstOrCreate(
            ['telegram_id' => $user->id],
            [
                'username' => $user->username,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
            ]
        );

        $bot->sendMessage(
            text: \App\Telegram\Views\MessageTemplates::welcome($user),
            reply_markup: \App\Telegram\Views\MessageTemplates::homeKeyboard(),
            parse_mode: 'Markdown'
        );
    }

    /**
     * Báo cáo ngày (/baocaongay).
     */
    public function dailyReport(Nutgram $bot)
    {
        if ($bot->isCallbackQuery()) {
            $bot->answerCallbackQuery();
        }

        $user = $bot->user();
        $telegramUser = TelegramUser::where('telegram_id', $user->id)->first();

        if (!$telegramUser || !$telegramUser->team_id) {
            $bot->sendMessage("Bạn chưa được phân vào team nào. Vui lòng liên hệ admin.");
            return;
        }

        \App\Telegram\Conversations\DailyReportConversation::begin($bot);
    }

    /**
     * Tổng kết tuần (Gửi vào thứ 6).
     */
    public function weeklyReport(Nutgram $bot)
    {
        if ($bot->isCallbackQuery()) {
            $bot->answerCallbackQuery();
        }

        $bot->sendMessage("Bắt đầu báo cáo tuần! Hãy nhập KPI tuần của bạn:");
    }

    /**
     * Tổng kết tháng.
     */
    public function monthlyReport(Nutgram $bot)
    {
        if ($bot->isCallbackQuery()) {
            $bot->answerCallbackQuery();
        }

        $bot->sendMessage("Bắt đầu báo cáo tháng! Hãy nhập KPI tháng của bạn:");
    }

    /**
     * Thông tin cá nhân.
     */
    public function info(Nutgram $bot)
    {
        if ($bot->isCallbackQuery()) {
            $bot->answerCallbackQuery();
        }

        $user = $bot->user();
        $telegramUser = TelegramUser::where('telegram_id', $user->id)->first();

        if (!$telegramUser) {
            $bot->sendMessage("Bạn chưa đăng ký. Hãy gửi /start để bắt đầu.");
            return;
        }

        $teamName = $telegramUser->team ? $telegramUser->team->name : 'Chưa chọn team';
        $msg = "👤 Thông tin của bạn:\n";
        $msg .= "Họ tên: {$telegramUser->first_name} {$telegramUser->last_name}\n";
        $msg .= "Username: @{$telegramUser->username}\n";
        $msg .= "Team: {$teamName}\n";
        $msg .= "Điểm: {$telegramUser->points}\n";

        $bot->sendMessage(text: $msg);
    }

    /**
     * Bảng xếp hạng (/leaderboard).
     */
    public function leaderboard(Nutgram $bot)
    {
        // Lấy top user theo điểm
        $topUsers = TelegramUser::orderBy('points', 'desc')->take(5)->get();
        
        $msg = "🏆 **Bảng Xếp Hạng** 🏆\n";
        foreach ($topUsers as $index => $u) {
            $msg .= ($index + 1) . ". {$u->first_name} - {$u->points} điểm\n";
        }
        
        $bot->sendMessage(text: $msg, parse_mode: 'Markdown');
    }
}
