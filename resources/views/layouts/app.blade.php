<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Smart Savings')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Inline script to prevent flash of wrong theme -->
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="h-full bg-[#FDFDFC] dark:bg-[#121212] text-[#1b1b18] dark:text-[#f3f3f3] font-sans antialiased transition-colors duration-200">

    <div class="flex h-full overflow-hidden">
        <!-- Collapsible Sidebar -->
        <aside id="sidebar" class="flex flex-col flex-shrink-0 h-full border-r border-[#19140015] dark:border-zinc-800 bg-white dark:bg-zinc-900 transition-all duration-300 w-64">
            <!-- Sidebar Header -->
            <div class="flex items-center justify-between p-5 border-b border-[#19140015] dark:border-zinc-800">
                <div class="flex items-center space-x-3 overflow-hidden">
                    <div class="bg-[#f53003] text-white p-2 rounded-lg flex-shrink-0">
                        <i class="fa-solid fa-wallet text-xl"></i>
                    </div>
                    <span id="logo-text" class="font-bold text-lg tracking-tight whitespace-nowrap transition-opacity duration-200">Smart Savings</span>
                </div>
            </div>

            <!-- Sidebar Nav Links -->
            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                <a href="{{ route('dashboard') }}" 
                   class="sidebar-link flex items-center px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-[#f53003]/10 text-[#f53003] font-semibold' : 'text-[#706f6c] dark:text-zinc-400 hover:bg-gray-100 dark:hover:bg-zinc-800' }}">
                    <i class="fa-solid fa-chart-pie text-lg w-6"></i>
                    <span class="sidebar-label ml-3 transition-opacity duration-200">Dashboard</span>
                </a>
                
                <a href="{{ route('transactions.index') }}" 
                   class="sidebar-link flex items-center px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('transactions.index') ? 'bg-[#f53003]/10 text-[#f53003] font-semibold' : 'text-[#706f6c] dark:text-zinc-400 hover:bg-gray-100 dark:hover:bg-zinc-800' }}">
                    <i class="fa-solid fa-clock-rotate-left text-lg w-6"></i>
                    <span class="sidebar-label ml-3 transition-opacity duration-200">Riwayat Transaksi</span>
                </a>

                @if(Auth::user()->role === 'superadmin')
                    <a href="{{ route('users.index') }}" 
                       class="sidebar-link flex items-center px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('users.index') ? 'bg-[#f53003]/10 text-[#f53003] font-semibold' : 'text-[#706f6c] dark:text-zinc-400 hover:bg-gray-100 dark:hover:bg-zinc-800' }}">
                        <i class="fa-solid fa-user-gear text-lg w-6"></i>
                        <span class="sidebar-label ml-3 transition-opacity duration-200">Hak Akses</span>
                    </a>
                @endif
            </nav>

            <!-- Sidebar Footer (Theme, User Profile, Logout) -->
            <div class="p-4 border-t border-[#19140015] dark:border-zinc-800 space-y-4">
                
                <!-- Dark Mode Toggle Button -->
                <button id="theme-toggle" class="w-full flex items-center justify-between px-4 py-2.5 rounded-xl border border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800 hover:bg-gray-100 dark:hover:bg-zinc-700/80 transition-all cursor-pointer">
                    <div class="flex items-center">
                        <i id="theme-toggle-icon" class="fa-solid text-lg w-6 text-[#706f6c] dark:text-zinc-400"></i>
                        <span id="theme-toggle-label" class="sidebar-label ml-3 text-sm text-[#706f6c] dark:text-zinc-400">Mode Terang</span>
                    </div>
                </button>

                <!-- User Profile & Logout -->
                <div class="flex items-center justify-between p-2 rounded-xl bg-gray-50 dark:bg-zinc-800/50">
                    <div class="flex items-center space-x-3 overflow-hidden">
                        <div class="w-9 h-9 rounded-lg bg-gray-200 dark:bg-zinc-700 flex items-center justify-center flex-shrink-0 text-gray-600 dark:text-gray-300 font-bold">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="sidebar-label overflow-hidden transition-opacity duration-200">
                            <h4 class="text-xs font-bold truncate">{{ Auth::user()->name }}</h4>
                            <p class="text-[10px] text-[#706f6c] dark:text-zinc-400 truncate">{{ Auth::user()->class }}</p>
                        </div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" class="flex-shrink-0">
                        @csrf
                        <button type="submit" class="text-[#706f6c] hover:text-rose-600 dark:text-zinc-400 dark:hover:text-rose-400 p-2 cursor-pointer transition-colors" title="Logout">
                            <i class="fa-solid fa-arrow-right-from-bracket text-lg"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Topbar (Hamburger Menu Toggle) -->
            <header class="h-16 flex items-center border-b border-[#19140015] dark:border-zinc-800 bg-white dark:bg-zinc-900 px-6">
                <button id="sidebar-toggle" class="text-[#706f6c] dark:text-zinc-400 hover:text-black dark:hover:text-white transition-colors cursor-pointer mr-4" title="Toggle Sidebar">
                    <i class="fa-solid fa-bars text-xl"></i>
                </button>
                <div class="flex-1"></div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto px-6 py-8">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Scripts for Sidebar & Theme Control -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const sidebar = document.getElementById('sidebar');
            const logoText = document.getElementById('logo-text');
            const sidebarLabels = document.querySelectorAll('.sidebar-label');
            const sidebarToggle = document.getElementById('sidebar-toggle');

            const themeToggle = document.getElementById('theme-toggle');
            const themeToggleIcon = document.getElementById('theme-toggle-icon');
            const themeToggleLabel = document.getElementById('theme-toggle-label');

            // --- Theme Handling ---
            const updateThemeUI = (isDark) => {
                if (isDark) {
                    themeToggleIcon.className = 'fa-solid fa-sun text-lg w-6 text-yellow-400';
                    themeToggleLabel.textContent = 'Mode Terang';
                } else {
                    themeToggleIcon.className = 'fa-solid fa-moon text-lg w-6 text-[#706f6c]';
                    themeToggleLabel.textContent = 'Mode Gelap';
                }
            };

            // Initial UI check
            updateThemeUI(document.documentElement.classList.contains('dark'));

            themeToggle.addEventListener('click', () => {
                const isDark = document.documentElement.classList.toggle('dark');
                localStorage.setItem('theme', isDark ? 'dark' : 'light');
                updateThemeUI(isDark);
            });

            // --- Sidebar Collapse Handling ---
            let isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';

            const applySidebarState = (collapsed) => {
                if (collapsed) {
                    sidebar.classList.remove('w-64');
                    sidebar.classList.add('w-20');
                    logoText.classList.add('opacity-0', 'pointer-events-none');
                    sidebarLabels.forEach(label => label.classList.add('opacity-0', 'pointer-events-none'));
                } else {
                    sidebar.classList.remove('w-20');
                    sidebar.classList.add('w-64');
                    logoText.classList.remove('opacity-0', 'pointer-events-none');
                    sidebarLabels.forEach(label => label.classList.remove('opacity-0', 'pointer-events-none'));
                }
            };

            // Initial Sidebar State
            applySidebarState(isCollapsed);

            sidebarToggle.addEventListener('click', () => {
                isCollapsed = !isCollapsed;
                localStorage.setItem('sidebarCollapsed', isCollapsed);
                applySidebarState(isCollapsed);
            });
        });
    </script>
</body>
</html>
