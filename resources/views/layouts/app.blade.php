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

        /* ── Sidebar ── */
        #sidebar {
            width: 256px;
            transition: width 0.25s ease, transform 0.25s ease;
        }
        #sidebar.collapsed {
            width: 68px;
        }
        #sidebar .sidebar-label,
        #sidebar .sidebar-group-label,
        #sidebar .sidebar-arrow {
            transition: opacity 0.15s ease, width 0.15s ease;
        }
        #sidebar.collapsed .sidebar-label,
        #sidebar.collapsed .sidebar-group-label,
        #sidebar.collapsed .sidebar-arrow {
            opacity: 0;
            width: 0;
            overflow: hidden;
            white-space: nowrap;
        }
        #sidebar.collapsed .sidebar-logo-text { display: none; }
        #sidebar .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            border-radius: 10px;
            font-size: 0.875rem;
            font-weight: 500;
            color: #4b5563;
            transition: background 0.15s, color 0.15s;
            white-space: nowrap;
            overflow: hidden;
        }
        #sidebar .nav-link:hover { background: #f0fdf4; color: #059669; }
        #sidebar .nav-link.active { background: #ecfdf5; color: #047857; font-weight: 700; }
        #sidebar .nav-link .nav-icon { width: 18px; height: 18px; shrink: 0; flex-shrink: 0; }
        #sidebar.collapsed .nav-link { justify-content: center; padding: 10px; }
        #sidebar.collapsed .nav-link .nav-icon { width: 20px; height: 20px; }

        /* ── Overlay ── */
        #sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.4);
            z-index: 39;
        }
        #sidebar-overlay.show { display: block; }

        /* ── Mobile ── */
        @media (max-width: 1024px) {
            #sidebar {
                position: fixed;
                top: 0;
                right: 0;
                height: 100%;
                z-index: 40;
                transform: translateX(100%);
                width: 256px !important;
            }
            #sidebar.mobile-open { transform: translateX(0); }
            #sidebar.collapsed { width: 256px !important; }
            #sidebar.collapsed .sidebar-label,
            #sidebar.collapsed .sidebar-group-label,
            #sidebar.collapsed .sidebar-arrow { opacity: 1; width: auto; overflow: visible; }
            #sidebar.collapsed .sidebar-logo-text { display: block; }
            #sidebar.collapsed .nav-link { justify-content: flex-start; padding: 8px 12px; }
            #sidebar.collapsed .nav-link .nav-icon { width: 18px; height: 18px; }
        }

        /* ── Tooltip on collapsed ── */
        #sidebar.collapsed .nav-link { position: relative; }
        #sidebar.collapsed .nav-link[data-tip]:hover::after {
            content: attr(data-tip);
            position: absolute;
            right: calc(100% + 8px);
            top: 50%;
            transform: translateY(-50%);
            background: #1f2937;
            color: white;
            font-size: 0.75rem;
            padding: 4px 10px;
            border-radius: 6px;
            white-space: nowrap;
            pointer-events: none;
            z-index: 99;
        }

        /* ── User dropdown ── */
        .user-dropdown { position: relative; }
        .user-dropdown-menu {
            display: none;
            position: absolute;
            top: calc(100% + 10px);
            left: 0;
            min-width: 200px;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.12);
            z-index: 100;
            overflow: hidden;
            animation: slideDown 0.15s ease;
        }
        .user-dropdown:hover .user-dropdown-menu,
        .user-dropdown.open .user-dropdown-menu { display: block; }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-6px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── Scrollbar sidebar ── */
        #sidebar-nav::-webkit-scrollbar { width: 3px; }
        #sidebar-nav::-webkit-scrollbar-track { background: transparent; }
        #sidebar-nav::-webkit-scrollbar-thumb { background: #d1fae5; border-radius: 99px; }
    </style>
</head>
<body class="min-h-screen bg-slate-50">

{{-- Mobile overlay --}}
<div id="sidebar-overlay" onclick="closeMobileSidebar()"></div>

<div class="flex min-h-screen">

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{--  SIDEBAR                                                          --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <aside id="sidebar" class="bg-white border-l border-gray-100 flex flex-col shrink-0"
           style="box-shadow: -1px 0 0 #f3f4f6, -4px 0 20px rgba(0,0,0,0.04);">

        {{-- Logo --}}
        <div class="flex items-center gap-3 px-4 h-[62px] border-b border-gray-100 shrink-0">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 min-w-0 group">
                <div class="relative w-9 h-9 shrink-0">
                    <div class="absolute inset-0 bg-emerald-600 rounded-xl rotate-6 opacity-20 group-hover:rotate-12 transition-transform"></div>
                    <div class="relative w-9 h-9 bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-xl flex items-center justify-center shadow-md">
                        <span class="text-white font-black text-base leading-none">م</span>
                    </div>
                </div>
                <div class="sidebar-logo-text min-w-0">
                    <p class="font-black text-gray-900 text-[15px] leading-tight tracking-tight whitespace-nowrap">مسالك النور</p>
                    <p class="text-[10px] text-emerald-600 font-medium leading-none whitespace-nowrap">نظام إدارة الجمعية</p>
                </div>
            </a>
        </div>

        {{-- Nav --}}
        <nav id="sidebar-nav" class="flex-1 overflow-y-auto px-3 py-4 space-y-5">

            @php
            function sidebarLink($route, $label, $icon, $activePattern = null) {
                $isActive = $activePattern
                    ? request()->routeIs($activePattern)
                    : request()->routeIs($route);
                $url = route($route);
                return compact('url','label','icon','isActive');
            }
            @endphp

            {{-- الرئيسية --}}
            <div>
                <p class="sidebar-group-label text-[10px] font-bold text-gray-400 uppercase tracking-widest px-3 mb-1.5 overflow-hidden whitespace-nowrap">الرئيسية</p>
                @php $link = sidebarLink('dashboard','لوحة التحكم','M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'); @endphp
                <a href="{{ $link['url'] }}" data-tip="{{ $link['label'] }}"
                   class="nav-link {{ $link['isActive'] ? 'active' : '' }}">
                    <svg class="nav-icon shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $link['icon'] }}"/></svg>
                    <span class="sidebar-label">{{ $link['label'] }}</span>
                </a>
                @php $link = sidebarLink('statistics','الإحصائيات','M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'); @endphp
                <a href="{{ $link['url'] }}" data-tip="{{ $link['label'] }}"
                   class="nav-link {{ $link['isActive'] ? 'active' : '' }}">
                    <svg class="nav-icon shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $link['icon'] }}"/></svg>
                    <span class="sidebar-label">{{ $link['label'] }}</span>
                </a>
            </div>

            {{-- الأعضاء --}}
            <div>
                <p class="sidebar-group-label text-[10px] font-bold text-gray-400 uppercase tracking-widest px-3 mb-1.5 overflow-hidden whitespace-nowrap">الأعضاء</p>
                <div class="space-y-0.5">
                    @foreach([
                        ['members.index',       'الأعضاء',              'M17 20h5v-2a4 4 0 00-5-3.87M9 20H4v-2a4 4 0 015-3.87m6-4.13a4 4 0 11-8 0 4 4 0 018 0zm6-4a3 3 0 11-6 0 3 3 0 016 0z', 'members.index'],
                        ['members.map',         'خريطة الأعضاء',       'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z'],
                        ['members.create',      'إضافة عضو',            'M18 9v3m0 0v3m0-3h3m-3 0h-3m-5-3a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z'],
                        ['members.duplicates',  'التكرارات',            'M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z'],
                        ['members.import.show', 'استيراد Excel',        'M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                        ['members.bulk-amount',       'إضافة وانقاص النقاط',     'M20 12H4'],
                        ['members.bulk-payments',     'الدفعات الجماعية',  'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'],
                        ['members.payment-batches',  'سجل الدفعات',       'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                        ['members.fv-reduction',      'رفع وتخفيض المبلغ', 'M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z'],
                        ['members.score-adjustments', 'تعديلات النقاط',    'M3 6h18M3 12h18M3 18h18'],
                        ['members.score-equalizer',  'تسوية النقاط',      'M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4'],
                        ['members.score-manager',   'إدارة النقاط',      'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                        ['member-images.index', 'أرشيف الصور',          'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z'],
                        ['age-statistics.index','إحصائيات الأعمار',    'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                    ] as $item)
                    @php [$r, $lbl, $ico] = $item; $pat = $item[3] ?? $r; $active = request()->routeIs($pat); @endphp
                    <a href="{{ route($r) }}" data-tip="{{ $lbl }}"
                       class="nav-link {{ $active ? 'active' : '' }}">
                        <svg class="nav-icon shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ico }}"/></svg>
                        <span class="sidebar-label">{{ $lbl }}</span>
                    </a>
                    @endforeach
                </div>
            </div>

            {{-- الجولات الميدانية --}}
            <div>
                <p class="sidebar-group-label text-[10px] font-bold text-gray-400 uppercase tracking-widest px-3 mb-1.5 overflow-hidden whitespace-nowrap">الجولات الميدانية</p>
                <div class="space-y-0.5">
                    <a href="{{ route('field-visits.with-amounts') }}" data-tip="الجولات الميدانية"
                       class="nav-link {{ request()->routeIs('field-visits.with-amounts') ? 'active' : '' }}">
                        <svg class="nav-icon shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span class="sidebar-label">الجولات الميدانية</span>
                    </a>
                </div>
            </div>

            {{-- المالية --}}
            <div>
                <p class="sidebar-group-label text-[10px] font-bold text-gray-400 uppercase tracking-widest px-3 mb-1.5 overflow-hidden whitespace-nowrap">الموظفون</p>
                @if(Auth::user()->role === 'admin')
                <div class="space-y-0.5 mb-3">
                    <a href="{{ route('employees.index') }}" data-tip="الموظفون"
                       class="nav-link {{ request()->routeIs('employees.*') ? 'active' : '' }}">
                        <svg class="nav-icon shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span class="sidebar-label">الموظفون</span>
                    </a>
                </div>
                @endif
                <p class="sidebar-group-label text-[10px] font-bold text-gray-400 uppercase tracking-widest px-3 mb-1.5 overflow-hidden whitespace-nowrap">المالية</p>
                <div class="space-y-0.5">
                    @foreach([
                        ['donations.index', 'التبرعات',  'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'donations.*', null],
                        ['expenses.index',  'المصروفات', 'M9 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M3 12l6.414 6.414a2 2 0 001.414.586H19a2 2 0 002-2V7a2 2 0 00-2-2h-8.172a2 2 0 00-1.414.586L3 12z', 'expenses.*', 'admin'],
                        ['budget.index',    'الميزانية', 'M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3', 'budget.*', null],
                    ] as [$r, $lbl, $ico, $pat, $role])
                    @if($role === null || Auth::user()->role === $role)
                    @php $active = request()->routeIs($pat ?? $r); @endphp
                    <a href="{{ route($r) }}" data-tip="{{ $lbl }}"
                       class="nav-link {{ $active ? 'active' : '' }}">
                        <svg class="nav-icon shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ico }}"/></svg>
                        <span class="sidebar-label">{{ $lbl }}</span>
                    </a>
                    @endif
                    @endforeach
                </div>
            </div>

            {{-- المراجعة --}}
            <div>
                <p class="sidebar-group-label text-[10px] font-bold text-gray-400 uppercase tracking-widest px-3 mb-1.5 overflow-hidden whitespace-nowrap">المراجعة</p>
                <div class="space-y-0.5">
                    @if(Auth::user()->role === 'admin')
                    <a href="{{ route('pending-changes.index') }}" data-tip="طلبات التعديل"
                       class="nav-link {{ request()->routeIs('pending-changes.index') ? 'active' : '' }}">
                        <svg class="nav-icon shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        <span class="sidebar-label flex items-center gap-2">
                            طلبات التعديل
                            @php $pc = \App\Models\PendingChange::where('status','pending')->count(); @endphp
                            @if($pc)<span class="bg-amber-100 text-amber-700 text-[10px] font-bold px-1.5 py-0.5 rounded-full leading-none">{{ $pc }}</span>@endif
                        </span>
                    </a>
                    @endif
                    <a href="{{ route('pending-changes.my') }}" data-tip="طلباتي"
                       class="nav-link {{ request()->routeIs('pending-changes.my') ? 'active' : '' }}">
                        <svg class="nav-icon shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span class="sidebar-label">طلباتي</span>
                    </a>
                    <a href="{{ route('activity-logs.index') }}" data-tip="سجل النشاط"
                       class="nav-link {{ request()->routeIs('activity-logs.*') ? 'active' : '' }}">
                        <svg class="nav-icon shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        <span class="sidebar-label">سجل النشاط</span>
                    </a>
                    <a href="{{ route('archive.index') }}" data-tip="الأرشيف"
                       class="nav-link {{ request()->routeIs('archive.*') ? 'active' : '' }}">
                        <svg class="nav-icon shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                        <span class="sidebar-label">الأرشيف</span>
                    </a>
                </div>
            </div>

            @if(Auth::user()->role === 'admin')
            {{-- مراجعة الدفع --}}
            <div>
                <p class="sidebar-group-label text-[10px] font-bold text-gray-400 uppercase tracking-widest px-3 mb-1.5 overflow-hidden whitespace-nowrap">مراجعة الدفع</p>
                <div class="space-y-0.5">
                    <a href="{{ route('payment-review.index') }}" data-tip="مراجعة الدفع AI"
                       class="nav-link {{ request()->routeIs('payment-review.index') ? 'active' : '' }}">
                        <svg class="nav-icon shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        <span class="sidebar-label">مراجعة الدفع AI</span>
                    </a>
                    <a href="{{ route('payment-review.duplicate-ibans') }}" data-tip="تكرار الآيبانات"
                       class="nav-link {{ request()->routeIs('payment-review.duplicate-ibans') ? 'active' : '' }}">
                        <svg class="nav-icon shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        <span class="sidebar-label">تكرار الآيبانات</span>
                    </a>
                </div>
            </div>

            {{-- الإعدادات --}}
            <div>
                <p class="sidebar-group-label text-[10px] font-bold text-gray-400 uppercase tracking-widest px-3 mb-1.5 overflow-hidden whitespace-nowrap">الإعدادات</p>
                <div class="space-y-0.5">
                    @foreach([
                        ['associations.index',         'الجمعيات',              'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                        ['marital-statuses.index',     'الحالات الاجتماعية',   'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z'],
                        ['verification-statuses.index','حالات التحقق',          'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z'],
                        ['final-statuses.index',       'الحالات النهائية',      'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z'],
                        ['field-visit-statuses.index', 'حالات الجولات الميدانية', 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z'],
                        ['house-types.index',          'أنواع البيوت',            'M3 9.75L12 3l9 6.75V21H15v-6H9v6H3V9.75z'],
                        ['house-conditions.index',     'حالات البيوت',            'M3 9.75L12 3l9 6.75V21H15v-6H9v6H3V9.75z'],
                        ['housing-statuses.index',     'أوضاع السكن',             'M3 9.75L12 3l9 6.75V21H15v-6H9v6H3V9.75z'],
                        ['regions.index',   'المناطق',     'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z'],
                        ['sectors.index',   'القطاعات',    'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                        ['delegates.index', 'المندوبون',   'M17 20h5v-2a4 4 0 00-5-3.87M9 20H4v-2a4 4 0 015-3.87m6-4.13a4 4 0 11-8 0 4 4 0 018 0zm6-4a3 3 0 11-6 0 3 3 0 016 0z'],
                        ['users.index',     'المستخدمون',  'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
                    ] as [$r, $lbl, $ico])
                    <a href="{{ route($r) }}" data-tip="{{ $lbl }}"
                       class="nav-link {{ request()->routeIs($r) ? 'active' : '' }}">
                        <svg class="nav-icon shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ico }}"/></svg>
                        <span class="sidebar-label">{{ $lbl }}</span>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Account section — visible to all users --}}
            <div>
                <p class="sidebar-group-label text-[10px] font-bold text-gray-400 uppercase tracking-widest px-3 mb-1.5 overflow-hidden whitespace-nowrap">الحساب</p>
                <div class="space-y-0.5">
                    <a href="{{ route('password.change') }}" data-tip="تغيير كلمة المرور"
                       class="nav-link {{ request()->routeIs('password.change') ? 'active' : '' }}">
                        <svg class="nav-icon shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                        </svg>
                        <span class="sidebar-label">تغيير كلمة المرور</span>
                    </a>
                </div>
            </div>

        </nav>

        {{-- Collapse toggle (desktop) --}}
        <div class="hidden lg:flex border-t border-gray-100 px-3 py-3 shrink-0">
            <button onclick="toggleSidebar()" title="طي القائمة"
                    class="w-full flex items-center justify-center gap-2 text-xs text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg px-3 py-2 transition-colors">
                <svg id="collapse-icon" class="w-4 h-4 shrink-0 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                </svg>
                <span class="sidebar-label text-xs">طي القائمة</span>
            </button>
        </div>

    </aside>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{--  MAIN AREA                                                        --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <div class="flex-1 flex flex-col min-w-0">

        {{-- Header --}}
        <header class="sticky top-0 z-30 bg-white border-b border-gray-100 shrink-0"
                style="box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
            <div class="flex items-center justify-between h-[62px] px-5 gap-3">

                {{-- Left: Toggle + Breadcrumb --}}
                <div class="flex items-center gap-3 min-w-0">
                    {{-- Mobile toggle --}}
                    <button onclick="openMobileSidebar()"
                            class="lg:hidden p-2 rounded-lg hover:bg-gray-50 border border-gray-100 transition-colors shrink-0">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>

                    {{-- Breadcrumb --}}
                    @hasSection('breadcrumb')
                    <div class="flex items-center gap-1 text-sm text-gray-500 min-w-0">
                        <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-emerald-600 transition-colors shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3"/>
                            </svg>
                        </a>
                        <svg class="w-3.5 h-3.5 text-gray-300 shrink-0 rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                        <span class="font-semibold text-gray-700 truncate">@yield('breadcrumb')</span>
                    </div>
                    @else
                    <span class="text-sm font-semibold text-gray-700">@yield('title', 'مسالك النور')</span>
                    @endif
                </div>

                {{-- Right: User --}}
                <div class="user-dropdown shrink-0">
                    <button class="flex items-center gap-2.5 rounded-xl px-3 py-1.5 hover:bg-gray-50 border border-transparent hover:border-gray-100 transition-all cursor-pointer"
                            onclick="this.closest('.user-dropdown').classList.toggle('open')">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center shadow-sm shrink-0">
                            <span class="text-white font-bold text-sm">{{ mb_substr(Auth::user()->name, 0, 1) }}</span>
                        </div>
                        <div class="hidden sm:flex flex-col items-start leading-tight">
                            <span class="text-[13px] font-semibold text-gray-800 whitespace-nowrap">{{ Auth::user()->name }}</span>
                            <span class="text-[10px] font-medium {{ Auth::user()->role === 'admin' ? 'text-red-500' : 'text-emerald-600' }}">
                                {{ Auth::user()->role === 'admin' ? 'مدير النظام' : (Auth::user()->role === 'representative' ? 'ممثل' : 'عضو') }}
                            </span>
                        </div>
                        <svg class="w-3.5 h-3.5 text-gray-400 hidden sm:block shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div class="user-dropdown-menu">
                        <div class="px-4 py-3 border-b border-gray-50">
                            <p class="text-xs font-semibold text-gray-800">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ Auth::user()->email }}</p>
                        </div>
                        <div class="p-1.5">
                            <a href="{{ route('password.change') }}"
                               class="flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition-colors {{ request()->routeIs('password.change') ? 'bg-gray-50 font-semibold' : '' }}">
                                <svg class="w-4 h-4 shrink-0 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                </svg>
                                تغيير كلمة المرور
                            </a>
                            <div class="my-1 border-t border-gray-50"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="w-full flex items-center gap-2.5 px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded-lg transition-colors text-right">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    تسجيل الخروج
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </header>

        {{-- Content --}}
        <main class="flex-1 p-6 min-w-0">

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

            @if (session('pending'))
            <div class="mb-6 flex items-center gap-3 bg-amber-50 border border-amber-200 text-amber-800 rounded-xl px-4 py-3 text-sm">
                <svg class="w-5 h-5 text-amber-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('pending') }}
            </div>
            @endif

            @yield('content')
        </main>

    </div>
</div>

<script>
const sidebar  = document.getElementById('sidebar');
const overlay  = document.getElementById('sidebar-overlay');
const colIcon  = document.getElementById('collapse-icon');

// Desktop: restore collapsed state
if (localStorage.getItem('sidebarCollapsed') === '1') {
    sidebar.classList.add('collapsed');
    if (colIcon) colIcon.style.transform = 'rotate(180deg)';
}

function toggleSidebar() {
    const isCollapsed = sidebar.classList.toggle('collapsed');
    localStorage.setItem('sidebarCollapsed', isCollapsed ? '1' : '0');
    if (colIcon) colIcon.style.transform = isCollapsed ? 'rotate(180deg)' : '';
}

function openMobileSidebar() {
    sidebar.classList.add('mobile-open');
    overlay.classList.add('show');
}

function closeMobileSidebar() {
    sidebar.classList.remove('mobile-open');
    overlay.classList.remove('show');
}

// Close user dropdown when clicking outside
document.addEventListener('click', (e) => {
    document.querySelectorAll('.user-dropdown.open').forEach(el => {
        if (!el.contains(e.target)) el.classList.remove('open');
    });
});
</script>
@stack('scripts')

{{-- ===== Global Delete Confirmation Modal ===== --}}
<div id="del-modal" class="hidden fixed inset-0 z-[9999] flex items-center justify-center p-4" role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" id="del-modal-backdrop"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6">
        <div class="flex flex-col items-center text-center">
            <div class="w-14 h-14 bg-red-100 rounded-2xl flex items-center justify-center mb-4">
                <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h3 class="text-lg font-black text-gray-900 mb-1.5">تأكيد الحذف</h3>
            <p id="del-modal-msg" class="text-gray-500 text-sm leading-relaxed mb-6"></p>
        </div>
        <div class="flex gap-3">
            <button id="del-modal-cancel" type="button"
                    class="flex-1 px-4 py-2.5 rounded-xl border border-gray-200 text-gray-600 text-sm font-semibold hover:bg-gray-50 transition-colors">
                إلغاء
            </button>
            <button id="del-modal-ok" type="button"
                    class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-bold transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                تأكيد الحذف
            </button>
        </div>
    </div>
</div>
<script>
(function () {
    var pendingForm = null;
    var modal    = document.getElementById('del-modal');
    var msgEl    = document.getElementById('del-modal-msg');
    var okBtn    = document.getElementById('del-modal-ok');
    var cancelBtn= document.getElementById('del-modal-cancel');
    var backdrop = document.getElementById('del-modal-backdrop');

    function showModal(form) {
        pendingForm = form;
        var name   = (form.dataset.confirmName || '').trim();
        var custom = (form.dataset.confirm     || '').trim();
        if (custom) {
            msgEl.innerHTML = custom + '<br><span class="text-xs text-red-500 mt-1 block">لا يمكن التراجع عن هذا الإجراء.</span>';
        } else if (name) {
            msgEl.innerHTML = 'هل أنت متأكد من حذف <span class="font-bold text-gray-900">' + name + '</span>؟<br><span class="text-xs text-red-500 mt-1 block">لا يمكن التراجع عن هذا الإجراء.</span>';
        } else {
            msgEl.innerHTML = 'هل أنت متأكد من الحذف؟<br><span class="text-xs text-red-500 mt-1 block">لا يمكن التراجع عن هذا الإجراء.</span>';
        }
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function hideModal() {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
        pendingForm = null;
    }

    // Intercept all DELETE form submissions (capture phase = before onsubmit)
    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (form._delConfirmed) return;
        var mi = form.querySelector('input[name="_method"]');
        if (!mi || mi.value.toUpperCase() !== 'DELETE') return;
        e.preventDefault();
        e.stopImmediatePropagation();
        showModal(form);
    }, true);

    okBtn.addEventListener('click', function () {
        var formToSubmit = pendingForm;
        hideModal();
        if (formToSubmit) {
            formToSubmit._delConfirmed = true;
            formToSubmit.submit();
        }
    });

    cancelBtn.addEventListener('click', hideModal);
    backdrop.addEventListener('click', hideModal);
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) hideModal();
    });
})();
</script>
</body>
</html>
