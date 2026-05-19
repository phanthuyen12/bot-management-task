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
            \App\Telegram\Views\MessageTemplates::welcome($user),
            [
                'reply_markup' => \App\Telegram\Views\MessageTemplates::homeKeyboard(),
                'parse_mode' => 'Markdown'
            ]
        );
    }

    /**
     * Báo cáo ngày (/baocaongay).
     */
    public function dailyReport(Nutgram $bot)
    {
        $user = $bot->user();
        $telegramUser = TelegramUser::where('telegram_id', $user->id)->first();

        if (!$telegramUser || !$telegramUser->team_id) {
            $bot->sendMessage("Bạn chưa được phân vào team nào. Vui lòng liên hệ admin.");
            return;
        }

        $bot->sendMessage("Bắt đầu báo cáo ngày! Hãy nhập 3 việc đã làm xong hôm nay:");
        
        // Gợi ý: Sử dụng Nutgram Conversation để xử lý luồng câu hỏi
    }

    /**
     * Tổng kết tuần (Gửi vào thứ 6).
     */
    public function weeklyReport(Nutgram $bot)
    {
        $bot->sendMessage("Bắt đầu báo cáo tuần! Hãy nhập KPI tuần của bạn:");
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
        
        $bot->sendMessage($msg, ['parse_mode' => 'Markdown']);
    }
}
