@extends('admin.layouts.app')

@section('title', 'Teams')
@section('header', 'Quản lý Teams')
@section('subheader', 'Tạo và chỉnh sửa nhóm để phân bổ thành viên hiệu quả hơn')

@section('actions')
    <a href="{{ route('admin.teams.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-[10px] hover:bg-indigo-700 transition-colors text-sm font-medium shadow-sm">
        <i class="fas fa-plus"></i> Tạo team mới
    </a>
@endsection

@section('content')
    <div class="bg-white dark:bg-[#1e293b] rounded-[10px] shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden flex flex-col">
        <!-- Search & Filter Section -->
        <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30">
            <form method="GET" action="{{ route('admin.teams.index') }}" class="flex flex-col md:flex-row gap-3 items-start md:items-center">
                <div class="flex-1 w-full md:w-auto">
                    <div class="relative">
                        <input type="text" 
                               name="search" 
                               placeholder="Tìm kiếm theo tên, slug, hoặc mô tả..." 
                               value="{{ $search ?? '' }}"
                               class="w-full px-4 py-2 rounded-[10px] border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:border-indigo-500 dark:focus:border-indigo-400 transition-colors text-sm">
                        <i class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-xs"></i>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-[10px] hover:bg-indigo-700 transition-colors text-sm font-medium shadow-sm whitespace-nowrap">
                        <i class="fas fa-search mr-2"></i>Tìm kiếm
                    </button>
                    @if($search)
                        <a href="{{ route('admin.teams.index') }}" class="px-4 py-2 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-[10px] hover:bg-slate-300 dark:hover:bg-slate-600 transition-colors text-sm font-medium whitespace-nowrap">
                            <i class="fas fa-times mr-2"></i>Xóa bộ lọc
                        </a>
                    @endif
                </div>
            </form>
        </div>
        
        <!-- Results Info -->
        @if($search)
        <div class="px-5 py-3 border-b border-slate-200 dark:border-slate-800 bg-blue-50 dark:bg-blue-900/20">
            <p class="text-sm text-blue-700 dark:text-blue-300">
                <i class="fas fa-info-circle mr-2"></i>Kết quả tìm kiếm cho: <strong>{{ $search }}</strong> ({{ $teams->total() }} kết quả)
            </p>
        </div>
        @endif
        
        <!-- Header -->
        <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30">
            <h3 class="text-base font-semibold text-slate-800 dark:text-slate-100">Danh sách Teams</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 text-xs uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                        <th class="px-6 py-4 font-medium w-16 text-center">ID</th>
                        <th class="px-6 py-4 font-medium">Tên team</th>
                        <th class="px-6 py-4 font-medium">Slug</th>
                        <th class="px-6 py-4 font-medium">Team leader</th>
                        <th class="px-6 py-4 font-medium text-center">Thành viên</th>
                        <th class="px-6 py-4 font-medium">Mô tả</th>
                        <th class="px-6 py-4 font-medium text-center w-28">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($teams as $team)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-6 py-4 text-center text-slate-500 dark:text-slate-400">{{ $team->id }}</td>
                            <td class="px-6 py-4 font-medium text-slate-800 dark:text-slate-200">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-[10px] bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400 flex items-center justify-center">
                                        <i class="fas fa-layer-group text-xs"></i>
                                    </div>
                                    {{ $team->name }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-[10px] text-xs font-medium bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400">
                                    {{ $team->slug }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-400">
                                {{ $team->leader?->displayName() ?? 'Chưa cấu hình' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-[10px] text-xs font-medium bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-300">
                                    {{ $team->users_count }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-400">
                                {{ Str::limit($team->description, 80, '...') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('admin.teams.edit', $team) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-[10px] bg-slate-100 text-slate-600 hover:bg-indigo-100 hover:text-indigo-600 dark:bg-slate-800 dark:text-slate-400 dark:hover:bg-indigo-900/30 dark:hover:text-indigo-400 transition-colors" title="Sửa">
                                    <i class="fas fa-pen text-xs"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-slate-500 dark:text-slate-400">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-12 h-12 rounded-[10px] bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-3">
                                        <i class="fas fa-layer-group text-slate-400 text-xl"></i>
                                    </div>
                                    <p>Chưa có team nào được tạo.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($teams->total() > 0)
        <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <span class="text-sm text-slate-600 dark:text-slate-300 font-medium">
                    <i class="fas fa-list mr-2 text-indigo-600"></i>
                    Hiển thị <strong class="text-indigo-600 dark:text-indigo-400">{{ $teams->firstItem() ?? 0 }}</strong> đến <strong class="text-indigo-600 dark:text-indigo-400">{{ $teams->lastItem() ?? 0 }}</strong> trên <strong class="text-indigo-600 dark:text-indigo-400">{{ $teams->total() }}</strong> team
                </span>
                @if($teams->hasPages())
                <div class="tailwind-pagination">
                    {{ $teams->appends(request()->query())->links('pagination::tailwind') }}
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>
@endsection
