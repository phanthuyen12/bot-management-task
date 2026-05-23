<?php

namespace App\Telegram\Views;

use App\Models\TelegramUser;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;

class MessageTemplates
{
    public static function welcome($user): string
    {
        return "Chào mừng {$user->first_name} đến với BiBi! 🤖\n\nBé sẽ giúp bạn thực hiện các báo cáo và theo dõi KPI. Hãy chọn một chức năng dưới đây:";
    }

    public static function homeKeyboard(): InlineKeyboardMarkup
    {
        return InlineKeyboardMarkup::make()
            ->addRow(
                InlineKeyboardButton::make('👥 Đăng ký team', callback_data: 'menu_register_team'),
                InlineKeyboardButton::make('📝 Báo cáo ngày', callback_data: 'menu_daily')
            )
            ->addRow(
                InlineKeyboardButton::make('📊 Báo cáo tuần', callback_data: 'menu_weekly'),
                InlineKeyboardButton::make('📅 Báo cáo tháng', callback_data: 'menu_monthly')
            )
            ->addRow(
                InlineKeyboardButton::make('📋 Xem báo cáo', callback_data: 'menu_view_reports'),
                InlineKeyboardButton::make('🏆 Bảng xếp hạng', callback_data: 'menu_leaderboard')
            )
            ->addRow(
                InlineKeyboardButton::make('ℹ️ Thông tin cá nhân', callback_data: 'menu_info')
            );
    }

    public static function reportTypesKeyboard(): InlineKeyboardMarkup
    {
        return InlineKeyboardMarkup::make()
            ->addRow(
                InlineKeyboardButton::make('📝 Ngày', callback_data: 'reports_type_daily'),
                InlineKeyboardButton::make('📊 Tuần', callback_data: 'reports_type_weekly'),
            )
            ->addRow(
                InlineKeyboardButton::make('📅 Tháng', callback_data: 'reports_type_monthly'),
            )
            ->addRow(
                InlineKeyboardButton::make('🏠 Menu chính', callback_data: 'menu_home'),
            );
    }

    public static function teamRegistrationPrompt(): string
    {
        return "👥 ĐĂNG KÝ TEAM\n\nVui lòng chọn team bạn muốn tham gia.";
    }

    public static function dailyReportPrompt(): string
    {
        return "📝 BÁO CÁO NGÀY\n\nBước 1: Hãy nhập 3 việc bạn đã hoàn thành hôm nay (cách nhau bằng dấu xuống dòng).";
    }

    public static function moodKeyboard(): InlineKeyboardMarkup
    {
        return InlineKeyboardMarkup::make()
            ->addRow(
                InlineKeyboardButton::make('⭐ 1', callback_data: 'mood_1'),
                InlineKeyboardButton::make('⭐⭐ 2', callback_data: 'mood_2'),
                InlineKeyboardButton::make('⭐⭐⭐ 3', callback_data: 'mood_3'),
                InlineKeyboardButton::make('⭐⭐⭐⭐ 4', callback_data: 'mood_4'),
                InlineKeyboardButton::make('⭐⭐⭐⭐⭐ 5', callback_data: 'mood_5')
            );
    }

    public static function supportKeyboard(?TelegramUser $leader = null, array $peers = []): InlineKeyboardMarkup
    {
        $keyboard = InlineKeyboardMarkup::make();

        if ($leader !== null) {
            self::addSupportUserRow($keyboard, $leader, 'Team Lead');
        }

        foreach ($peers as $peer) {
            self::addSupportUserRow($keyboard, $peer, 'Đồng đội');
        }

        $keyboard->addRow(
            InlineKeyboardButton::make('Không cần hỗ trợ', callback_data: 'support_none')
        );

        return $keyboard;
    }

    public static function kpiPrompt(string $kpiName, string $question): string
    {
        return "📊 KPI: {$kpiName}\n\n{$question}";
    }

    public static function weeklyReportPrompt(): string
    {
        return "📊 BÁO CÁO TUẦN\n\nBước 1: Tóm tắt những việc bạn đã hoàn thành trong tuần này.";
    }

    public static function monthlyReportPrompt(): string
    {
        return "📅 BÁO CÁO THÁNG\n\nBước 1: Điểm nổi bật nhất của bạn trong tháng này.";
    }

    public static function leaderboard($users): string
    {
        $msg = "🏆 BẢNG XẾP HẠNG\n\n";
        foreach ($users as $index => $u) {
            $medal = $index == 0 ? '🥇' : ($index == 1 ? '🥈' : ($index == 2 ? '🥉' : '👤'));
            $msg .= "{$medal} {$u->first_name}\n";
            $msg .= "└ 💰 Điểm: {$u->points} | 🔥 Streak: {$u->streak_count} ngày\n\n";
        }
        return $msg;
    }

    public static function reminder($user, $type): string
    {
        if ($type === '21:00') {
            return "Ê {$user->first_name}, hôm nay quẩy gì kể bé nghe đi ☕ — còn 1 tiếng nữa thôi đấy nha.";
        }
        if ($type === '21:45') {
            return "15 phút nữa thôi đó {$user->first_name}, streak {$user->streak_count} ngày của bạn đang đẹp lắm, đừng bỏ giữa chừng nha 🔥";
        }
        return "Hôm nay {$user->first_name} vắng mặt trong sổ nhật ký rồi 😔 — mai gặp lại nha, bé vẫn đợi bạn.";
    }

    protected static function truncateLabel(string $label, int $limit = 40): string
    {
        return mb_strlen($label) > $limit
            ? mb_substr($label, 0, $limit - 1) . '…'
            : $label;
    }

    protected static function addSupportUserRow(InlineKeyboardMarkup $keyboard, TelegramUser $user, string $prefix): void
    {
        $label = $prefix . ': ' . self::truncateLabel($user->displayName(), 28);

        if (!empty($user->username)) {
            $keyboard->addRow(
                InlineKeyboardButton::make($label, url: 'https://t.me/' . ltrim($user->username, '@')),
                InlineKeyboardButton::make('Xong', callback_data: 'support_user_' . $user->id)
            );

            return;
        }

        $keyboard->addRow(
            InlineKeyboardButton::make($label, callback_data: 'support_user_' . $user->id)
        );
    }
}
