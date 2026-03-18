<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'مسالك النور')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Tajawal', sans-serif; }

        .nav-item {
            position: relative;
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            font-size: 0.875rem;
            color: #4b5563;
            border-radius: 8px;
            transition: all 0.15s ease;
            font-weight: 500;
            white-space: nowrap;
        }
        .nav-item:hover { color: #059669; background: #f0fdf4; }
        .nav-item.active {
            color: #047857;
            background: #ecfdf5;
            font-weight: 600;
        }
        .nav-item.active::after {
            content: '';
            position: absolute;
            bottom: -17px;
            right: 50%;
            transform: translateX(50%);
            width: 70%;
            height: 2.5px;
            background: #059669;
            border-radius: 2px 2px 0 0;
        }
        .nav-item.disabled { opacity: 0.4; cursor: not-allowed; pointer-events: none; }

        .user-dropdown { position: relative; }
        .user-dropdown-menu {
            display: none;
            position: absolute;
            top: calc(100% + 10px);
            left: 0;
            min-width: 180px;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.12);
            z-index: 100;
            overflow: hidden;
        }
        .user-dropdown:hover .user-dropdown-menu,
        .user-dropdown.open .user-dropdown-menu { display: block; }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-6px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .user-dropdown-menu { animation: slideDown 0.15s ease; }
    </style>
