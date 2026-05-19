<?php

namespace App\Telegram\Views;

use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;

class MessageTemplates
{
    /**
     * Tin nhắn chào mừng và Menu chính (Giao diện Home)
     */
    public static function welcome($user): string
    {
        return "Chào mừng **{$user->first_name}** đến với BiBi! 🤖\n\nBé sẽ giúp bạn thực hiện các báo cáo và theo dõi KPI. Hãy chọn một chức năng dưới đây:";
    }

    /**
     * Giao diện Home với các nút bấm
     */
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
                InlineKeyboardButton::make('🏆 Bảng xếp hạng', callback_data: 'menu_leaderboard'),
                InlineKeyboardButton::make('ℹ️ Thông tin cá nhân', callback_data: 'menu_info')
            );
    }

    /**
     * Tin nhắn chọn team đăng ký
     */
    public static function teamRegistrationPrompt(): string
    {
        return "👥 **ĐĂNG KÝ TEAM** 👥\n\nVui lòng chọn team bạn muốn tham gia.";
    }

    /**
     * Tin nhắn bắt đầu báo cáo ngày
     */
    public static function dailyReportPrompt(): string
    {
        return "📝 **BÁO CÁO NGÀY** 📝\n\n**Bước 1:** Hãy nhập 3 việc bạn đã hoàn thành hôm nay (cách nhau bằng dấu xuống dòng).";
    }

    /**
     * Keyboard chọn tâm trạng (1-5 sao)
     */
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

    /**
     * Keyboard chọn người hỗ trợ (mẫu)
     */
    public static function supportKeyboard(): InlineKeyboardMarkup
    {
        return InlineKeyboardMarkup::make()
            ->addRow(
                InlineKeyboardButton::make('Team Lead', callback_data: 'support_lead'),
                InlineKeyboardButton::make('Đồng đội', callback_data: 'support_peer')
            )
            ->addRow(
                InlineKeyboardButton::make('Không cần hỗ trợ', callback_data: 'support_none')
            );
    }

    /**
     * Tin nhắn hỏi KPI
     */
    public static function kpiPrompt(string $kpiName, string $question): string
    {
        return "📊 **Chỉ số KPI: {$kpiName}**\n\n{$question}";
    }

    public static function weeklyReportPrompt(): string
    {
        return "📅 **BÁO CÁO TUẦN** 📅\n\nHãy tóm tắt nhanh những việc bạn đã hoàn thành trong tuần này.";
    }

    public static function monthlyReportPrompt(): string
    {
        return "🗓️ **BÁO CÁO THÁNG** 🗓️\n\nHãy chia sẻ những điểm nổi bật của bạn trong tháng này.";
    }

    /**
     * Bảng xếp hạng
     */
    public static function leaderboard($users): string
    {
        $msg = "🏆 **BẢNG XẾP HẠNG** 🏆\n\n";
        foreach ($users as $index => $u) {
            $medal = $index == 0 ? '🥇' : ($index == 1 ? '🥈' : ($index == 2 ? '🥉' : '👤'));
            $msg .= "{$medal} **{$u->first_name}**\n";
            $msg .= "└ 💰 Điểm: {$u->points} | 🔥 Streak: {$u->streak_count} ngày\n\n";
        }
        return $msg;
    }

    /**
     * Tin nhắn nhắc nhở
     */
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
}
