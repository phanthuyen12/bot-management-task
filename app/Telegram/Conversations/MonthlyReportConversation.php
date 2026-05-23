<?php

namespace App\Telegram\Conversations;

use App\Models\Report;
use App\Services\ReportService;
use App\Telegram\Concerns\CachesTelegramUser;
use App\Telegram\Concerns\ExitsToHomeMenu;
use App\Telegram\Concerns\PromptsForceReply;
use App\Telegram\Views\MessageTemplates;
use Carbon\Carbon;
use SergiX44\Nutgram\Conversations\Conversation;
use SergiX44\Nutgram\Nutgram;
use Throwable;

class MonthlyReportConversation extends Conversation
{
    use CachesTelegramUser;
    use ExitsToHomeMenu;
    use PromptsForceReply;

    protected string $highlights = '';
    protected string $achievements = '';
    protected ?string $next_goals = null;

    public function start(Nutgram $bot): void
    {
        $this->setSkipHandlers(true);

        $this->sendForceReplyPrompt(
            $bot,
            MessageTemplates::monthlyReportPrompt()."\n\nBấm «Trả lời» trên tin nhắn này để nhập.",
            'Điểm nổi bật…',
        );
        $this->next('askHighlights');
    }

    public function askHighlights(Nutgram $bot): void
    {
        if ($this->shouldExitToHome($bot)) {
            $this->exitToHome($bot);
            return;
        }

        $text = $this->acceptForceReplyText($bot, MessageTemplates::monthlyReportPrompt());
        if ($text === null) {
            return;
        }

        $this->highlights = $text;

        $this->sendForceReplyPrompt(
            $bot,
            "Bước 2: Thành tựu quan trọng nhất trong tháng?\n(Bấm «Trả lời» để nhập)",
            'Thành tựu…',
        );
        $this->next('askAchievements');
    }

    public function askAchievements(Nutgram $bot): void
    {
        if ($this->shouldExitToHome($bot)) {
            $this->exitToHome($bot);
            return;
        }

        $text = $this->acceptForceReplyText(
            $bot,
            "Bước 2: Thành tựu quan trọng nhất trong tháng?\n(Bấm «Trả lời» để nhập)",
        );
        if ($text === null) {
            return;
        }

        $this->achievements = $text;

        $this->sendForceReplyPrompt(
            $bot,
            "Bước 3: Mục tiêu tháng tới?\n(Nhập text hoặc /skip — bấm «Trả lời»)",
            'Mục tiêu hoặc /skip',
        );
        $this->next('askNextGoals');
    }

    public function askNextGoals(Nutgram $bot): void
    {
        if ($this->shouldExitToHome($bot)) {
            $this->exitToHome($bot);
            return;
        }

        $text = $this->acceptForceReplyText(
            $bot,
            "Bước 3: Mục tiêu tháng tới?\n(Nhập text hoặc /skip — bấm «Trả lời»)",
        );
        if ($text === null) {
            return;
        }

        $this->next_goals = $text === '/skip' ? null : $text;

        $this->finishReport($bot);
    }

    protected function finishReport(Nutgram $bot): void
    {
        $user = $this->telegramUser($bot);

        if (!$user) {
            $bot->sendMessage('❌ Không tìm thấy tài khoản. Gõ /start để đăng ký.');
            $this->end();
            return;
        }

        try {
            $report = Report::updateOrCreate(
                [
                    'telegram_user_id' => $user->id,
                    'type' => 'monthly',
                    'date' => Carbon::now()->startOfMonth()->toDateString(),
                ],
                [
                    'data' => [
                        'highlights' => $this->highlights,
                        'achievements' => $this->achievements,
                        'next_goals' => $this->next_goals,
                    ],
                    'status' => 'submitted',
                ]
            );

            $points = app(ReportService::class)->calculatePoints($report);
            $report->update(['points_earned' => $points]);
            $user->increment('points', $points);

            $bot->sendMessage("🎉 Đã ghi nhận báo cáo tháng! (+{$points} điểm)");
        } catch (Throwable $e) {
            report($e);
            $bot->sendMessage('❌ Không lưu được báo cáo tháng. Vui lòng thử lại sau.');
        }

        $this->end();
    }
}
