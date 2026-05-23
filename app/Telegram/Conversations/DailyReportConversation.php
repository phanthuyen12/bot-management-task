<?php

namespace App\Telegram\Conversations;

use App\Models\TelegramUser;
use App\Models\Report;
use App\Telegram\Concerns\CachesTelegramUser;
use App\Telegram\Concerns\ExitsToHomeMenu;
use App\Telegram\Concerns\PromptsForceReply;
use App\Telegram\TelegramResponse;
use App\Telegram\Views\MessageTemplates;
use Carbon\Carbon;
use SergiX44\Nutgram\Conversations\Conversation;
use SergiX44\Nutgram\Nutgram;

class DailyReportConversation extends Conversation
{
    use CachesTelegramUser;
    use ExitsToHomeMenu;
    use PromptsForceReply;

    protected $tasks_done;
    protected $stuck_task;
    protected $support_needed = 'support_none';
    protected $support_person = null;
    protected $mood;
    protected $feeling;
    protected $kpi_answers = [];

    public function start(Nutgram $bot): void
    {
        $this->setSkipHandlers(true);

        $this->sendForceReplyPrompt(
            $bot,
            MessageTemplates::dailyReportPrompt(),
            '3 việc đã làm (mỗi dòng một việc)',
        );
        $this->next('askTasksDone');
    }

    public function askTasksDone(Nutgram $bot): void
    {
        if ($this->shouldExitToHome($bot)) {
            $this->exitToHome($bot);
            return;
        }

        $text = $this->acceptForceReplyText(
            $bot,
            MessageTemplates::dailyReportPrompt(),
        );
        if ($text === null) {
            return;
        }

        $this->tasks_done = $text;

        $this->sendForceReplyPrompt(
            $bot,
            "Bước 2: Có việc gì đang vướng?\n(Nhập text hoặc /skip — nhớ bấm «Trả lời»)",
            'Việc vướng hoặc /skip',
        );
        $this->next('askStuckTask');
    }

    public function askStuckTask(Nutgram $bot): void
    {
        if ($this->shouldExitToHome($bot)) {
            $this->exitToHome($bot);
            return;
        }

        $text = $this->acceptForceReplyText(
            $bot,
            "Bước 2: Có việc gì đang vướng?\n(Nhập text hoặc /skip — nhớ bấm «Trả lời»)",
        );
        if ($text === null) {
            return;
        }

        $this->stuck_task = $text === '/skip' ? null : $text;

        $user = $this->telegramUser($bot, true);
        $team = $user?->team;
        $leader = null;
        $peers = [];

        if ($user && $team) {
            $leader = $team->leader && $team->leader->id !== $user->id ? $team->leader : null;

            $peers = $team->users
                ->filter(fn (TelegramUser $member) => $member->id !== $user->id && ($leader === null || $member->id !== $leader->id))
                ->sortBy(fn (TelegramUser $member) => mb_strtolower($member->displayName()))
                ->values()
                ->all();
        }

        $bot->sendMessage(
            text: "Bước 3: Bạn cần ai hỗ trợ?\nBấm vào tên để mở Telegram của đồng đội, rồi bấm «Xong» để tiếp tục.",
            reply_markup: MessageTemplates::supportKeyboard($leader, $peers),
        );
        $this->next('askSupport');
    }

    public function askSupport(Nutgram $bot): void
    {
        if (!$bot->isCallbackQuery()) {
            if ($this->shouldExitToHome($bot)) {
                $this->exitToHome($bot);
                return;
            }
            $bot->sendMessage('Vui lòng chọn nút bên dưới. Gõ /start để về menu.');
            return;
        }

        TelegramResponse::answerCallback($bot);

        $supportData = $bot->callbackQuery()->data;
        $this->support_needed = $supportData;
        $this->support_person = null;

        if (str_starts_with($supportData, 'support_user_')) {
            $selectedId = (int) str_replace('support_user_', '', $supportData);
            $currentUser = $this->telegramUser($bot, true);
            $team = $currentUser?->team;

            $selectedUser = $team?->users->firstWhere('id', $selectedId);
            if (!$selectedUser && $team?->leader?->id === $selectedId) {
                $selectedUser = $team->leader;
            }

            if (!$selectedUser) {
                $bot->sendMessage('Người hỗ trợ không hợp lệ hoặc không còn trong team. Vui lòng chọn lại.');
                $leader = $team?->leader && $team->leader->id !== $currentUser?->id ? $team->leader : null;
                $peers = $team
                    ? $team->users
                        ->filter(fn (TelegramUser $member) => $member->id !== $currentUser?->id && ($leader === null || $member->id !== $leader->id))
                        ->sortBy(fn (TelegramUser $member) => mb_strtolower($member->displayName()))
                        ->values()
                        ->all()
                    : [];

                $bot->sendMessage(
                    text: "Bước 3: Bạn cần ai hỗ trợ?\nBấm vào tên để mở Telegram của đồng đội, rồi bấm «Xong» để tiếp tục.",
                    reply_markup: MessageTemplates::supportKeyboard($leader, $peers),
                );
                return;
            }

            $this->support_needed = $team?->leader?->id === $selectedUser->id ? 'support_lead' : 'support_peer';
            $this->support_person = [
                'id' => $selectedUser->id,
                'telegram_id' => $selectedUser->telegram_id,
                'name' => $selectedUser->displayName(),
                'username' => $selectedUser->username,
                'role' => $this->support_needed === 'support_lead' ? 'lead' : 'peer',
            ];
        }

        $bot->sendMessage(
            text: 'Bước 4: Tâm trạng hôm nay?',
            reply_markup: MessageTemplates::moodKeyboard(),
        );
        $this->next('askMood');
    }

