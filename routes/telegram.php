<?php
/** @var SergiX44\Nutgram\Nutgram $bot */

use SergiX44\Nutgram\Nutgram;
use App\Http\Controllers\TelegramBotController;
use App\Telegram\ConversationStarter;
use App\Telegram\Conversations\DailyReportConversation;
use App\Telegram\Conversations\MonthlyReportConversation;
use App\Telegram\Conversations\TeamRegistrationConversation;
use App\Telegram\Conversations\ViewReportsConversation;
use App\Telegram\Conversations\WeeklyReportConversation;
use App\Telegram\TelegramResponse;

/*
|--------------------------------------------------------------------------
| Nutgram Handlers
|--------------------------------------------------------------------------
*/

$bot->onCommand('start', [TelegramBotController::class, 'start'])
    ->description('Khởi động bot');

$bot->onCommand('baocaongay', function (Nutgram $bot) {
    app(TelegramBotController::class)->dailyReport($bot);
})->description('Báo cáo ngày hôm nay');

$bot->onCommand('baocaotuan', function (Nutgram $bot) {
    app(TelegramBotController::class)->weeklyReport($bot);
})->description('Báo cáo tuần');

$bot->onCommand('baocaothang', function (Nutgram $bot) {
    app(TelegramBotController::class)->monthlyReport($bot);
})->description('Báo cáo tháng');

$bot->onCommand('xembaocao', [TelegramBotController::class, 'viewReports'])
    ->description('Xem báo cáo đã gửi');

$bot->onCommand('leaderboard', [TelegramBotController::class, 'leaderboard'])
    ->description('Xem bảng xếp hạng điểm');

$bot->onCommand('dangky', function (Nutgram $bot) {
    TelegramResponse::answerCallback($bot);
    ConversationStarter::beginFresh($bot, TeamRegistrationConversation::class);
})->description('Đăng ký team của bạn');

$bot->onCallbackQueryData('menu_home', [TelegramBotController::class, 'start']);

$bot->onCallbackQueryData('menu_register_team', function (Nutgram $bot) {
    TelegramResponse::answerCallback($bot);
    ConversationStarter::beginFresh($bot, TeamRegistrationConversation::class);
});

$bot->onCallbackQueryData('menu_daily', [TelegramBotController::class, 'dailyReport']);
$bot->onCallbackQueryData('menu_weekly', [TelegramBotController::class, 'weeklyReport']);
$bot->onCallbackQueryData('menu_monthly', [TelegramBotController::class, 'monthlyReport']);
$bot->onCallbackQueryData('menu_view_reports', [TelegramBotController::class, 'viewReports']);
$bot->onCallbackQueryData('menu_leaderboard', [TelegramBotController::class, 'leaderboard']);
$bot->onCallbackQueryData('menu_info', [TelegramBotController::class, 'info']);

$bot->fallback(function (Nutgram $bot) {
    $bot->sendMessage(
        text: "❓ Tôi chưa hiểu lệnh này.\n\nGõ /start để xem menu chính.",
    );
});
