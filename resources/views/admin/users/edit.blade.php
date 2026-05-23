@extends('admin.layouts.app')

@section('title', 'Chỉnh sửa người dùng')
@section('header', 'Chỉnh sửa thành viên')
@section('subheader', 'Điều chỉnh team, điểm và chuỗi liên tiếp để cập nhật leaderboard')

@section('actions')
    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-[#1e293b] border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 rounded-[10px] hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors text-sm font-medium shadow-sm">
        <i class="fas fa-arrow-left"></i> Quay lại
    </a>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- User Profile Card -->
    <div class="lg:col-span-1">
        <div class="bg-white dark:bg-[#1e293b] rounded-[10px] shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30">
                <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300">Thông tin Tài khoản</h3>
            </div>
            <div class="p-6 flex flex-col items-center text-center">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->first_name . ' ' . $user->last_name) }}&background=e0e7ff&color=4f46e5&size=100" class="w-20 h-20 rounded-[10px] mb-4 shadow-sm" alt="Avatar">
                <h4 class="text-base font-bold text-slate-800 dark:text-slate-100">{{ $user->first_name }} {{ $user->last_name }}</h4>
                <p class="text-sm text-indigo-600 dark:text-indigo-400 font-medium mb-4">&commat;{{ $user->username }}</p>

                <div class="w-full space-y-3 text-left">
                    <div class="bg-slate-50 dark:bg-slate-800/50 rounded-[10px] px-4 py-3 border border-slate-100 dark:border-slate-800">
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-0.5">Username</p>
                        <p class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $user->username }}</p>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-800/50 rounded-[10px] px-4 py-3 border border-slate-100 dark:border-slate-800">
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-0.5">Email</p>
                        <p class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $user->email ?? 'Không có' }}</p>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-800/50 rounded-[10px] px-4 py-3 border border-slate-100 dark:border-slate-800">
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-0.5">Team hiện tại</p>
                        <p class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $user->team?->name ?? 'Chưa phân' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="lg:col-span-2">
        <div class="bg-white dark:bg-[#1e293b] rounded-[10px] shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30">
                <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-2">
                    <i class="fas fa-pen text-indigo-500"></i> Chỉnh sửa thông tin
                </h3>
            </div>
            <div class="p-6">
                @if ($errors->any())
                    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/50 text-red-700 dark:text-red-400 px-4 py-3 rounded-[10px] mb-5 text-sm">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.users.update', $user) }}">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
                        <!-- Team -->
                        <div class="md:col-span-2">
                            <label for="team_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Phân team</label>
                            <div class="relative">
                                <select name="team_id" id="team_id" class="block w-full appearance-none bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-[10px] px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors cursor-pointer">
                                    <option value="">— Chưa phân team —</option>
                                    @foreach($teams as $team)
                                        <option value="{{ $team->id }}" {{ $team->id === $user->team_id ? 'selected' : '' }}>{{ $team->name }}</option>
                                    @endforeach
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Points -->
                        <div>
                            <label for="points" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                Điểm tích lũy
                                <span class="text-xs text-slate-400 font-normal ml-1">(tổng số điểm)</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-star text-amber-500 text-xs"></i>
                                </div>
                                <input type="number" id="points" name="points" min="0" value="{{ old('points', $user->points) }}" class="block w-full pl-9 pr-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-[10px] bg-slate-50 dark:bg-slate-800/50 text-slate-800 dark:text-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                            </div>
                        </div>

                        <!-- Streak Count -->
                        <div>
                            <label for="streak_count" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                Chuỗi hiện tại
                                <span class="text-xs text-slate-400 font-normal ml-1">(ngày liên tiếp)</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-fire text-orange-500 text-xs"></i>
                                </div>
                                <input type="number" id="streak_count" name="streak_count" min="0" value="{{ old('streak_count', $user->streak_count) }}" class="block w-full pl-9 pr-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-[10px] bg-slate-50 dark:bg-slate-800/50 text-slate-800 dark:text-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                            </div>
                        </div>

                        <!-- Best Streak -->
                        <div>
                            <label for="best_streak" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                Chuỗi tốt nhất
                                <span class="text-xs text-slate-400 font-normal ml-1">(kỷ lục)</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-trophy text-indigo-400 text-xs"></i>
                                </div>
                                <input type="number" id="best_streak" name="best_streak" min="0" value="{{ old('best_streak', $user->best_streak) }}" class="block w-full pl-9 pr-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-[10px] bg-slate-50 dark:bg-slate-800/50 text-slate-800 dark:text-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 rounded-[10px] hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors text-sm font-medium">
                            Hủy bỏ
                        </a>
                        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2 bg-indigo-600 text-white rounded-[10px] hover:bg-indigo-700 transition-colors text-sm font-medium shadow-sm">
                            <i class="fas fa-save"></i> Lưu thay đổi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