    public function askMood(Nutgram $bot): void
    {
        if (!$bot->isCallbackQuery()) {
            if ($this->shouldExitToHome($bot)) {
                $this->exitToHome($bot);
                return;
            }
            $bot->sendMessage('Vui lòng chọn số sao. Gõ /start để về menu.');
            return;
        }

        TelegramResponse::answerCallback($bot);

        $this->mood = str_replace('mood_', '', $bot->callbackQuery()->data);

        $this->sendForceReplyPrompt(
            $bot,
            "Bước 5: Một dòng cảm xúc (dưới 100 ký tự).\nBấm «Trả lời» trên tin nhắn này.",
            'Cảm xúc hôm nay…',
        );
        $this->next('askFeeling');
    }

    public function askFeeling(Nutgram $bot): void
    {
        if ($this->shouldExitToHome($bot)) {
            $this->exitToHome($bot);
            return;
        }

        $text = $this->acceptForceReplyText(
            $bot,
            "Bước 5: Một dòng cảm xúc (dưới 100 ký tự).\nBấm «Trả lời» trên tin nhắn này.",
        );
        if ($text === null) {
            return;
        }

        $this->feeling = $text;

        $user = $this->telegramUser($bot, true);
        $team = $user?->team;

        if ($team && $team->kpis->count() > 0) {
            $firstKpi = $team->kpis->first();
            $this->sendForceReplyPrompt(
                $bot,
                MessageTemplates::kpiPrompt($firstKpi->name, $firstKpi->question_text),
                'Nhập KPI…',
            );
            $bot->setUserData('current_kpi_index', 0);
            $this->next('askKpis');
        } else {
            $this->finishReport($bot);
        }
    }

    public function askKpis(Nutgram $bot): void
    {
        if ($this->shouldExitToHome($bot)) {
            $this->exitToHome($bot);
            return;
        }

        $user = $this->telegramUser($bot, true);
        $team = $user?->team;
        if (!$team || $team->kpis->isEmpty()) {
            $this->finishReport($bot);
            return;
        }

        $currentIndex = (int) $bot->getUserData('current_kpi_index', null, 0);
        $kpis = $team->kpis;
        $currentKpi = $kpis[$currentIndex];

        $text = $this->acceptForceReplyText(
            $bot,
            MessageTemplates::kpiPrompt($currentKpi->name, $currentKpi->question_text),
        );
        if ($text === null) {
            return;
        }

        $this->kpi_answers[$currentKpi->name] = $text;

        $nextIndex = $currentIndex + 1;

        if ($nextIndex < $kpis->count()) {
            $nextKpi = $kpis[$nextIndex];
            $this->sendForceReplyPrompt(
                $bot,
                MessageTemplates::kpiPrompt($nextKpi->name, $nextKpi->question_text),
                'Nhập KPI…',
            );
            $bot->setUserData('current_kpi_index', $nextIndex);
            $this->next('askKpis');
        } else {
            $this->finishReport($bot);
        }
    }

    protected function finishReport(Nutgram $bot): void
    {
        $user = $this->telegramUser($bot);

        if (!$user) {
            $bot->sendMessage('❌ Không tìm thấy tài khoản. Gõ /start.');
            $this->end();
            return;
        }

        Report::updateOrCreate(
            [
                'telegram_user_id' => $user->id,
                'type' => 'daily',
                'date' => Carbon::today(),
            ],
            [
                'data' => [
                    'tasks_done' => $this->tasks_done,
                    'stuck_task' => $this->stuck_task,
                    'support_needed' => $this->support_needed,
                    'support_person' => $this->support_person,
                    'mood' => $this->mood,
                    'feeling' => $this->feeling,
                    'kpis' => $this->kpi_answers,
                ],
                'points_earned' => 10,
                'status' => 'submitted',
            ]
        );

        $bot->sendMessage('🎉 Cảm ơn bạn đã hoàn thành báo cáo ngày! Bé đã ghi nhận.');
        $this->end();
    }
}
