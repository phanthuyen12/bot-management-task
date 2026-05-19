<?php
/** @var SergiX44\Nutgram\Nutgram $bot */

use SergiX44\Nutgram\Nutgram;
use App\Http\Controllers\TelegramBotController;
use App\Telegram\Conversations\DailyReportConversation;

/*
|--------------------------------------------------------------------------
| Nutgram Handlers
|--------------------------------------------------------------------------
|
| Here is where you can register telegram handlers for Nutgram. These
| handlers are loaded by the NutgramServiceProvider. Enjoy!
|
*/

// /start — Chào mừng & đăng ký user
$bot->onCommand('start', [TelegramBotController::class, 'start'])
    ->description('Khởi động bot');

// /baocaongay — Bắt đầu hội thoại báo cáo ngày
$bot->onCommand('baocaongay', DailyReportConversation::class)
    ->description('Báo cáo ngày hôm nay');

// Callback cho các nút menu chính
$bot->onCallbackQueryData('menu_daily', [TelegramBotController::class, 'dailyReport']);
$bot->onCallbackQueryData('menu_weekly', [TelegramBotController::class, 'weeklyReport']);
$bot->onCallbackQueryData('menu_monthly', [TelegramBotController::class, 'monthlyReport']);
$bot->onCallbackQueryData('menu_leaderboard', [TelegramBotController::class, 'leaderboard']);
$bot->onCallbackQueryData('menu_info', [TelegramBotController::class, 'info']);

// /leaderboard — Bảng xếp hạng
$bot->onCommand('leaderboard', [TelegramBotController::class, 'leaderboard'])
    ->description('Xem bảng xếp hạng điểm');
