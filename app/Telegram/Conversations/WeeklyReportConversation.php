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

class WeeklyReportConversation extends Conversation
{
    use CachesTelegramUser;
    use ExitsToHomeMenu;
    use PromptsForceReply;

    protected string $summary = '';
    protected ?string $challenges = null;
    protected ?string $shoutout = null;

    public function start(Nutgram $bot): void
    {
        $this->setSkipHandlers(true);

        $this->sendForceReplyPrompt(
            $bot,
            MessageTemplates::weeklyReportPrompt()."\n\nBấm «Trả lời» trên tin nhắn này để nhập.",
            'Tóm tắt tuần…',
        );
        $this->next('askSummary');
    }

    public function askSummary(Nutgram $bot): void
    {
        if ($this->shouldExitToHome($bot)) {
            $this->exitToHome($bot);
            return;
        }

        $text = $this->acceptForceReplyText($bot, MessageTemplates::weeklyReportPrompt());
        if ($text === null) {
            return;
        }

        $this->summary = $text;

        $this->sendForceReplyPrompt(
            $bot,
            "Bước 2: Khó khăn lớn nhất trong tuần?\n(Nhập text hoặc /skip — bấm «Trả lời»)",
            'Khó khăn hoặc /skip',
        );
        $this->next('askChallenges');
    }

    public function askChallenges(Nutgram $bot): void
    {
        if ($this->shouldExitToHome($bot)) {
            $this->exitToHome($bot);
            return;
        }

        $text = $this->acceptForceReplyText(
            $bot,
            "Bước 2: Khó khăn lớn nhất trong tuần?\n(Nhập text hoặc /skip — bấm «Trả lời»)",
        );
        if ($text === null) {
            return;
        }

        $this->challenges = $text === '/skip' ? null : $text;

        $this->sendForceReplyPrompt(
            $bot,
            "Bước 3: Ghi nhận đồng đội (shoutout)?\n(Nhập tên hoặc /skip — bấm «Trả lời»)",
            'Shoutout hoặc /skip',
        );
        $this->next('askShoutout');
    }

    public function askShoutout(Nutgram $bot): void
    {
        if ($this->shouldExitToHome($bot)) {
            $this->exitToHome($bot);
            return;
        }

        $text = $this->acceptForceReplyText(
            $bot,
            "Bước 3: Ghi nhận đồng đội (shoutout)?\n(Nhập tên hoặc /skip — bấm «Trả lời»)",
        );
        if ($text === null) {
            return;
        }

        $this->shoutout = $text === '/skip' ? null : $text;

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
                    'type' => 'weekly',
                    'date' => Carbon::now()->startOfWeek()->toDateString(),
                ],
                [
                    'data' => [
                        'summary' => $this->summary,
                        'challenges' => $this->challenges,
                        'shoutout' => $this->shoutout,
                    ],
                    'status' => 'submitted',
                ]
            );

            $points = app(ReportService::class)->calculatePoints($report);
            $report->update(['points_earned' => $points]);
            $user->increment('points', $points);

            $bot->sendMessage("🎉 Đã ghi nhận báo cáo tuần! (+{$points} điểm)");

            try {
                app(ReportService::class)->updateStreak($user->fresh(), $report->fresh());
            } catch (Throwable) {
                //
            }
        } catch (Throwable $e) {
            report($e);
            $bot->sendMessage('❌ Không lưu được báo cáo tuần. Vui lòng thử lại sau.');
        }

        $this->end();
    }
}
