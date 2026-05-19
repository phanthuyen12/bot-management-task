<?php

namespace App\Services;

use App\Models\TelegramUser;
use App\Models\Report;
use App\Models\Badge;
use Carbon\Carbon;

class ReportService
{
    /**
     * Tính điểm cho báo cáo dựa trên quy tắc.
     */
    public function calculatePoints(Report $report): int
    {
        $points = 0;
        $data = $report->data;

        if ($report->type === 'daily') {
            // Gửi trước 22:00
            if (Carbon::parse($report->created_at)->format('H:i') < '22:00') {
                $points += 10;
            }

            // Đủ 3 việc đã làm
            if (isset($data['tasks_done']) && count($data['tasks_done']) >= 3) {
                $points += 5;
            }

            // Có nêu việc kẹt
            if (!empty($data['stuck_task'])) {
                $points += 5;
            }

            // Đạt KPI ngày (giả định có flag hoặc logic check)
            if (!empty($data['kpi_met'])) {
                $points += 10;
            }
        } elseif ($report->type === 'weekly') {
            if (Carbon::parse($report->created_at)->format('H:i') < '22:00' && Carbon::parse($report->created_at)->isFriday()) {
                $points += 30;
            }
            // Thêm logic cho KPI tuần...
            if (!empty($data['shoutout'])) {
                $points += 5;
            }
        } elseif ($report->type === 'monthly') {
            if (Carbon::parse($report->created_at)->format('H:i') < '22:00') {
                $points += 100;
            }
            // Thêm logic cho KPI tháng...
        }

        return $points;
    }

    /**
     * Cập nhật streak cho user.
     */
    public function updateStreak(TelegramUser $user, Report $report): void
    {
        if ($report->type !== 'daily' && $report->type !== 'weekly') {
            return; // Chỉ tính streak cho báo cáo ngày/tuần
        }

        $today = Carbon::today();
        $lastReportDate = $user->last_report_date ? Carbon::parse($user->last_report_date) : null;

        if (!$lastReportDate) {
            $user->streak_count = 1;
        } else {
            // Tính số ngày chênh lệch, bỏ qua T7, CN
            $diffDays = 0;
            $tempDate = $lastReportDate->copy()->addDay();
            
            while ($tempDate->lt($today)) {
                if (!$tempDate->isWeekend()) {
                    $diffDays++;
                }
                $tempDate->addDay();
            }

            if ($diffDays == 0) {
                // Báo cáo liên tiếp (hoặc cách nhau bởi cuối tuần)
                $user->streak_count += 1;
            } elseif ($diffDays > 0) {
                // Bị đứt chuỗi
                $user->streak_count = 1;
            }
        }

        $user->last_report_date = $today;
        
        if ($user->streak_count > $user->best_streak) {
            $user->best_streak = $user->streak_count;
        }

        // Thưởng streak
        if ($user->streak_count == 5) {
            $user->points += 20;
        } elseif ($user->streak_count == 20) {
            $user->points += 100;
            $this->awardBadge($user, 'lua_thieng');
        }

        $user->save();
    }

    /**
     * Cấp huy hiệu cho user.
     */
    public function awardBadge(TelegramUser $user, string $badgeCode): void
    {
        $badge = Badge::where('code', $badgeCode)->first();
        if ($badge && !$user->badges()->where('badge_id', $badge->id)->exists()) {
            $user->badges()->attach($badge->id);
        }
    }
}
