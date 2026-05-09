@extends('layouts.app')

@section('title', 'الإحصائيات — مسالك النور')

@section('content')

{{-- Hero --}}
<div class="relative bg-gradient-to-l from-indigo-600 via-violet-500 to-purple-500 rounded-3xl p-7 mb-8 overflow-hidden shadow-lg">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute -top-8 -left-8 w-48 h-48 bg-white rounded-full"></div>
        <div class="absolute -bottom-12 left-24 w-64 h-64 bg-white rounded-full"></div>
        <div class="absolute top-6 right-16 w-24 h-24 bg-white rounded-full"></div>
    </div>
    <div class="relative flex items-center justify-between flex-wrap gap-4">
        <div>
            <p class="text-indigo-100 text-sm font-medium mb-1">جمعية مسالك النور</p>
            <h1 class="text-3xl font-black text-white mb-1">الإحصائيات</h1>
            <p class="text-indigo-200 text-sm">{{ number_format($totalMembers) }} عضو — {{ number_format((float)$totalEstimatedAmount, 0, '.', ',') }} ل.س مجموع مقدر</p>
        </div>
        <a href="{{ route('age-statistics.index') }}"
           class="inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 text-white text-sm font-semibold px-4 py-2 rounded-xl transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            إحصائيات الأعمار
        </a>
    </div>
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
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-5">
    <h3 class="text-sm font-bold text-gray-700 mb-4 flex items-center gap-2">
        <span class="w-2 h-2 rounded-full bg-teal-500 inline-block"></span>
        أعضاء الجمعيات (أعلى {{ $associationDist->count() }})
    </h3>
    <canvas id="chartAssociation" class="max-h-56"></canvas>
</div>
@endif

{{-- Row 4: Marital Status --}}
@if($maritalDist->isNotEmpty())
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-5">
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

{{-- Final Status Breakdown --}}
@if($finalStatuses->isNotEmpty())
<div class="mb-5">
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

doughnutChart('chartVerification',
    {!! json_encode($verificationDist->pluck('name')->concat($noVerificationCount ? ['بدون حالة'] : [])) !!},
    {!! json_encode($verificationDist->pluck('count')->concat($noVerificationCount ? [$noVerificationCount] : [])) !!},
    {!! json_encode($verificationDist->pluck('color')->concat($noVerificationCount ? ['#d1d5db'] : [])) !!}
);

doughnutChart('chartFinal',
    {!! json_encode($finalDist->pluck('name')->concat($noFinalStatusCount ? ['بدون حالة'] : [])) !!},
    {!! json_encode($finalDist->pluck('count')->concat($noFinalStatusCount ? [$noFinalStatusCount] : [])) !!},
    {!! json_encode($finalDist->pluck('color')->concat($noFinalStatusCount ? ['#d1d5db'] : [])) !!}
);

doughnutChart('chartGender',
    {!! json_encode($genderDist->pluck('name')) !!},
    {!! json_encode($genderDist->pluck('count')) !!},
    ['#6366f1','#ec4899','#94a3b8']
);

doughnutChart('chartNetwork',
    {!! json_encode($networkDist->pluck('name')) !!},
    {!! json_encode($networkDist->pluck('count')) !!},
    ['#f59e0b','#3b82f6','#94a3b8']
);

doughnutChart('chartShamCash',
    {!! json_encode($shamCashDist->pluck('name')) !!},
    {!! json_encode($shamCashDist->pluck('count')) !!},
    ['#10b981','#06b6d4','#e5e7eb']
);

doughnutChart('chartSpecial',
    ['نعم', 'لا'],
    [{{ $specialCasesCount }}, {{ $noSpecialCasesCount }}],
    ['#ef4444','#e5e7eb']
);

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

@if($maritalDist->isNotEmpty())
doughnutChart('chartMarital',
    {!! json_encode($maritalDist->pluck('name')) !!},
    {!! json_encode($maritalDist->pluck('count')) !!},
    {!! json_encode($maritalDist->keys()->map(fn($i) => 'hsl(' . ($i * 60 + 270) . ',60%,55%)')->values()) !!}
);
@endif
</script>
@endpush

@endsection
