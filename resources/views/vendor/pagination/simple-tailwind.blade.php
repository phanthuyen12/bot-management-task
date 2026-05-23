@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between">
        @if ($paginator->onFirstPage())
            <span class="inline-flex items-center gap-2 px-4 py-2 text-sm text-slate-400 dark:text-slate-600 bg-white dark:bg-[#1e293b] border border-slate-200 dark:border-slate-700 rounded-[10px] cursor-not-allowed">
                <i class="fas fa-chevron-left text-xs"></i> Trước
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm text-slate-700 dark:text-slate-300 bg-white dark:bg-[#1e293b] border border-slate-200 dark:border-slate-700 rounded-[10px] hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                <i class="fas fa-chevron-left text-xs"></i> Trước
            </a>
        @endif

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm text-slate-700 dark:text-slate-300 bg-white dark:bg-[#1e293b] border border-slate-200 dark:border-slate-700 rounded-[10px] hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                Tiếp <i class="fas fa-chevron-right text-xs"></i>
            </a>
        @else
            <span class="inline-flex items-center gap-2 px-4 py-2 text-sm text-slate-400 dark:text-slate-600 bg-white dark:bg-[#1e293b] border border-slate-200 dark:border-slate-700 rounded-[10px] cursor-not-allowed">
                Tiếp <i class="fas fa-chevron-right text-xs"></i>
            </span>
        @endif
    </nav>
@endif
