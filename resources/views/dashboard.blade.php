@extends('layouts.app')

@section('title', 'لوحة التحكم — مسالك النور')

@section('content')

{{-- Hero --}}
<div class="relative bg-gradient-to-l from-emerald-600 via-emerald-500 to-teal-500 rounded-3xl p-7 mb-8 overflow-hidden shadow-lg">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute -top-8 -left-8 w-48 h-48 bg-white rounded-full"></div>
        <div class="absolute -bottom-12 left-24 w-64 h-64 bg-white rounded-full"></div>
        <div class="absolute top-6 right-16 w-24 h-24 bg-white rounded-full"></div>
    </div>
    <div class="relative">
        <p class="text-emerald-100 text-sm font-medium mb-1">مرحباً، {{ auth()->user()->name }}</p>
        <h1 class="text-3xl font-black text-white mb-1">لوحة التحكم</h1>
        <p class="text-emerald-200 text-sm">جمعية مسالك النور — نظام إدارة الأعضاء</p>
    </div>
</div>

{{-- Stats --}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-8">
    <a href="{{ route('members.index') }}" class="group relative bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-2xl p-6 shadow-md overflow-hidden hover:shadow-lg transition-shadow">
        <div class="absolute -bottom-4 -left-4 w-20 h-20 bg-white/10 rounded-full"></div>
        <div class="absolute -top-4 -right-4 w-16 h-16 bg-white/10 rounded-full"></div>
        <div class="relative">
            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mb-3">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-5-3.87M9 20H4v-2a4 4 0 015-3.87m6-4.13a4 4 0 11-8 0 4 4 0 018 0zm6-4a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <p class="text-emerald-100 text-sm font-medium mb-1">إجمالي الأعضاء</p>
            <p class="text-4xl font-black text-white">{{ \App\Models\Member::count() }}</p>
            <p class="text-emerald-200 text-xs mt-1 group-hover:text-white transition-colors">عرض الكل ←</p>
        </div>
    </a>

    <a href="{{ route('donations.index') }}" class="group relative bg-gradient-to-br from-blue-500 to-blue-700 rounded-2xl p-6 shadow-md overflow-hidden hover:shadow-lg transition-shadow">
        <div class="absolute -bottom-4 -left-4 w-20 h-20 bg-white/10 rounded-full"></div>
        <div class="absolute -top-4 -right-4 w-16 h-16 bg-white/10 rounded-full"></div>
        <div class="relative">
            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mb-3">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p class="text-blue-100 text-sm font-medium mb-1">تبرعات هذا الشهر</p>
            <p class="text-4xl font-black text-white">{{ \App\Models\Donation::forMonth(now()->year, now()->month)->where('status','paid')->count() }}</p>
            <p class="text-blue-200 text-xs mt-1 group-hover:text-white transition-colors">عرض الكل ←</p>
        </div>
    </a>

    <a href="{{ route('expenses.index') }}" class="group relative bg-gradient-to-br from-orange-400 to-orange-600 rounded-2xl p-6 shadow-md overflow-hidden hover:shadow-lg transition-shadow">
        <div class="absolute -bottom-4 -left-4 w-20 h-20 bg-white/10 rounded-full"></div>
        <div class="absolute -top-4 -right-4 w-16 h-16 bg-white/10 rounded-full"></div>
        <div class="relative">
            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mb-3">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M3 12l6.414 6.414a2 2 0 001.414.586H19a2 2 0 002-2V7a2 2 0 00-2-2h-8.172a2 2 0 00-1.414.586L3 12z"/>
                </svg>
            </div>
            <p class="text-orange-100 text-sm font-medium mb-1">إجمالي المصروفات</p>
            <p class="text-4xl font-black text-white">{{ \App\Models\Expense::count() }}</p>
            <p class="text-orange-200 text-xs mt-1 group-hover:text-white transition-colors">عرض الكل ←</p>
        </div>
    </a>

    <a href="{{ route('member-images.index') }}" class="group relative bg-gradient-to-br from-violet-500 to-purple-700 rounded-2xl p-6 shadow-md overflow-hidden hover:shadow-lg transition-shadow">
        <div class="absolute -bottom-4 -left-4 w-20 h-20 bg-white/10 rounded-full"></div>
        <div class="absolute -top-4 -right-4 w-16 h-16 bg-white/10 rounded-full"></div>
        <div class="relative">
            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mb-3">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <p class="text-violet-100 text-sm font-medium mb-1">إجمالي الصور والمستندات</p>
            <p class="text-4xl font-black text-white">{{ \App\Models\MemberImage::count() }}</p>
            <p class="text-violet-200 text-xs mt-1 group-hover:text-white transition-colors">أرشيف الاضبارات ←</p>
        </div>
    </a>

    @if($allPendingCount)
    <a href="{{ route('pending-changes.index') }}" class="group relative bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl p-6 shadow-md overflow-hidden hover:shadow-lg transition-shadow md:col-span-4">
        <div class="absolute -bottom-4 -left-4 w-20 h-20 bg-white/10 rounded-full"></div>
        <div class="absolute -top-4 -right-4 w-16 h-16 bg-white/10 rounded-full"></div>
        <div class="relative flex items-center gap-5">
            <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center shrink-0">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
            </div>
            <div>
                <p class="text-amber-100 text-sm font-medium mb-0.5">طلبات تعديل تنتظر مراجعتك</p>
                <p class="text-5xl font-black text-white leading-none">{{ $allPendingCount }}</p>
                <p class="text-amber-200 text-sm mt-1 group-hover:text-white transition-colors">اضغط للمراجعة والموافقة ←</p>
            </div>
        </div>
    </a>
    @endif
</div>

{{-- Stats summary + link to statistics --}}
<a href="{{ route('statistics') }}"
   class="group flex items-center justify-between bg-white border border-indigo-100 rounded-2xl shadow-sm px-5 py-4 mb-8 hover:shadow-md hover:border-indigo-200 transition-all">
    <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-xl bg-indigo-100 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
        </div>
        <div>
            <p class="text-sm font-bold text-gray-800">الإحصائيات التفصيلية</p>
            <p class="text-xs text-gray-400">{{ number_format($totalMembers) }} عضو — {{ number_format((float)$totalEstimatedAmount, 0, '.', ',') }} ل.س مجموع مقدر</p>
        </div>
    </div>
    <span class="text-sm font-semibold text-indigo-600 group-hover:text-indigo-700 transition-colors">عرض الإحصائيات ←</span>
</a>

{{-- ══ User Monitoring (admin only) ══ --}}
@if(auth()->user()?->role === 'admin')
@php
    $actionMeta = [
        'created' => ['label'=>'إضافة',  'bg'=>'bg-blue-50',    'text'=>'text-blue-700',   'border'=>'border-blue-200',   'dot'=>'bg-blue-500',   'icon'=>'M12 4v16m8-8H4'],
        'updated' => ['label'=>'تعديل',  'bg'=>'bg-amber-50',   'text'=>'text-amber-700',  'border'=>'border-amber-200',  'dot'=>'bg-amber-500',  'icon'=>'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'],
        'deleted' => ['label'=>'حذف',   'bg'=>'bg-red-50',     'text'=>'text-red-700',    'border'=>'border-red-200',    'dot'=>'bg-red-500',    'icon'=>'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16'],
        'login'   => ['label'=>'دخول',  'bg'=>'bg-emerald-50', 'text'=>'text-emerald-700','border'=>'border-emerald-200','dot'=>'bg-emerald-500','icon'=>'M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1'],
        'logout'  => ['label'=>'خروج',  'bg'=>'bg-gray-50',    'text'=>'text-gray-500',   'border'=>'border-gray-200',   'dot'=>'bg-gray-400',   'icon'=>'M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1'],
        'viewed'  => ['label'=>'عرض',   'bg'=>'bg-purple-50',  'text'=>'text-purple-700', 'border'=>'border-purple-200', 'dot'=>'bg-purple-400', 'icon'=>'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z'],
    ];
    $totalActiveToday = $usersActivity->filter(fn($u) => ($todayBreakdown[$u->id] ?? collect())->sum() > 0)->count();
    $totalEditsToday  = $todayBreakdown->map->sum()->sum();
@endphp

<div class="mb-8">

    {{-- Section header --}}
    <div class="flex items-center gap-3 mb-5">
        <div class="w-8 h-8 rounded-xl bg-violet-100 flex items-center justify-center shrink-0">
            <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
        </div>
        <div>
            <h2 class="text-base font-black text-gray-800">مراقبة المستخدمين</h2>
            <p class="text-xs text-gray-400">{{ now()->translatedFormat('l، j F Y') }}</p>
        </div>
        <div class="flex-1 h-px bg-gray-100 mx-2"></div>

        {{-- Summary pills --}}
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 text-xs font-semibold bg-violet-50 text-violet-700 border border-violet-100 px-3 py-1.5 rounded-full">
                <span class="w-1.5 h-1.5 bg-violet-500 rounded-full"></span>
                {{ $usersActivity->count() }} مستخدم
            </span>
            <span class="inline-flex items-center gap-1.5 text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100 px-3 py-1.5 rounded-full">
                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                {{ $totalActiveToday }} نشط اليوم
            </span>
            <span class="inline-flex items-center gap-1.5 text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100 px-3 py-1.5 rounded-full">
                {{ number_format($totalEditsToday) }} عملية اليوم
            </span>
        </div>

        <a href="{{ route('activity-logs.index') }}"
           class="shrink-0 inline-flex items-center gap-1.5 text-xs font-semibold text-violet-600 hover:text-violet-700 bg-white border border-violet-200 hover:bg-violet-50 px-3 py-1.5 rounded-lg transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            سجل النشاط الكامل
        </a>
    </div>

    {{-- User cards grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach($usersActivity as $u)
        @php
            $breakdown   = $todayBreakdown[$u->id]  ?? collect();
            $wEdits      = $weekEdits[$u->id]        ?? 0;
            $lastLogin   = $lastLoginPerUser[$u->id] ?? null;
            $recentFeed  = $recentActionsPerUser[$u->id] ?? collect();
            $todayTotal  = $breakdown->sum();
            $hasActivity = !is_null($u->last_at);
            $lastAt      = $hasActivity ? \Carbon\Carbon::parse($u->last_at) : null;
            $isOnline    = $hasActivity && $lastAt->diffInMinutes(now()) < 30;
            $lastMeta    = $actionMeta[$u->last_action] ?? $actionMeta['viewed'];

            // Avatar gradient based on name hash
            $gradients = [
                'from-violet-400 to-purple-600',
                'from-blue-400 to-indigo-600',
                'from-emerald-400 to-teal-600',
                'from-rose-400 to-pink-600',
                'from-amber-400 to-orange-500',
                'from-sky-400 to-cyan-600',
            ];
            $grad = $gradients[crc32($u->name) % count($gradients)];
        @endphp

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition-shadow cursor-pointer"
             onclick="openUserModal({{ $u->id }}, '{{ addslashes($u->name) }}', '{{ route('dashboard.user-activity', $u->id) }}')">

            {{-- Card header --}}
            <div class="px-5 py-4 flex items-center gap-3 border-b border-gray-50">
                {{-- Avatar --}}
                <div class="relative shrink-0">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-br {{ $grad }} flex items-center justify-center text-white font-black text-base shadow-sm">
                        {{ mb_substr($u->name, 0, 1) }}
                    </div>
                    @if($isOnline)
                        <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-emerald-400 border-2 border-white rounded-full"></span>
                    @elseif($todayTotal > 0)
                        <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-blue-400 border-2 border-white rounded-full"></span>
                    @else
                        <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-gray-300 border-2 border-white rounded-full"></span>
                    @endif
                </div>

                {{-- Name + status --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="font-black text-gray-800 text-sm">{{ $u->name }}</span>
                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-md {{ $u->role === 'admin' ? 'bg-violet-100 text-violet-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $u->role === 'admin' ? 'مدير' : 'مستخدم' }}
                        </span>
                        @if($isOnline)
                            <span class="inline-flex items-center gap-1 text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200 px-1.5 py-0.5 rounded-md">
                                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>نشط الآن
                            </span>
                        @endif
                    </div>
                    @if($lastLogin)
                        <p class="text-[11px] text-gray-400 mt-0.5">آخر دخول: {{ \Carbon\Carbon::parse($lastLogin)->diffForHumans() }}</p>
                    @else
                        <p class="text-[11px] text-gray-300 mt-0.5">لا يوجد تسجيل دخول مسجّل</p>
                    @endif
                </div>

                {{-- This week badge --}}
                <div class="shrink-0 text-center">
                    <p class="text-2xl font-black {{ $wEdits > 0 ? 'text-violet-600' : 'text-gray-200' }}">{{ number_format($wEdits) }}</p>
                    <p class="text-[10px] text-gray-400 leading-tight">هذا الأسبوع</p>
                </div>
            </div>

            {{-- Today breakdown --}}
            <div class="px-5 py-3 bg-gray-50/50 border-b border-gray-50">
                @if($todayTotal > 0)
                <div class="flex items-center gap-3">
                    <span class="text-[11px] font-semibold text-gray-400 shrink-0">اليوم:</span>
                    <div class="flex items-center gap-2 flex-wrap">
                        @if($breakdown->get('created', 0) > 0)
                        <span class="inline-flex items-center gap-1 text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100 px-2 py-0.5 rounded-full">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            {{ $breakdown->get('created') }} إضافة
                        </span>
                        @endif
                        @if($breakdown->get('updated', 0) > 0)
                        <span class="inline-flex items-center gap-1 text-xs font-bold bg-amber-50 text-amber-700 border border-amber-100 px-2 py-0.5 rounded-full">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            {{ $breakdown->get('updated') }} تعديل
                        </span>
                        @endif
                        @if($breakdown->get('deleted', 0) > 0)
                        <span class="inline-flex items-center gap-1 text-xs font-bold bg-red-50 text-red-700 border border-red-100 px-2 py-0.5 rounded-full">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            {{ $breakdown->get('deleted') }} حذف
                        </span>
                        @endif
                    </div>
                </div>
                @else
                <p class="text-xs text-gray-300 font-medium">لا توجد عمليات اليوم</p>
                @endif
            </div>

            {{-- Mini feed: last 3 actions --}}
            @if($recentFeed->isNotEmpty())
            <div class="divide-y divide-gray-50">
                @foreach($recentFeed as $log)
                @php $lm = $actionMeta[$log->action] ?? $actionMeta['viewed']; @endphp
                <div class="px-5 py-2.5 flex items-center gap-2.5">
                    <span class="shrink-0 w-5 h-5 rounded-md {{ $lm['bg'] }} {{ $lm['text'] }} flex items-center justify-center">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $lm['icon'] }}"/></svg>
                    </span>
                    <span class="text-xs font-semibold {{ $lm['text'] }} shrink-0">{{ $lm['label'] }}</span>
                    <span class="text-xs text-gray-600 truncate flex-1">{{ $log->subject_label ?: $log->description }}</span>
                    <span class="text-[10px] text-gray-300 shrink-0 whitespace-nowrap">{{ $log->created_at->diffForHumans(null, true) }}</span>
                </div>
                @endforeach
            </div>
            @else
            <div class="px-5 py-3 text-center">
                <p class="text-xs text-gray-300">لا يوجد سجل نشاط</p>
            </div>
            @endif

        </div>
        @endforeach
    </div>
</div>
@endif

{{-- My Recent Requests --}}
@if($myRecentChanges->isNotEmpty())
<div class="mt-8">
    <div class="mb-4 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <div class="w-1 h-5 bg-gradient-to-b from-amber-500 to-orange-500 rounded-full"></div>
            <h2 class="text-lg font-bold text-gray-800">طلباتي الأخيرة</h2>
        </div>
        <a href="{{ route('pending-changes.my') }}" class="text-sm text-amber-600 hover:text-amber-700 font-semibold">عرض الكل ←</a>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="divide-y divide-gray-50">
            @foreach($myRecentChanges as $change)
                @php
                    $ac = ['create' => 'emerald', 'update' => 'blue', 'delete' => 'red'][$change->action] ?? 'gray';
                @endphp
                <div @class([
                    'px-5 py-3.5 flex items-center justify-between gap-4',
                    'bg-red-50/40' => $change->isRejected(),
                ])>
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="shrink-0 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-{{ $ac }}-50 text-{{ $ac }}-700 border border-{{ $ac }}-100">
                            {{ $change->actionLabel() }} {{ $change->modelLabel() }}
                        </span>
                        <div class="min-w-0">
                            <p class="font-semibold text-gray-800 text-sm truncate">
                                {{ $change->payload['full_name'] ?? $change->original['full_name'] ?? '—' }}
                            </p>
                            @if($change->isRejected() && $change->reviewer_notes)
                                <p class="text-xs text-red-500 truncate mt-0.5">{{ $change->reviewer_notes }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        <span class="text-xs text-gray-400">{{ $change->created_at->diffForHumans() }}</span>
                        @if($change->isPending())
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                <span class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-pulse"></span>
                                معلّق
                            </span>
                        @elseif($change->isApproved())
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                موافق عليه
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-600 border border-red-200">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                مرفوض
                            </span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        @if($myPendingCount || $myRejectedCount)
        <div class="px-5 py-3 border-t border-gray-100 bg-gray-50/50 flex items-center gap-4 text-sm">
            @if($myPendingCount)
                <span class="text-amber-600 font-semibold">{{ $myPendingCount }} معلّق</span>
            @endif
            @if($myRejectedCount)
                <span class="text-red-500 font-semibold">{{ $myRejectedCount }} مرفوض</span>
            @endif
        </div>
        @endif
    </div>
</div>
@endif


{{-- User Week Activity Modal --}}
<div id="user-modal" style="display:none" class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeUserModal()"></div>
    <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-xl max-h-[85vh] flex flex-col overflow-hidden">

        {{-- Modal header --}}
        <div class="flex items-center gap-3 px-6 py-5 border-b border-gray-100">
            <div id="modal-avatar" class="w-11 h-11 rounded-2xl bg-gradient-to-br from-violet-400 to-purple-600 flex items-center justify-center text-white font-black text-base shrink-0"></div>
            <div class="flex-1 min-w-0">
                <h3 id="modal-name" class="font-black text-gray-800 text-base"></h3>
                <p class="text-xs text-gray-400">تعديلات هذا الأسبوع</p>
            </div>
            <div id="modal-count" class="shrink-0 text-center ml-2">
                <p class="text-2xl font-black text-violet-600"></p>
                <p class="text-[10px] text-gray-400">عملية</p>
            </div>
            <button onclick="closeUserModal()" class="shrink-0 w-8 h-8 rounded-xl bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Filters --}}
        <div class="flex items-center gap-2 px-6 py-3 border-b border-gray-50 bg-gray-50/50">
            <button onclick="filterModal('all')"     id="filter-all"     class="modal-filter active-filter text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors">الكل</button>
            <button onclick="filterModal('created')" id="filter-created" class="modal-filter text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors bg-white border border-gray-200 text-gray-500 hover:bg-blue-50 hover:text-blue-700">إضافة</button>
            <button onclick="filterModal('updated')" id="filter-updated" class="modal-filter text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors bg-white border border-gray-200 text-gray-500 hover:bg-amber-50 hover:text-amber-700">تعديل</button>
            <button onclick="filterModal('deleted')" id="filter-deleted" class="modal-filter text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors bg-white border border-gray-200 text-gray-500 hover:bg-red-50 hover:text-red-700">حذف</button>
            <div id="modal-loading" class="hidden mr-auto">
                <svg class="w-4 h-4 text-violet-400 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
            </div>
        </div>

        {{-- Log list --}}
        <div id="modal-body" class="flex-1 overflow-y-auto divide-y divide-gray-50"></div>

        {{-- Empty state --}}
        <div id="modal-empty" class="hidden flex-1 flex items-center justify-center py-16 flex-col gap-3">
            <div class="w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center">
                <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <p class="text-sm font-semibold text-gray-400">لا توجد عمليات هذا الأسبوع</p>
        </div>

    </div>
</div>

<style>
.active-filter { background: #7c3aed; color: #fff; border: 1px solid #7c3aed; }
</style>

@push('scripts')
<script>
const ACTION_META = {
    created: { label:'إضافة', bg:'#eff6ff', text:'#1d4ed8', border:'#bfdbfe', icon:'M12 4v16m8-8H4' },
    updated: { label:'تعديل', bg:'#fffbeb', text:'#b45309', border:'#fde68a', icon:'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z' },
    deleted: { label:'حذف',   bg:'#fef2f2', text:'#b91c1c', border:'#fecaca', icon:'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16' },
};

let allLogs = [];
let currentFilter = 'all';

function openUserModal(userId, userName, url) {
    allLogs = [];
    currentFilter = 'all';

    document.getElementById('modal-avatar').textContent = userName.charAt(0);
    document.getElementById('modal-name').textContent = userName;
    document.getElementById('modal-count').querySelector('p').textContent = '';
    document.getElementById('modal-body').innerHTML = '';
    document.getElementById('modal-empty').classList.add('hidden');
    document.getElementById('modal-loading').classList.remove('hidden');
    document.getElementById('user-modal').style.display = 'flex';
    document.body.style.overflow = 'hidden';

    resetFilters();

    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(data => {
            document.getElementById('modal-loading').classList.add('hidden');
            allLogs = data.logs;
            document.getElementById('modal-count').querySelector('p').textContent = allLogs.length;
            renderLogs();
        });
}

function closeUserModal() {
    document.getElementById('user-modal').style.display = 'none';
    document.body.style.overflow = '';
}

function resetFilters() {
    document.querySelectorAll('.modal-filter').forEach(b => {
        b.classList.remove('active-filter');
        b.style.background = '';
        b.style.color = '';
        b.style.borderColor = '';
    });
    const all = document.getElementById('filter-all');
    all.classList.add('active-filter');
}

function filterModal(type) {
    currentFilter = type;
    document.querySelectorAll('.modal-filter').forEach(b => {
        b.classList.remove('active-filter');
        b.className = b.className.replace('active-filter','').trim();
    });

    const colors = { all:'bg-violet-600 text-white border-violet-600', created:'bg-blue-600 text-white border-blue-600', updated:'bg-amber-500 text-white border-amber-500', deleted:'bg-red-600 text-white border-red-600' };
    const btn = document.getElementById('filter-' + type);
    btn.classList.add('active-filter');

    renderLogs();
}

function renderLogs() {
    const body  = document.getElementById('modal-body');
    const empty = document.getElementById('modal-empty');
    const filtered = currentFilter === 'all' ? allLogs : allLogs.filter(l => l.action === currentFilter);

    if (filtered.length === 0) {
        body.innerHTML = '';
        body.classList.add('hidden');
        empty.classList.remove('hidden');
        return;
    }

    body.classList.remove('hidden');
    empty.classList.add('hidden');

    body.innerHTML = filtered.map(log => {
        const m = ACTION_META[log.action] || ACTION_META.updated;
        return `
        <div class="px-6 py-3.5 flex items-center gap-3 hover:bg-gray-50/60 transition-colors">
            <span style="background:${m.bg};color:${m.text};border:1px solid ${m.border}"
                  class="shrink-0 inline-flex items-center gap-1 text-xs font-bold px-2 py-1 rounded-lg">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="${m.icon}"/>
                </svg>
                ${m.label}
            </span>
            <span class="flex-1 text-sm text-gray-700 truncate" title="${log.label}">${log.label}</span>
            <div class="shrink-0 text-left">
                <p class="text-xs text-gray-400 whitespace-nowrap">${log.diff}</p>
                <p class="text-[10px] text-gray-300 whitespace-nowrap">${log.time}</p>
            </div>
        </div>`;
    }).join('');
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') closeUserModal(); });
</script>
@endpush

@endsection
