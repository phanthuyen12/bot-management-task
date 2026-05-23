@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between gap-4">
        {{-- Mobile: Prev/Next only --}}
        <div class="flex justify-between flex-1 sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center px-4 py-2 text-sm font-medium text-slate-400 dark:text-slate-600 bg-white dark:bg-[#1e293b] border border-slate-200 dark:border-slate-700 rounded-[10px] cursor-not-allowed">
                    Trước
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-[#1e293b] border border-slate-200 dark:border-slate-700 rounded-[10px] hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                    Trước
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-[#1e293b] border border-slate-200 dark:border-slate-700 rounded-[10px] hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                    Tiếp
                </a>
            @else
                <span class="inline-flex items-center px-4 py-2 text-sm font-medium text-slate-400 dark:text-slate-600 bg-white dark:bg-[#1e293b] border border-slate-200 dark:border-slate-700 rounded-[10px] cursor-not-allowed">
                    Tiếp
                </span>
            @endif
        </div>

        {{-- Desktop: Full pagination --}}
        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between w-full">
            <div>
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    Trang <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $paginator->currentPage() }}</span>
                    / <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $paginator->lastPage() }}</span>
                    &mdash; Tổng <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $paginator->total() }}</span> bản ghi
                </p>
            </div>

            <div>
                <span class="inline-flex rounded-[10px] overflow-hidden shadow-sm border border-slate-200 dark:border-slate-700">
                    {{-- Prev Button --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="Previous" class="inline-flex items-center px-3 py-2 text-sm text-slate-400 dark:text-slate-600 bg-white dark:bg-[#1e293b] cursor-not-allowed border-r border-slate-200 dark:border-slate-700">
                            <i class="fas fa-chevron-left text-xs"></i>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Previous" class="inline-flex items-center px-3 py-2 text-sm text-slate-600 dark:text-slate-400 bg-white dark:bg-[#1e293b] hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors border-r border-slate-200 dark:border-slate-700">
                            <i class="fas fa-chevron-left text-xs"></i>
                        </a>
                    @endif

                    {{-- Page Numbers --}}
                    @foreach ($elements as $element)
                        @if (is_string($element))
                            <span aria-disabled="true" class="inline-flex items-center px-3 py-2 text-sm text-slate-400 dark:text-slate-500 bg-white dark:bg-[#1e293b] border-r border-slate-200 dark:border-slate-700 select-none">
                                &hellip;
                            </span>
                        @endif

                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-500/10 border-r border-slate-200 dark:border-slate-700">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="inline-flex items-center px-4 py-2 text-sm text-slate-600 dark:text-slate-400 bg-white dark:bg-[#1e293b] hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors border-r border-slate-200 dark:border-slate-700">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Button --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next" class="inline-flex items-center px-3 py-2 text-sm text-slate-600 dark:text-slate-400 bg-white dark:bg-[#1e293b] hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                            <i class="fas fa-chevron-right text-xs"></i>
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="Next" class="inline-flex items-center px-3 py-2 text-sm text-slate-400 dark:text-slate-600 bg-white dark:bg-[#1e293b] cursor-not-allowed">
                            <i class="fas fa-chevron-right text-xs"></i>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif
