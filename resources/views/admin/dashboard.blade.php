@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('header', 'Bảng điều khiển')
@section('subheader', 'Tổng quan hệ thống người dùng, đội nhóm và báo cáo hàng ngày')

@section('content')
    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-6">
        
        <!-- Users Card -->
        <div class="bg-white dark:bg-[#1e293b] rounded-[10px] p-5 shadow-sm border border-slate-200 dark:border-slate-800 flex flex-col justify-between hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Người dùng</p>
                    <h3 class="text-2xl font-bold text-slate-800 dark:text-slate-100 mt-1">{{ $userCount }}</h3>
                </div>
                <div class="w-10 h-10 rounded-[10px] bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                <a href="{{ route('admin.users.index') }}" class="text-xs text-blue-600 dark:text-blue-400 font-medium hover:underline flex items-center gap-1">
                    Xem chi tiết <i class="fas fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </div>

        <!-- Teams Card -->
        <div class="bg-white dark:bg-[#1e293b] rounded-[10px] p-5 shadow-sm border border-slate-200 dark:border-slate-800 flex flex-col justify-between hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Teams</p>
                    <h3 class="text-2xl font-bold text-slate-800 dark:text-slate-100 mt-1">{{ $teamCount }}</h3>
                </div>
                <div class="w-10 h-10 rounded-[10px] bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                    <i class="fas fa-layer-group"></i>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                <a href="{{ route('admin.teams.index') }}" class="text-xs text-emerald-600 dark:text-emerald-400 font-medium hover:underline flex items-center gap-1">
                    Quản lý team <i class="fas fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </div>

        <!-- Reports Card -->
        <div class="bg-white dark:bg-[#1e293b] rounded-[10px] p-5 shadow-sm border border-slate-200 dark:border-slate-800 flex flex-col justify-between hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Báo cáo</p>
                    <h3 class="text-2xl font-bold text-slate-800 dark:text-slate-100 mt-1">{{ $reportCount }}</h3>
                </div>
                <div class="w-10 h-10 rounded-[10px] bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center text-amber-600 dark:text-amber-400">
                    <i class="fas fa-file-alt"></i>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                <a href="{{ route('admin.reports.index') }}" class="text-xs text-amber-600 dark:text-amber-500 font-medium hover:underline flex items-center gap-1">
                    Xem báo cáo <i class="fas fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </div>

        <!-- KPI Card -->
        <div class="bg-white dark:bg-[#1e293b] rounded-[10px] p-5 shadow-sm border border-slate-200 dark:border-slate-800 flex flex-col justify-between hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">KPI / Đánh giá</p>
                    <h3 class="text-2xl font-bold text-slate-800 dark:text-slate-100 mt-1">{{ $kpiCount }}</h3>
                </div>
                <div class="w-10 h-10 rounded-[10px] bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                <a href="{{ route('admin.reports.index') }}" class="text-xs text-indigo-600 dark:text-indigo-400 font-medium hover:underline flex items-center gap-1">
                    Xem KPI <i class="fas fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Top Users Table -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-[#1e293b] rounded-[10px] shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden h-full flex flex-col">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center bg-slate-50/50 dark:bg-slate-800/30">
                    <h3 class="text-base font-semibold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                        Top người dùng
                    </h3>
                </div>
                <div class="flex-1 p-0 overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 text-xs uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                                <th class="px-5 py-3 font-medium w-12 text-center">#</th>
                                <th class="px-5 py-3 font-medium">Người dùng</th>
                                <th class="px-5 py-3 font-medium">Team</th>
                                <th class="px-5 py-3 font-medium text-right">Điểm</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse($topUsers as $index => $user)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                    <td class="px-5 py-3 text-center">
                                        <span class="text-slate-500 dark:text-slate-400 font-medium">{{ $index + 1 }}</span>
                                    </td>
                                    <td class="px-5 py-3">
                                        <div class="flex items-center gap-3">
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->first_name . ' ' . $user->last_name) }}&background=f1f5f9&color=475569&size=32" class="w-8 h-8 rounded-[10px]" alt="Avatar">
                                            <div>
                                                <p class="font-medium text-slate-800 dark:text-slate-200">{{ $user->first_name }} {{ $user->last_name }}</p>
                                                <p class="text-xs text-slate-500 dark:text-slate-400">&commat;{{ $user->username }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-[10px] text-xs font-medium {{ $user->team ? 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300' : 'bg-slate-50 text-slate-500 dark:bg-slate-800/50 dark:text-slate-500' }}">
                                            {{ $user->team?->name ?? 'Chưa phân' }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-right">
                                        <span class="font-semibold text-slate-800 dark:text-slate-200">{{ number_format($user->points) }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-8 text-center text-slate-500 dark:text-slate-400">
                                        <p>Chưa có dữ liệu người dùng</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Reports -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-[#1e293b] rounded-[10px] shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden h-full flex flex-col">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center bg-slate-50/50 dark:bg-slate-800/30">
                    <h3 class="text-base font-semibold text-slate-800 dark:text-slate-100">
                        Báo cáo gần nhất
                    </h3>
                </div>

                <div class="flex-1 px-5 py-4 overflow-y-auto custom-scrollbar">
                    <div class="space-y-4">
                        @forelse($recentReports as $report)
                            <div class="flex items-start justify-between group">
                                <div>
                                    <p class="text-sm font-medium text-slate-800 dark:text-slate-200 mb-0.5">{{ $report->user?->first_name ?? 'Không xác định' }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 truncate max-w-[150px]">{{ $report->type }}</p>
                                    <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1">{{ $report->date?->format('d/m/Y') ?? 'N/A' }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="inline-block px-2 py-0.5 text-[10px] font-medium rounded-[10px] mb-1
                                        @if($report->status == 'approved') bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400
                                        @elseif($report->status == 'pending') bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400
                                        @elseif($report->status == 'rejected') bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400
                                        @else bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 @endif">
                                        {{ ucfirst($report->status) }}
                                    </span>
                                    <p class="text-xs font-semibold text-slate-700 dark:text-slate-300">Điểm: {{ $report->points_earned }}</p>
                                </div>
                            </div>
                            @if(!$loop->last)
                                <hr class="border-slate-100 dark:border-slate-800 my-3">
                            @endif
                        @empty
                            <div class="text-center py-6 text-sm text-slate-500 dark:text-slate-400">
                                Chưa có báo cáo nào
                            </div>
                        @endforelse
                    </div>
                </div>
                
                @if(count($recentReports) > 0)
                <div class="px-5 py-3 border-t border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30 text-center">
                    <a href="{{ route('admin.reports.index') }}" class="text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                        Xem tất cả báo cáo
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection
