<!DOCTYPE html>
<html lang="vi" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin Panel') | Quản trị Bot</title>
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <!-- Alpine.js for interactions -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-[#f8fafc] text-slate-800 dark:bg-[#0f172a] dark:text-slate-200 antialiased font-sans" x-data="{ sidebarOpen: false, darkMode: false }" :class="{ 'dark': darkMode }">
    
    <div class="flex h-screen overflow-hidden">
        
        <!-- Mobile sidebar backdrop -->
        <div x-show="sidebarOpen" class="fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-sm lg:hidden" x-transition.opacity @click="sidebarOpen = false"></div>

        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-[#1e293b] border-r border-slate-200 dark:border-slate-800 transition-transform duration-300 lg:static lg:translate-x-0 flex flex-col shadow-sm">
            
            <!-- Logo -->
            <div class="flex items-center justify-center h-16 border-b border-slate-200 dark:border-slate-800">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-[10px] bg-indigo-600 text-white flex items-center justify-center shadow-sm">
                        <i class="fas fa-robot text-lg"></i>
                    </div>
                    <span class="text-lg font-bold tracking-tight text-slate-800 dark:text-white">Bot Admin</span>
                </a>
            </div>

            <!-- User Info -->
            @auth
            <div class="p-4 border-b border-slate-200 dark:border-slate-800">
                <div class="flex items-center gap-3">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=4f46e5&color=fff&size=64" class="w-10 h-10 rounded-[10px] shadow-sm" alt="User Image">
                    <div class="flex flex-col">
                        <span class="text-sm font-semibold text-slate-800 dark:text-slate-100 truncate w-36">{{ auth()->user()->name }}</span>
                        <span class="text-xs text-slate-500 dark:text-slate-400 truncate w-36">{{ auth()->user()->email }}</span>
                    </div>
                </div>
            </div>
            @endauth

            <!-- Navigation -->
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto custom-scrollbar">
                <p class="px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Main Menu</p>
                
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-[10px] transition-colors @if(request()->routeIs('admin.dashboard')) bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 font-medium @else text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-slate-200 @endif">
                    <i class="fas fa-tachometer-alt w-5 text-center"></i>
                    <span class="text-sm">Dashboard</span>
                </a>

                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-[10px] transition-colors @if(request()->routeIs('admin.users.*')) bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 font-medium @else text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-slate-200 @endif">
                    <i class="fas fa-users w-5 text-center"></i>
                    <span class="text-sm">Người dùng</span>
                </a>

                <a href="{{ route('admin.teams.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-[10px] transition-colors @if(request()->routeIs('admin.teams.*')) bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 font-medium @else text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-slate-200 @endif">
                    <i class="fas fa-layer-group w-5 text-center"></i>
                    <span class="text-sm">Teams</span>
                </a>

                <a href="{{ route('admin.reports.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-[10px] transition-colors @if(request()->routeIs('admin.reports.*')) bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 font-medium @else text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-slate-200 @endif">
                    <i class="fas fa-file-alt w-5 text-center"></i>
                    <span class="text-sm">Báo cáo</span>
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden relative">
            
            <!-- Header -->
            <header class="sticky top-0 z-30 bg-white dark:bg-[#1e293b] border-b border-slate-200 dark:border-slate-800 h-16 px-6 flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden w-8 h-8 rounded-[10px] bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition-colors">
                        <i class="fas fa-bars"></i>
                    </button>
                    
                    <div class="hidden md:block">
                        <h1 class="text-lg font-bold text-slate-800 dark:text-slate-100 tracking-tight">@yield('header', 'Dashboard')</h1>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <!-- Dark Mode Toggle -->
                    <button @click="darkMode = !darkMode" class="w-8 h-8 rounded-[10px] bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition-colors">
                        <i class="fas fa-sun" x-show="!darkMode"></i>
                        <i class="fas fa-moon" x-show="darkMode" style="display: none;"></i>
                    </button>

                    <!-- User Dropdown -->
                    @auth
                    <div class="relative" x-data="{ userMenuOpen: false }">
                        <button @click="userMenuOpen = !userMenuOpen" @click.away="userMenuOpen = false" class="flex items-center gap-2 focus:outline-none hover:bg-slate-50 dark:hover:bg-slate-800 py-1 px-2 rounded-[10px] transition-colors">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=4f46e5&color=fff&size=32" class="w-8 h-8 rounded-[10px]" alt="User">
                            <i class="fas fa-chevron-down text-[10px] text-slate-500"></i>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div x-show="userMenuOpen" x-transition.origin.top.right class="absolute right-0 mt-2 w-48 bg-white dark:bg-[#1e293b] rounded-[10px] shadow-lg py-2 border border-slate-200 dark:border-slate-800 z-50" style="display: none;">
                            <div class="px-4 py-2 border-b border-slate-100 dark:border-slate-800 mb-2">
                                <p class="text-sm font-medium text-slate-800 dark:text-slate-200 truncate">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ auth()->user()->email }}</p>
                            </div>
                            <form method="POST" action="{{ route('admin.logout') }}" class="w-full">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors flex items-center gap-2">
                                    <i class="fas fa-sign-out-alt"></i> Đăng xuất
                                </button>
                            </form>
                        </div>
                    </div>
                    @endauth
                </div>
            </header>

            <!-- Main Scrollable Area -->
            <main class="flex-1 overflow-y-auto p-4 md:p-6 custom-scrollbar">
                <div class="max-w-7xl mx-auto space-y-6">
                    
                    <!-- Mobile Header -->
                    <div class="md:hidden mb-4">
                        <h1 class="text-xl font-bold text-slate-800 dark:text-slate-100 tracking-tight">@yield('header', 'Dashboard')</h1>
                        @hasSection('subheader')
                            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">@yield('subheader')</p>
                        @endif
                    </div>

                    @hasSection('subheader')
                    <div class="hidden md:block mb-6">
                        <p class="text-sm text-slate-500 dark:text-slate-400">@yield('subheader')</p>
                    </div>
                    @endif

                    @if(session('success'))
                        <div class="bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-emerald-700 dark:text-emerald-400 px-4 py-3 rounded-[10px] shadow-sm flex items-center gap-3 mb-6" role="alert">
                            <i class="fas fa-check-circle"></i>
                            <span class="block sm:inline text-sm font-medium">{{ session('success') }}</span>
                        </div>
                    @endif

                    @yield('content')
                </div>
                
                <!-- Footer -->
                <footer class="max-w-7xl mx-auto mt-12 pt-6 border-t border-slate-200 dark:border-slate-800 flex flex-col md:flex-row items-center justify-between text-xs text-slate-500 dark:text-slate-400 pb-4">
                    <p>&copy; {{ date('Y') }} <strong>Quản lý Bot Nội bộ</strong>. All rights reserved.</p>
                </footer>
            </main>
        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: rgba(156, 163, 175, 0.3);
            border-radius: 10px;
        }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: rgba(75, 85, 99, 0.5);
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background-color: rgba(156, 163, 175, 0.5);
        }
    </style>

    @stack('scripts')
</body>
</html>
