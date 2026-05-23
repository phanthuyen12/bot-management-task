<?php

namespace App\Telegram\Services;

use App\Models\Report;

class ReportMessageFormatter
{
    public static function typeLabel(string $type): string
    {
        return match ($type) {
            'daily' => 'Báo cáo ngày',
            'weekly' => 'Báo cáo tuần',
            'monthly' => 'Báo cáo tháng',
            default => $type,
        };
    }

    public static function listItem(Report $report): string
    {
        $date = $report->date?->format('d/m/Y') ?? '—';
        $points = $report->points_earned;

        return "• {$date} (+{$points} điểm)";
    }

    public static function detail(Report $report): string
    {
        $type = self::typeLabel($report->type);
        $date = $report->date?->format('d/m/Y') ?? '—';
        $data = $report->data ?? [];

        $lines = [
            "📋 {$type}",
            "📅 Ngày: {$date}",
            "💰 Điểm: +{$report->points_earned}",
            '—',
        ];

        $lines = array_merge($lines, self::formatDataFields($report->type, $data));

        return implode("\n", $lines);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return list<string>
     */
    protected static function formatDataFields(string $type, array $data): array
    {
        return match ($type) {
            'daily' => self::formatDaily($data),
            'weekly' => self::formatWeekly($data),
            'monthly' => self::formatMonthly($data),
            default => self::formatGeneric($data),
        };
    }

    /**
     * @param  array<string, mixed>  $data
     * @return list<string>
     */
    protected static function formatDaily(array $data): array
    {
        $lines = [];

        if (!empty($data['tasks_done'])) {
            $lines[] = '✅ Việc đã làm:';
            $lines[] = self::indent((string) $data['tasks_done']);
        }
        if (!empty($data['stuck_task'])) {
            $lines[] = '⚠️ Việc vướng:';
            $lines[] = self::indent((string) $data['stuck_task']);
        }
        if (!empty($data['support_needed'])) {
            $lines[] = '🤝 Hỗ trợ: ' . self::supportLabel(
                (string) $data['support_needed'],
                is_array($data['support_person'] ?? null) ? $data['support_person'] : null,
            );
        }
        if (!empty($data['mood'])) {
            $lines[] = '⭐ Tâm trạng: ' . $data['mood'] . '/5';
        }
        if (!empty($data['feeling'])) {
            $lines[] = '💬 Cảm xúc: ' . $data['feeling'];
        }
        if (!empty($data['kpis']) && is_array($data['kpis'])) {
            $lines[] = '📊 KPI:';
            foreach ($data['kpis'] as $name => $answer) {
                $lines[] = "  • {$name}: {$answer}";
            }
        }

        return $lines ?: ['(Không có nội dung chi tiết)'];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return list<string>
     */
    protected static function formatWeekly(array $data): array
    {
        $lines = [];

        if (!empty($data['summary'])) {
            $lines[] = '📝 Tóm tắt tuần:';
            $lines[] = self::indent((string) $data['summary']);
        }
        if (!empty($data['challenges'])) {
            $lines[] = '⚠️ Khó khăn:';
            $lines[] = self::indent((string) $data['challenges']);
        }
        if (!empty($data['shoutout'])) {
            $lines[] = '🙌 Ghi nhận đồng đội:';
            $lines[] = self::indent((string) $data['shoutout']);
        }

        return $lines ?: ['(Không có nội dung chi tiết)'];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return list<string>
     */
    protected static function formatMonthly(array $data): array
    {
        $lines = [];

        if (!empty($data['highlights'])) {
            $lines[] = '✨ Điểm nổi bật:';
            $lines[] = self::indent((string) $data['highlights']);
        }
        if (!empty($data['achievements'])) {
            $lines[] = '🏆 Thành tựu:';
            $lines[] = self::indent((string) $data['achievements']);
        }
        if (!empty($data['next_goals'])) {
            $lines[] = '🎯 Mục tiêu tháng tới:';
            $lines[] = self::indent((string) $data['next_goals']);
        }

        return $lines ?: ['(Không có nội dung chi tiết)'];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return list<string>
     */
    protected static function formatGeneric(array $data): array
    {
        $lines = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $value = json_encode($value, JSON_UNESCAPED_UNICODE);
            }
            $lines[] = "{$key}: {$value}";
        }

        return $lines ?: ['(Không có nội dung chi tiết)'];
    }

    protected static function supportLabel(string $code, ?array $person = null): string
    {
        if ($person && !empty($person['name'])) {
            $role = match ($person['role'] ?? null) {
                'lead' => 'Team Lead',
                'peer' => 'Đồng đội',
                default => null,
            };

            return $role ? "{$role}: {$person['name']}" : (string) $person['name'];
        }

        return match ($code) {
            'support_lead' => 'Team Lead',
            'support_peer' => 'Đồng đội',
            'support_none' => 'Không cần',
            default => $code,
        };
    }

    protected static function indent(string $text): string
    {
        return implode("\n", array_map(
            fn (string $line) => '  ' . $line,
            explode("\n", trim($text))
        ));
    }
}
