@extends('admin.layouts.app')

@section('title', 'Báo cáo')
@section('header', 'Quản lý báo cáo')
@section('subheader', 'Xem và duyệt tất cả báo cáo người dùng trong hệ thống')

@section('content')
    <!-- Status Overview -->
    <div class="bg-white dark:bg-[#1e293b] rounded-[10px] p-4 md:p-5 shadow-sm border border-slate-200 dark:border-slate-800 mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h3 class="text-base font-semibold text-slate-800 dark:text-slate-100 mb-1">Danh sách báo cáo</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400">Thông tin báo cáo được cập nhật theo thời gian thực.</p>
        </div>
        <div class="px-4 py-2 bg-slate-100 dark:bg-slate-800 rounded-[10px] text-sm font-medium text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
            Tổng cộng: <span class="font-bold text-indigo-600 dark:text-indigo-400">{{ $reports->total() }}</span> báo cáo
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
                        <th class="px-6 py-4 font-medium">Ngày báo cáo</th>
                        <th class="px-6 py-4 font-medium">Loại</th>
                        <th class="px-6 py-4 font-medium text-center">Trạng thái</th>
                        <th class="px-6 py-4 font-medium text-right">Điểm</th>
                        <th class="px-6 py-4 font-medium text-center w-20">Chi tiết</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($reports as $report)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-6 py-4 text-center text-slate-500 dark:text-slate-400">{{ $report->id }}</td>
                            <td class="px-6 py-4 font-medium text-slate-800 dark:text-slate-200">
                                {{ $report->user?->first_name ?? 'Không rõ' }}
                            </td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-400">
                                <i class="far fa-calendar-alt text-slate-400 mr-1.5"></i>
                                {{ $report->date?->format('d/m/Y') ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-400">
                                {{ $report->type }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-[10px] text-xs font-medium border
                                    @if($report->status === 'approved') 
                                        bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-800/50
                                    @elseif($report->status === 'rejected') 
                                        bg-red-50 text-red-700 border-red-200 dark:bg-red-900/20 dark:text-red-400 dark:border-red-800/50
                                    @else 
                                        bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-900/20 dark:text-amber-400 dark:border-amber-800/50
                                    @endif">
                                    {{ ucfirst($report->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="font-bold text-slate-800 dark:text-slate-200">{{ $report->points_earned }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('admin.reports.show', $report) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-[10px] bg-slate-100 text-slate-600 hover:bg-indigo-100 hover:text-indigo-600 dark:bg-slate-800 dark:text-slate-400 dark:hover:bg-indigo-900/30 dark:hover:text-indigo-400 transition-colors" title="Xem">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-slate-500 dark:text-slate-400">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-12 h-12 rounded-[10px] bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-3">
                                        <i class="fas fa-file-alt text-slate-400 text-xl"></i>
                                    </div>
                                    <p>Không tìm thấy báo cáo nào</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($reports->hasPages())
        <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30 flex flex-col md:flex-row justify-between items-center gap-4">
            <span class="text-sm text-slate-500 dark:text-slate-400">
                Hiển thị {{ $reports->count() }} báo cáo trên trang này
            </span>
            <div class="tailwind-pagination">
                {{ $reports->links('pagination::tailwind') }}
            </div>
        </div>
        @else
        <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30 text-sm text-slate-500 dark:text-slate-400 text-center md:text-left">
            Hiển thị tổng cộng {{ $reports->count() }} báo cáo
        </div>
        @endif
    </div>
@endsection
