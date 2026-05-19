<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Team;
use App\Models\Kpi;
use App\Models\Badge;

class TeamKpiSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tạo Dữ liệu Teams và KPIs
        $teams = [
            [
                'name' => 'Team Media',
                'slug' => 'media',
                'kpis' => [
                    ['name' => 'Số video đăng', 'type' => 'number', 'question' => 'Hôm nay team mình đăng được bao nhiêu video?'],
                    ['name' => 'View trung bình', 'type' => 'number', 'question' => 'Lượt view trung bình hôm nay là bao nhiêu?'],
                    ['name' => 'Lead thu từ kênh', 'type' => 'number', 'question' => 'Thu được bao nhiêu lead từ kênh?'],
                    ['name' => 'Tỷ lệ chuyển đổi', 'type' => 'number', 'question' => 'Tỷ lệ chuyển đổi hôm nay là bao nhiêu (%)?'],
                ]
            ],
            [
                'name' => 'Team Vận hành',
                'slug' => 'van-hanh',
                'kpis' => [
                    ['name' => 'Task hoàn thành', 'type' => 'number', 'question' => 'Số task đã hoàn thành hôm nay?'],
                    ['name' => 'Deadline trễ', 'type' => 'number', 'question' => 'Số task bị trễ deadline?'],
                    ['name' => 'Sự cố phát sinh', 'type' => 'number', 'question' => 'Số sự cố phát sinh?'],
                    ['name' => 'Vấn đề liên team', 'type' => 'text', 'question' => 'Có vấn đề gì liên team không?'],
                ]
            ],
            [
                'name' => 'Team CSKH',
                'slug' => 'cskh',
                'kpis' => [
                    ['name' => 'Số inbox xử lý', 'type' => 'number', 'question' => 'Số inbox đã xử lý hôm nay?'],
                    ['name' => 'Số khách chốt', 'type' => 'number', 'question' => 'Số khách đã chốt đơn?'],
                    ['name' => 'NPS', 'type' => 'number', 'question' => 'Điểm NPS hôm nay?'],
                    ['name' => 'Lead đang chăm', 'type' => 'number', 'question' => 'Số lead đang chăm sóc?'],
                ]
            ],
            [
                'name' => 'Team Livetrade',
                'slug' => 'livetrade',
                'kpis' => [
                    ['name' => 'Số buổi live', 'type' => 'number', 'question' => 'Hôm nay đã live bao nhiêu buổi?'],
                    ['name' => 'Số người xem', 'type' => 'number', 'question' => 'Số người xem cao nhất (peak view)?'],
                    ['name' => 'Lead', 'type' => 'number', 'question' => 'Số lead thu được từ buổi live?'],
                    ['name' => 'Tỷ lệ chuyển đổi', 'type' => 'number', 'question' => 'Tỷ lệ chuyển đổi (%)?'],
                ]
            ],
            [
                'name' => 'Team Đào tạo',
                'slug' => 'dao-tao',
                'kpis' => [
                    ['name' => 'Số lớp', 'type' => 'number', 'question' => 'Hôm nay có bao nhiêu lớp học?'],
                    ['name' => 'Số học viên active', 'type' => 'number', 'question' => 'Số học viên tham gia học?'],
                    ['name' => 'Tỷ lệ hoàn thành', 'type' => 'number', 'question' => 'Tỷ lệ hoàn thành bài tập (%)?'],
                    ['name' => 'NPS học viên', 'type' => 'number', 'question' => 'Điểm NPS từ học viên?'],
                ]
            ],
        ];

        foreach ($teams as $tData) {
            $team = Team::create([
                'name' => $tData['name'],
                'slug' => $tData['slug'],
            ]);

            foreach ($tData['kpis'] as $kData) {
                Kpi::create([
                    'team_id' => $team->id,
                    'name' => $kData['name'],
                    'question_text' => $kData['question'],
                    'cycle' => 'daily',
                    'type' => $kData['type'],
                ]);
            }
        }

        // 2. Tạo Dữ liệu Huy hiệu (Badges)
        $badges = [
            ['name' => 'Mầm non', 'code' => 'mam_non', 'icon' => '🌱', 'description' => 'Báo cáo đầy đủ 5 ngày làm việc đầu tiên'],
            ['name' => 'Tia chớp', 'code' => 'tia_chop', 'icon' => '⚡', 'description' => 'Top 3 báo cáo nhanh nhất 1 tuần'],
            ['name' => 'Xạ thủ', 'code' => 'xa_thu', 'icon' => '🎯', 'description' => 'Vượt KPI cá nhân 3 tuần liên tiếp'],
            ['name' => 'Lửa thiêng', 'code' => 'lua_thieng', 'icon' => '🔥', 'description' => 'Streak 20 ngày làm việc liên tiếp'],
        ];

        foreach ($badges as $badge) {
            Badge::create($badge);
        }
    }
}
