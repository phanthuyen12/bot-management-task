@extends('admin.layouts.app')

@section('title', 'Người dùng')
@section('header', 'Quản lý người dùng')
@section('subheader', 'Xem và quản lý thông tin các user Telegram')

@section('actions')
    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-[#1e293b] border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 rounded-[10px] hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors text-sm font-medium shadow-sm">
        <i class="fas fa-sync-alt"></i> Làm mới
    </a>
@endsection

@section('content')
    <!-- Search & Filter Bar -->
    <div class="bg-white dark:bg-[#1e293b] rounded-[10px] p-4 md:p-5 shadow-sm border border-slate-200 dark:border-slate-800 mb-6">
        <div class="flex flex-col gap-4">
            <div>
                <h3 class="text-base font-semibold text-slate-800 dark:text-slate-100 mb-1">Danh sách người dùng</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400">Quản lý phân team, điểm và chuỗi liên tiếp của từng thành viên.</p>
            </div>
            <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col md:flex-row gap-3 items-start md:items-end">
                <!-- Search input -->
                <div class="flex-1 w-full md:w-auto">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Tìm kiếm</label>
                    <div class="relative">
                        <input type="search" 
                               name="search" 
                               placeholder="Tìm kiếm username, tên..." 
                               value="{{ $search ?? '' }}"
                               class="w-full px-4 py-2 pl-10 border border-slate-200 dark:border-slate-700 rounded-[10px] bg-slate-50 dark:bg-slate-800/50 text-slate-800 dark:text-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors placeholder-slate-400 dark:placeholder-slate-500">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    </div>
                </div>
                
                <!-- Team filter -->
                <div class="w-full md:w-auto">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Team</label>
                    <select name="team_id" 
                            class="w-full px-4 py-2 border border-slate-200 dark:border-slate-700 rounded-[10px] bg-slate-50 dark:bg-slate-800/50 text-slate-800 dark:text-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                        <option value="">-- Tất cả teams --</option>
                        @foreach($teams as $team)
                            <option value="{{ $team->id }}" {{ $teamId == $team->id ? 'selected' : '' }}>
                                {{ $team->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Buttons -->
                <div class="flex gap-2 w-full md:w-auto">
                    <button type="submit" class="flex-1 md:flex-none px-4 py-2 bg-indigo-600 text-white rounded-[10px] hover:bg-indigo-700 transition-colors text-sm font-medium shadow-sm whitespace-nowrap">
                        <i class="fas fa-search mr-2"></i>Tìm kiếm
                    </button>
                    @if($search || $teamId)
                        <a href="{{ route('admin.users.index') }}" class="flex-1 md:flex-none px-4 py-2 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-[10px] hover:bg-slate-300 dark:hover:bg-slate-600 transition-colors text-sm font-medium whitespace-nowrap text-center">
                            <i class="fas fa-times mr-2"></i>Xóa lọc
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white dark:bg-[#1e293b] rounded-[10px] shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden flex flex-col">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 text-xs uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                        <th class="px-6 py-4 font-medium w-16 text-center">ID</th>
                        <th class="px-6 py-4 font-medium">Người dùng</th>
                        <th class="px-6 py-4 font-medium">Team</th>
                        <th class="px-6 py-4 font-medium text-right">Điểm</th>
                        <th class="px-6 py-4 font-medium text-center">Chuỗi</th>
                        <th class="px-6 py-4 font-medium text-center w-28">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-6 py-4 text-center text-slate-500 dark:text-slate-400">{{ $user->id }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->first_name . ' ' . $user->last_name) }}&background=e0e7ff&color=4f46e5&size=36" class="w-9 h-9 rounded-[10px]" alt="Avatar">
                                    <div>
                                        <p class="font-medium text-slate-800 dark:text-slate-200">{{ $user->first_name }} {{ $user->last_name }}</p>
                                        <p class="text-xs text-indigo-600 dark:text-indigo-400 font-medium">&commat;{{ $user->username }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-[10px] text-xs font-medium {{ $user->team ? 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300' : 'bg-slate-50 text-slate-500 dark:bg-slate-800/50 dark:text-slate-500' }}">
                                    {{ $user->team?->name ?? 'Chưa phân' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="font-bold text-slate-800 dark:text-slate-200">{{ number_format($user->points) }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 rounded-[10px] text-xs font-semibold">
                                    <i class="fas fa-fire"></i> {{ $user->streak_count ?? 0 }} / {{ $user->best_streak ?? 0 }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('admin.users.edit', $user) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-[10px] bg-slate-100 text-slate-600 hover:bg-indigo-100 hover:text-indigo-600 dark:bg-slate-800 dark:text-slate-400 dark:hover:bg-indigo-900/30 dark:hover:text-indigo-400 transition-colors" title="Sửa">
                                    <i class="fas fa-pen text-xs"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-slate-500 dark:text-slate-400">
                                <p>Không tìm thấy người dùng nào</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($users->total() > 0)
        <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <span class="text-sm text-slate-600 dark:text-slate-300 font-medium">
                    <i class="fas fa-users mr-2 text-indigo-600"></i>
                    Hiển thị <strong class="text-indigo-600 dark:text-indigo-400">{{ $users->firstItem() ?? 0 }}</strong> đến <strong class="text-indigo-600 dark:text-indigo-400">{{ $users->lastItem() ?? 0 }}</strong> trên <strong class="text-indigo-600 dark:text-indigo-400">{{ $users->total() }}</strong> thành viên
                </span>
                @if($users->hasPages())
                <div class="tailwind-pagination">
                    {{ $users->appends(request()->query())->links('pagination::tailwind') }}
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>
@endsection
