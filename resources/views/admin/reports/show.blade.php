@extends('admin.layouts.app')

@section('title', 'Chi tiết báo cáo')
@section('header', 'Chi tiết báo cáo')
@section('subheader', 'Xem nội dung và điều chỉnh trạng thái báo cáo của thành viên')

@section('actions')
    <a href="{{ route('admin.reports.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-[#1e293b] border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 rounded-[10px] hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors text-sm font-medium shadow-sm">
        <i class="fas fa-arrow-left"></i> Quay lại
    </a>
@endsection

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Report Info -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-[#1e293b] rounded-[10px] shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30 flex justify-between items-start md:items-center flex-col md:flex-row gap-4">
                    <div>
                        <h4 class="text-lg font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                            <i class="fas fa-file-alt text-indigo-500"></i> Báo cáo #{{ $report->id }}
                        </h4>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                            Người gửi: <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $report->user?->first_name ?? 'Không rõ' }} {{ $report->user?->last_name }}</span>
                        </p>
                    </div>
                    <span class="inline-flex items-center px-3 py-1.5 rounded-[10px] text-sm font-medium border
                        @if($report->status === 'approved') 
                            bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-800/50
                        @elseif($report->status === 'rejected') 
                            bg-red-50 text-red-700 border-red-200 dark:bg-red-900/20 dark:text-red-400 dark:border-red-800/50
                        @else 
                            bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-900/20 dark:text-amber-400 dark:border-amber-800/50
                        @endif">
                        <i class="fas fa-circle text-[8px] mr-2"></i> {{ ucfirst($report->status) }}
                    </span>
                </div>

                <div class="p-6">
                    <!-- Quick Stats -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                        <div class="bg-slate-50 dark:bg-slate-800/50 rounded-[10px] p-4 border border-slate-100 dark:border-slate-800">
                            <span class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Ngày gửi</span>
                            <div class="text-lg font-bold text-slate-800 dark:text-slate-200">{{ $report->date?->format('d/m/Y') ?? 'N/A' }}</div>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-800/50 rounded-[10px] p-4 border border-slate-100 dark:border-slate-800">
                            <span class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Loại báo cáo</span>
                            <div class="text-lg font-bold text-slate-800 dark:text-slate-200 truncate" title="{{ $report->type }}">{{ $report->type }}</div>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-800/50 rounded-[10px] p-4 border border-slate-100 dark:border-slate-800">
                            <span class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Điểm nhận được</span>
                            <div class="text-lg font-bold text-indigo-600 dark:text-indigo-400">+{{ $report->points_earned }}</div>
                        </div>
                    </div>

                    <!-- Report Data -->
                    <div>
                        <h5 class="text-sm font-bold text-slate-800 dark:text-slate-200 mb-3 flex items-center gap-2">
                            <i class="fas fa-code text-slate-400"></i> Dữ liệu báo cáo (JSON)
                        </h5>
                        <div class="bg-[#0d1117] rounded-[10px] p-4 overflow-x-auto border border-slate-800 relative group">
                            <div class="absolute top-3 right-3 text-slate-500 text-xs font-medium">JSON</div>
                            <pre class="text-sm font-mono text-slate-300 leading-relaxed">{{ json_encode($report->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-[#1e293b] rounded-[10px] shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden sticky top-24">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30">
                    <h3 class="text-base font-semibold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                        <i class="fas fa-tasks text-slate-400"></i> Đánh giá báo cáo
                    </h3>
                </div>
                
                <div class="p-5">
                    <form method="POST" action="{{ route('admin.reports.review', $report) }}" class="space-y-5">
                        @csrf
                        
                        <div>
                            <label for="status" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Trạng thái</label>
                            <div class="relative">
                                <select name="status" id="status" class="block w-full appearance-none bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-[10px] px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors cursor-pointer">
                                    <option value="pending" {{ $report->status === 'pending' ? 'selected' : '' }}>Chờ duyệt (Pending)</option>
                                    <option value="approved" {{ $report->status === 'approved' ? 'selected' : '' }}>Chấp nhận (Approved)</option>
                                    <option value="rejected" {{ $report->status === 'rejected' ? 'selected' : '' }}>Từ chối (Rejected)</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="points_earned" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Điểm thưởng</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-star text-amber-500 text-xs"></i>
                                </div>
                                <input type="number" id="points_earned" name="points_earned" class="block w-full pl-10 pr-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-[10px] bg-slate-50 dark:bg-slate-800/50 text-slate-800 dark:text-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" value="{{ old('points_earned', $report->points_earned) }}" min="0">
                            </div>
                            <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">Điều chỉnh số điểm nhận được cho báo cáo này.</p>
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="w-full flex justify-center items-center gap-2 py-2.5 px-4 border border-transparent rounded-[10px] shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                <i class="fas fa-save"></i> Cập nhật đánh giá
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