</head>
<body class="min-h-screen bg-slate-50">

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{--  HEADER                                                           --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <header class="sticky top-0 z-50 bg-white border-b border-gray-100"
            style="box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 4px 16px rgba(0,0,0,0.04);">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex items-center justify-between h-[62px]">

                {{-- ── LOGO ── --}}
                <div class="flex items-center gap-7">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 shrink-0 group">
                        <div class="relative w-9 h-9 shrink-0">
                            <div class="absolute inset-0 bg-emerald-600 rounded-xl rotate-6 opacity-20 group-hover:rotate-12 transition-transform"></div>
                            <div class="relative w-9 h-9 bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-xl flex items-center justify-center shadow-md">
                                <span class="text-white font-black text-base leading-none">م</span>
                            </div>
                        </div>
                        <div class="hidden sm:block">
                            <p class="font-black text-gray-900 text-[15px] leading-tight tracking-tight">مسالك النور</p>
                            <p class="text-[10px] text-emerald-600 font-medium leading-none">نظام إدارة الجمعية</p>
                        </div>
                    </a>

                    {{-- separator --}}
                    <div class="hidden md:block h-7 w-px bg-gray-100"></div>

                    {{-- ── NAV LINKS ── --}}
                    <nav class="hidden md:flex items-center gap-0.5">

                        <a href="{{ route('dashboard') }}"
                           class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            لوحة التحكم
                        </a>

                        <a href="{{ route('members.index') }}"
                           class="nav-item {{ request()->routeIs('members.*') ? 'active' : '' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M17 20h5v-2a4 4 0 00-5-3.87M9 20H4v-2a4 4 0 015-3.87m6-4.13a4 4 0 11-8 0 4 4 0 018 0zm6-4a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            الأعضاء
                        </a>

                        <a href="{{ route('donations.index') }}"
                           class="nav-item {{ request()->routeIs('donations.*') ? 'active' : '' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            التبرعات
                        </a>

                        @if (Auth::user()->role === 'admin')
                        <a href="{{ route('expenses.index') }}"
                           class="nav-item {{ request()->routeIs('expenses.*') ? 'active' : '' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M9 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M3 12l6.414 6.414a2 2 0 001.414.586H19a2 2 0 002-2V7a2 2 0 00-2-2h-8.172a2 2 0 00-1.414.586L3 12z"/>
                            </svg>
                            المصروفات
                        </a>

                        <a href="{{ route('users.index') }}"
                           class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            المستخدمون
                        </a>
                        @endif

                    </nav>
                </div>

                {{-- ── RIGHT: breadcrumb + user ── --}}
                <div class="flex items-center gap-3">

                    {{-- Breadcrumb --}}
                    @hasSection('breadcrumb')
                        <div class="hidden lg:flex items-center gap-1 text-xs text-gray-400 bg-gray-50 border border-gray-100 rounded-lg px-3 py-1.5">
                            <svg class="w-3.5 h-3.5 text-gray-300 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3"/>
                            </svg>
                            @yield('breadcrumb')
                        </div>
                    @endif

                    {{-- User dropdown --}}
                    <div class="user-dropdown">
                        <button class="flex items-center gap-2.5 rounded-xl px-3 py-1.5 hover:bg-gray-50 border border-transparent hover:border-gray-100 transition-all cursor-pointer"
                                onclick="this.closest('.user-dropdown').classList.toggle('open')">
                            {{-- Avatar --}}
                            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center shadow-sm shrink-0">
                                <span class="text-white font-bold text-sm">{{ mb_substr(Auth::user()->name, 0, 1) }}</span>
                            </div>
                            {{-- Name + role --}}
                            <div class="hidden sm:flex flex-col items-start leading-tight">
                                <span class="text-[13px] font-semibold text-gray-800 whitespace-nowrap">{{ Auth::user()->name }}</span>
                                <span class="text-[10px] font-medium
                                    {{ Auth::user()->role === 'admin' ? 'text-red-500' : 'text-emerald-600' }}">
                                    {{ Auth::user()->role === 'admin' ? 'مدير النظام' : (Auth::user()->role === 'representative' ? 'ممثل' : 'عضو') }}
                                </span>
                            </div>
                            {{-- chevron --}}
                            <svg class="w-3.5 h-3.5 text-gray-400 hidden sm:block shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        {{-- Dropdown menu --}}
                        <div class="user-dropdown-menu">
                            <div class="px-4 py-3 border-b border-gray-50">
                                <p class="text-xs font-semibold text-gray-800">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ Auth::user()->email }}</p>
                            </div>
                            <div class="p-1.5">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                            class="w-full flex items-center gap-2.5 px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded-lg transition-colors text-right">
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                        تسجيل الخروج
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Mobile menu btn --}}
                    <button id="mobile-menu-btn"
                            class="md:hidden p-2 rounded-lg hover:bg-gray-50 border border-gray-100 transition-colors">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>

            </div>

            {{-- Mobile nav --}}
            <div id="mobile-menu" class="hidden md:hidden border-t border-gray-100 py-3 space-y-0.5 pb-4">
                <a href="{{ route('dashboard') }}"
                   class="nav-item w-full {{ request()->routeIs('dashboard') ? 'active' : '' }}">لوحة التحكم</a>
                <a href="{{ route('members.index') }}"
                   class="nav-item w-full {{ request()->routeIs('members.*') ? 'active' : '' }}">الأعضاء</a>
                <a href="{{ route('donations.index') }}"
                   class="nav-item w-full {{ request()->routeIs('donations.*') ? 'active' : '' }}">التبرعات</a>
                @if (Auth::user()->role === 'admin')
                <a href="{{ route('expenses.index') }}"
                   class="nav-item w-full {{ request()->routeIs('expenses.*') ? 'active' : '' }}">المصروفات</a>
                <a href="{{ route('users.index') }}"
                   class="nav-item w-full {{ request()->routeIs('users.*') ? 'active' : '' }}">المستخدمون</a>
                @endif
            </div>
        </div>
    </header>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{--  CONTENT                                                          --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <main class="@yield('max-width', 'max-w-7xl') mx-auto px-6 py-8">

        @if (session('success'))
            <div class="mb-6 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3 text-sm">
                <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3 text-sm">
                <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-btn')?.addEventListener('click', () => {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });

        // Close user dropdown when clicking outside
        document.addEventListener('click', (e) => {
            document.querySelectorAll('.user-dropdown.open').forEach(el => {
                if (!el.contains(e.target)) el.classList.remove('open');
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
