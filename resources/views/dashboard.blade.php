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

{{-- Charts Section --}}
<div class="mb-8">
    <div class="flex items-center gap-2.5 mb-4">
        <div class="w-7 h-7 rounded-lg bg-indigo-100 flex items-center justify-center shrink-0">
            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
        </div>
        <h2 class="text-base font-bold text-gray-700">إحصائيات الأعضاء</h2>
        <div class="flex-1 h-px bg-gray-100"></div>
        <span class="text-xs text-gray-400">{{ number_format($totalMembers) }} عضو — {{ number_format((float)$totalEstimatedAmount, 0, '.', ',') }} ل.س مجموع مقدر</span>
    </div>

    {{-- Row 1: Verification + Final Status --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">

        {{-- حالة التحقق --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h3 class="text-sm font-bold text-gray-700 mb-4 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-blue-500 inline-block"></span>
                توزيع حالة التحقق
            </h3>
            <div class="flex items-center gap-6">
                <div class="relative w-40 h-40 shrink-0">
                    <canvas id="chartVerification"></canvas>
                </div>
                <div class="flex-1 space-y-2">
                    @foreach($verificationDist as $item)
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background:{{ $item['color'] }}"></span>
                            <span class="text-gray-700 truncate">{{ $item['name'] }}</span>
                        </div>
                        <span class="font-bold text-gray-900 ml-2 shrink-0">{{ number_format($item['count']) }}</span>
                    </div>
                    @endforeach
                    @if($noVerificationCount)
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full shrink-0 bg-gray-300"></span>
                            <span class="text-gray-500">بدون حالة</span>
                        </div>
                        <span class="font-bold text-gray-500">{{ number_format($noVerificationCount) }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- الحالة النهائية --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h3 class="text-sm font-bold text-gray-700 mb-4 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span>
                توزيع الحالة النهائية
            </h3>
            <div class="flex items-center gap-6">
                <div class="relative w-40 h-40 shrink-0">
                    <canvas id="chartFinal"></canvas>
                </div>
                <div class="flex-1 space-y-2">
                    @foreach($finalDist as $item)
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background:{{ $item['color'] }}"></span>
                            <span class="text-gray-700 truncate">{{ $item['name'] }}</span>
                        </div>
                        <span class="font-bold text-gray-900 ml-2 shrink-0">{{ number_format($item['count']) }}</span>
                    </div>
                    @endforeach
                    @if($noFinalStatusCount)
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full shrink-0 bg-gray-300"></span>
                            <span class="text-gray-500">بدون حالة</span>
                        </div>
                        <span class="font-bold text-gray-500">{{ number_format($noFinalStatusCount) }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Row 2: Gender + Network + Sham Cash + Special Cases --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-5">

        {{-- الجنس --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h3 class="text-sm font-bold text-gray-700 mb-3">الجنس</h3>
            <canvas id="chartGender" class="max-h-32"></canvas>
            <div class="mt-3 space-y-1.5">
                @foreach($genderDist as $item)
                <div class="flex items-center justify-between text-xs">
                    <span class="text-gray-600 truncate">{{ $item['name'] }}</span>
                    <span class="font-bold text-gray-800">{{ number_format($item['count']) }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- نوع الشبكة --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h3 class="text-sm font-bold text-gray-700 mb-3">نوع الشبكة</h3>
            <canvas id="chartNetwork" class="max-h-32"></canvas>
            <div class="mt-3 space-y-1.5">
                @foreach($networkDist as $item)
                <div class="flex items-center justify-between text-xs">
                    <span class="text-gray-600 truncate">{{ $item['name'] }}</span>
                    <span class="font-bold text-gray-800">{{ number_format($item['count']) }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- شام كاش --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h3 class="text-sm font-bold text-gray-700 mb-3">شام كاش</h3>
            <canvas id="chartShamCash" class="max-h-32"></canvas>
            <div class="mt-3 space-y-1.5">
                @foreach($shamCashDist as $item)
                <div class="flex items-center justify-between text-xs">
                    <span class="text-gray-600">{{ $item['name'] }}</span>
                    <span class="font-bold text-gray-800">{{ number_format($item['count']) }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- الحالات الخاصة --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h3 class="text-sm font-bold text-gray-700 mb-3">الحالات الخاصة</h3>
            <canvas id="chartSpecial" class="max-h-32"></canvas>
            <div class="mt-3 space-y-1.5">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-gray-600">نعم</span>
                    <span class="font-bold text-rose-600">{{ number_format($specialCasesCount) }}</span>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <span class="text-gray-600">لا</span>
                    <span class="font-bold text-gray-800">{{ number_format($noSpecialCasesCount) }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Row 3: Association bar chart --}}
    @if($associationDist->isNotEmpty())
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <h3 class="text-sm font-bold text-gray-700 mb-4 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-teal-500 inline-block"></span>
            أعضاء الجمعيات (أعلى {{ $associationDist->count() }})
        </h3>
        <canvas id="chartAssociation" class="max-h-56"></canvas>
    </div>
    @endif

    {{-- Row 4: Marital Status --}}
    @if($maritalDist->isNotEmpty())
    <div class="mt-5 bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <h3 class="text-sm font-bold text-gray-700 mb-4 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-purple-500 inline-block"></span>
            الحالة الاجتماعية
        </h3>
        <div class="flex items-center gap-6">
            <div class="relative w-36 h-36 shrink-0">
                <canvas id="chartMarital"></canvas>
            </div>
            <div class="flex flex-wrap gap-x-8 gap-y-2">
                @foreach($maritalDist as $i => $item)
                <div class="flex items-center gap-2 text-sm">
                    <span class="w-3 h-3 rounded-full shrink-0" style="background: hsl({{ $i * 60 + 270 }}, 60%, 55%)"></span>
                    <span class="text-gray-700">{{ $item['name'] }}</span>
                    <span class="font-bold text-gray-900">{{ number_format($item['count']) }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
Chart.defaults.font.family = 'inherit';
Chart.defaults.plugins.legend.display = false;

const PALETTE = [
    '#10b981','#3b82f6','#f59e0b','#ef4444','#8b5cf6',
    '#06b6d4','#f97316','#84cc16','#ec4899','#6366f1','#14b8a6','#a855f7'
];

function doughnutChart(id, labels, data, colors) {
    const ctx = document.getElementById(id);
    if (!ctx) return;
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels,
            datasets: [{ data, backgroundColor: colors || PALETTE, borderWidth: 2, borderColor: '#fff', hoverOffset: 6 }]
        },
        options: {
            cutout: '68%',
            plugins: { tooltip: { rtl: true, callbacks: { label: ctx => ' ' + ctx.formattedValue + ' — ' + ctx.label } } }
        }
    });
}

// حالة التحقق
doughnutChart('chartVerification',
    {!! json_encode($verificationDist->pluck('name')->concat($noVerificationCount ? ['بدون حالة'] : [])) !!},
    {!! json_encode($verificationDist->pluck('count')->concat($noVerificationCount ? [$noVerificationCount] : [])) !!},
    {!! json_encode($verificationDist->pluck('color')->concat($noVerificationCount ? ['#d1d5db'] : [])) !!}
);

// الحالة النهائية
doughnutChart('chartFinal',
    {!! json_encode($finalDist->pluck('name')->concat($noFinalStatusCount ? ['بدون حالة'] : [])) !!},
    {!! json_encode($finalDist->pluck('count')->concat($noFinalStatusCount ? [$noFinalStatusCount] : [])) !!},
    {!! json_encode($finalDist->pluck('color')->concat($noFinalStatusCount ? ['#d1d5db'] : [])) !!}
);

// الجنس
doughnutChart('chartGender',
    {!! json_encode($genderDist->pluck('name')) !!},
    {!! json_encode($genderDist->pluck('count')) !!},
    ['#6366f1','#ec4899','#94a3b8']
);

// نوع الشبكة
doughnutChart('chartNetwork',
    {!! json_encode($networkDist->pluck('name')) !!},
    {!! json_encode($networkDist->pluck('count')) !!},
    ['#f59e0b','#3b82f6','#94a3b8']
);

// شام كاش
doughnutChart('chartShamCash',
    {!! json_encode($shamCashDist->pluck('name')) !!},
    {!! json_encode($shamCashDist->pluck('count')) !!},
    ['#10b981','#06b6d4','#e5e7eb']
);

// الحالات الخاصة
doughnutChart('chartSpecial',
    ['نعم', 'لا'],
    [{{ $specialCasesCount }}, {{ $noSpecialCasesCount }}],
    ['#ef4444','#e5e7eb']
);

// الجمعيات
@if($associationDist->isNotEmpty())
new Chart(document.getElementById('chartAssociation'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($associationDist->pluck('name')) !!},
        datasets: [{
            data: {!! json_encode($associationDist->pluck('count')) !!},
            backgroundColor: PALETTE,
            borderRadius: 8,
            borderSkipped: false,
        }]
    },
    options: {
        indexAxis: 'y',
        plugins: { tooltip: { rtl: true } },
        scales: {
            x: { grid: { display: false }, ticks: { precision: 0 } },
            y: { grid: { display: false }, ticks: { font: { size: 12 } } }
        }
    }
});
@endif

// الحالة الاجتماعية
@if($maritalDist->isNotEmpty())
doughnutChart('chartMarital',
    {!! json_encode($maritalDist->pluck('name')) !!},
    {!! json_encode($maritalDist->pluck('count')) !!},
    {!! json_encode($maritalDist->keys()->map(fn($i) => 'hsl(' . ($i * 60 + 270) . ',60%,55%)')->values()) !!}
);
@endif
</script>
@endpush

{{-- Quick Actions --}}
@php
$sections = [
    [
        'title' => 'الأعضاء',
        'color' => 'emerald',
        'icon'  => 'M17 20h5v-2a4 4 0 00-5-3.87M9 20H4v-2a4 4 0 015-3.87m6-4.13a4 4 0 11-8 0 4 4 0 018 0zm6-4a3 3 0 11-6 0 3 3 0 016 0z',
        'items' => [
            ['href' => route('members.index'),      'label' => 'الأعضاء',         'desc' => 'عرض وإدارة الأعضاء',       'from' => 'from-emerald-500', 'to' => 'to-emerald-600', 'icon' => 'M17 20h5v-2a4 4 0 00-5-3.87M9 20H4v-2a4 4 0 015-3.87m6-4.13a4 4 0 11-8 0 4 4 0 018 0zm6-4a3 3 0 11-6 0 3 3 0 016 0z'],
            ['href' => route('members.create'),     'label' => 'إضافة عضو',       'desc' => 'تسجيل عضو جديد',           'from' => 'from-blue-500',    'to' => 'to-blue-600',    'icon' => 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-5-3a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z'],
            ['href' => route('members.duplicates'), 'label' => 'التكرارات',       'desc' => 'رصد الأعضاء المكررين',     'from' => 'from-red-500',     'to' => 'to-rose-600',    'icon' => 'M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z'],
            ['href' => route('members.import.show'),'label' => 'استيراد Excel',   'desc' => 'استيراد الأعضاء من ملف',   'from' => 'from-sky-500',     'to' => 'to-cyan-600',    'icon' => 'M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
            ['href' => route('members.bulk-amount'),'label' => 'تعديل المبالغ',   'desc' => 'تعديل جماعي للمبلغ المقدر','from' => 'from-violet-500',  'to' => 'to-purple-600',  'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['href' => route('delegates.index'),    'label' => 'المندوبون',        'desc' => 'المندوبون وأعضاؤهم',       'from' => 'from-sky-500',     'to' => 'to-blue-600',    'icon' => 'M17 20h5v-2a4 4 0 00-5-3.87M9 20H4v-2a4 4 0 015-3.87m6-4.13a4 4 0 11-8 0 4 4 0 018 0zm6-4a3 3 0 11-6 0 3 3 0 016 0z'],
            ['href' => route('member-images.index'),   'label' => 'أرشيف الصور',     'desc' => 'صور ومستندات الاضبارات',   'from' => 'from-violet-500',  'to' => 'to-purple-600',  'icon' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z'],
            ['href' => route('age-statistics.index'),  'label' => 'إحصائيات الأعمار','desc' => 'توزيع وتحليل أعمار الأعضاء','from' => 'from-blue-500',    'to' => 'to-indigo-600',  'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
        ],
    ],
    [
        'title' => 'المالية',
        'color' => 'amber',
        'icon'  => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        'items' => [
            ['href' => route('donations.index'),  'label' => 'التبرعات',  'desc' => 'إدارة التبرعات الشهرية',   'from' => 'from-amber-500', 'to' => 'to-orange-500', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['href' => route('expenses.index'),   'label' => 'المصروفات', 'desc' => 'إدارة مصروفات الجمعية',   'from' => 'from-rose-500',  'to' => 'to-red-600',    'icon' => 'M9 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M3 12l6.414 6.414a2 2 0 001.414.586H19a2 2 0 002-2V7a2 2 0 00-2-2h-8.172a2 2 0 00-1.414.586L3 12z'],
            ['href' => route('budget.index'),     'label' => 'الميزانية', 'desc' => 'إدارة الميزانية والمدفوعات','from' => 'from-teal-500',  'to' => 'to-cyan-600',   'icon' => 'M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3'],
        ],
    ],
    [
        'title' => 'المراجعة والطلبات',
        'color' => 'orange',
        'icon'  => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
        'items' => [
            ['href' => route('pending-changes.index'), 'label' => 'طلبات التعديل', 'desc' => 'مراجعة وموافقة التعديلات', 'from' => 'from-amber-500',  'to' => 'to-orange-500',  'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
            ['href' => route('pending-changes.my'),    'label' => 'طلباتي',        'desc' => 'تتبع طلباتك ومراجعتها',   'from' => 'from-blue-400',   'to' => 'to-indigo-500',  'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
            ['href' => route('activity-logs.index'),   'label' => 'سجل النشاط',    'desc' => 'تتبع نشاط المستخدمين',    'from' => 'from-indigo-500', 'to' => 'to-violet-600',  'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01'],
        ],
    ],
    [
        'title' => 'الإعدادات',
        'color' => 'slate',
        'icon'  => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
        'items' => [
            ['href' => route('associations.index'),          'label' => 'الجمعيات',           'desc' => 'إدارة قائمة الجمعيات',          'from' => 'from-teal-500',   'to' => 'to-emerald-600', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
            ['href' => route('marital-statuses.index'),      'label' => 'الحالات الاجتماعية', 'desc' => 'إدارة قائمة الحالات الاجتماعية', 'from' => 'from-purple-500', 'to' => 'to-violet-600',  'icon' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z'],
            ['href' => route('verification-statuses.index'), 'label' => 'حالات التحقق',       'desc' => 'إدارة حالات التحقق',             'from' => 'from-yellow-400', 'to' => 'to-amber-500',   'icon' => 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z'],
            ['href' => route('final-statuses.index'),        'label' => 'الحالات النهائية',   'desc' => 'إدارة الحالات النهائية للأعضاء', 'from' => 'from-slate-500',  'to' => 'to-gray-600',    'icon' => 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z'],
        ],
    ],
];

if (auth()->user()?->role === 'admin') {
    $sections[] = [
        'title' => 'مراجعة الدفع AI',
        'color' => 'violet',
        'icon'  => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
        'items' => [
            ['href' => route('payment-review.index'),          'label' => 'مراجعة الدفع AI',  'desc' => 'مقارنة payment_info مع AI',    'from' => 'from-violet-500', 'to' => 'to-purple-600', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
            ['href' => route('payment-review.duplicate-ibans'),'label' => 'تكرار الآيبانات', 'desc' => 'آيبانات مسجلة لأكثر من عضو',  'from' => 'from-red-600',    'to' => 'to-rose-500',   'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'],
        ],
    ];
}
@endphp

@foreach($sections as $section)
<div class="mb-7">
    {{-- Section header --}}
    <div class="flex items-center gap-2.5 mb-3">
        <div class="w-7 h-7 rounded-lg bg-{{ $section['color'] }}-100 flex items-center justify-center shrink-0">
            <svg class="w-4 h-4 text-{{ $section['color'] }}-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $section['icon'] }}"/>
            </svg>
        </div>
        <h2 class="text-base font-bold text-gray-700">{{ $section['title'] }}</h2>
        <div class="flex-1 h-px bg-gray-100"></div>
    </div>

    {{-- Section cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-6 gap-3">
        @foreach($section['items'] as $action)
        <a href="{{ $action['href'] }}"
           class="group relative bg-white rounded-2xl p-4 shadow-sm border border-gray-100 hover:shadow-md hover:-translate-y-0.5 transition-all overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br {{ $action['from'] }} {{ $action['to'] }} opacity-0 group-hover:opacity-5 transition-opacity rounded-2xl"></div>
            <div class="relative">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br {{ $action['from'] }} {{ $action['to'] }} flex items-center justify-center mb-3 shadow-sm">
                    <svg class="w-4.5 h-4.5 text-white" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $action['icon'] }}"/>
                    </svg>
                </div>
                <p class="font-bold text-gray-800 text-sm mb-0.5">{{ $action['label'] }}</p>
                <p class="text-xs text-gray-400 leading-tight">{{ $action['desc'] }}</p>
            </div>
        </a>
        @endforeach
    </div>
</div>
@endforeach

{{-- Final Status Breakdown --}}
@if($finalStatuses->isNotEmpty())
<div class="mt-8">
    <div class="mb-4 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <div class="w-1 h-5 bg-gradient-to-b from-slate-500 to-gray-600 rounded-full"></div>
            <h2 class="text-lg font-bold text-gray-800">توزيع الأعضاء حسب الحالة النهائية</h2>
        </div>
        <a href="{{ route('final-statuses.index') }}" class="text-sm text-slate-600 hover:text-slate-700 font-semibold">إدارة الحالات ←</a>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-6 gap-3">
        @foreach($finalStatuses as $fs)
        <a href="{{ route('members.index', ['final_status_id[]' => $fs->id]) }}"
           class="group bg-white rounded-2xl p-4 border border-gray-100 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all overflow-hidden relative">
            <div class="absolute top-0 right-0 w-1.5 h-full rounded-r-2xl" style="background: {{ $fs->color }}"></div>
            <div class="flex items-center gap-2 mb-2">
                <span class="w-3 h-3 rounded-full flex-shrink-0" style="background: {{ $fs->color }}"></span>
                <span class="text-sm font-bold text-gray-700 truncate">{{ $fs->name }}</span>
            </div>
            <p class="text-3xl font-black" style="color: {{ $fs->color }}">{{ number_format($fs->members_count) }}</p>
            <p class="text-xs text-gray-400 mt-0.5 group-hover:text-gray-600 transition-colors">عضو ←</p>
        </a>
        @endforeach

        @if($noFinalStatusCount > 0)
        <a href="{{ route('members.index') }}"
           class="group bg-white rounded-2xl p-4 border border-gray-100 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all overflow-hidden relative">
            <div class="absolute top-0 right-0 w-1.5 h-full rounded-r-2xl bg-gray-300"></div>
            <div class="flex items-center gap-2 mb-2">
                <span class="w-3 h-3 rounded-full flex-shrink-0 bg-gray-300"></span>
                <span class="text-sm font-bold text-gray-500 truncate">بدون حالة</span>
            </div>
            <p class="text-3xl font-black text-gray-400">{{ number_format($noFinalStatusCount) }}</p>
            <p class="text-xs text-gray-400 mt-0.5 group-hover:text-gray-600 transition-colors">عضو ←</p>
        </a>
        @endif
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


@endsection
