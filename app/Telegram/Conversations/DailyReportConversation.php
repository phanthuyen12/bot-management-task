<?php

namespace App\Telegram\Conversations;

use SergiX44\Nutgram\Conversations\Conversation;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ForceReply;
use App\Telegram\Views\MessageTemplates;
use App\Models\TelegramUser;
use App\Models\Report;
use Carbon\Carbon;

class DailyReportConversation extends Conversation
{
    protected $tasks_done;
    protected $stuck_task;
    protected $support_needed;
    protected $mood;
    protected $feeling;
    protected $kpi_answers = [];

    /**
     * Bắt đầu hội thoại - Dùng ForceReply để bắt buộc trả lời
     */
    public function start(Nutgram $bot)
    {
        $bot->sendMessage(
            text: MessageTemplates::dailyReportPrompt(),
            reply_markup: ForceReply::make(true, null, true),
            parse_mode: 'Markdown'
        );
        $this->next('askTasksDone');
    }

    /**
     * Bước 1: Thu thập 3 việc đã làm
     */
    public function askTasksDone(Nutgram $bot)
    {
        $this->tasks_done = $bot->message()->text;
        
        $bot->sendMessage(
            text: "Đã ghi nhận công việc. \n\n**Bước 2:** Có việc gì đang vướng không bạn? (Nhập text hoặc bấm /skip nếu không có)",
            reply_markup: ForceReply::make(true, null, true),
            parse_mode: 'Markdown'
        );
        $this->next('askStuckTask');
    }

    /**
     * Bước 2: Thu thập việc vướng mắc
     */
    public function askStuckTask(Nutgram $bot)
    {
        $text = $bot->message()->text;
        $this->stuck_task = $text === '/skip' ? null : $text;

        $bot->sendMessage(
            text: "**Bước 3:** Bạn cần ai hỗ trợ không? Hãy chọn từ danh sách dưới đây:",
            reply_markup: MessageTemplates::supportKeyboard()
        );
        $this->next('askSupport');
    }

    /**
     * Bước 3: Thu thập người hỗ trợ (từ inline keyboard)
     */
    public function askSupport(Nutgram $bot)
    {
        if (!$bot->isCallbackQuery()) {
            $bot->sendMessage("Vui lòng chọn từ menu dưới đây:");
            return;
        }

        $this->support_needed = $bot->callbackQuery()->data;
        $bot->answerCallbackQuery();

        $bot->sendMessage(
            text: "**Bước 4:** Tâm trạng & năng lượng hôm nay của bạn thế nào?",
            reply_markup: MessageTemplates::moodKeyboard()
        );
        $this->next('askMood');
    }

    /**
     * Bước 4: Thu thập tâm trạng (từ inline keyboard)
     */
    public function askMood(Nutgram $bot)
    {
        if (!$bot->isCallbackQuery()) {
            $bot->sendMessage("Vui lòng chọn số sao:");
            return;
        }

        $this->mood = str_replace('mood_', '', $bot->callbackQuery()->data);
        $bot->answerCallbackQuery();

        $bot->sendMessage(
            text: "**Bước 5:** Viết một dòng cảm xúc tự do (dưới 100 ký tự):",
            reply_markup: ForceReply::make(true, null, true)
        );
        $this->next('askFeeling');
    }

    /**
     * Bước 5: Thu thập cảm xúc
     */
    public function askFeeling(Nutgram $bot)
    {
        $this->feeling = $bot->message()->text;

        $user = TelegramUser::where('telegram_id', $bot->user()->id)->first();
        $team = $user->team;

        if ($team && $team->kpis->count() > 0) {
            $firstKpi = $team->kpis->first();
            $bot->sendMessage(
                text: MessageTemplates::kpiPrompt($firstKpi->name, $firstKpi->question_text),
                reply_markup: ForceReply::make(true, null, true)
            );
            
            $bot->setUserData('current_kpi_index', 0);
            $this->next('askKpis');
        } else {
            $this->finishReport($bot);
        }
    }

    /**
     * Bước 6: Thu thập KPI động theo team
     */
    public function askKpis(Nutgram $bot)
    {
        $user = TelegramUser::where('telegram_id', $bot->user()->id)->first();
        $team = $user->team;
        $currentIndex = $bot->getUserData('current_kpi_index', null, 0);
        
        $kpis = $team->kpis;
        $currentKpi = $kpis[$currentIndex];
        
        $this->kpi_answers[$currentKpi->name] = $bot->message()->text;
        
        $nextIndex = $currentIndex + 1;
        
        if ($nextIndex < $kpis->count()) {
            $nextKpi = $kpis[$nextIndex];
            $bot->sendMessage(
                text: MessageTemplates::kpiPrompt($nextKpi->name, $nextKpi->question_text),
                reply_markup: ForceReply::make(true, null, true)
            );
            $bot->setUserData('current_kpi_index', $nextIndex);
            $this->next('askKpis');
        } else {
            $this->finishReport($bot);
        }
    }

    /**
     * Kết thúc báo cáo và lưu DB
     */
    protected function finishReport(Nutgram $bot)
    {
        $user = TelegramUser::where('telegram_id', $bot->user()->id)->first();
        
        $data = [
            'tasks_done' => $this->tasks_done,
            'stuck_task' => $this->stuck_task,
            'support_needed' => $this->support_needed,
            'mood' => $this->mood,
            'feeling' => $this->feeling,
            'kpis' => $this->kpi_answers
        ];

        Report::updateOrCreate(
            [
                'telegram_user_id' => $user->id,
                'type' => 'daily',
                'date' => Carbon::today(),
            ],
            [
                'data' => $data,
                'points_earned' => 10,
                'status' => 'submitted',
            ]
        );

        $bot->sendMessage("🎉 Cảm ơn bạn đã hoàn thành báo cáo ngày! Bé đã ghi nhận.");
        $this->end();
    }
}
