@extends('layouts.app')

@section('title', 'إحصائيات الأعمار — مسالك النور')

@section('breadcrumb')
    <span class="text-gray-700">إحصائيات الأعمار</span>
@endsection

@section('content')

{{-- Hero --}}
<div class="relative bg-gradient-to-l from-blue-600 via-indigo-500 to-violet-500 rounded-3xl p-6 mb-6 overflow-hidden shadow-lg">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute -top-6 -left-6 w-36 h-36 bg-white rounded-full"></div>
        <div class="absolute -bottom-10 left-20 w-52 h-52 bg-white rounded-full"></div>
        <div class="absolute top-4 right-10 w-20 h-20 bg-white rounded-full"></div>
    </div>
    <div class="relative">
        <h1 class="text-2xl font-black text-white">إحصائيات الأعمار</h1>
        <p class="text-indigo-100 text-sm mt-0.5">تحليل توزيع أعمار الأعضاء المسجلين</p>
    </div>
</div>

{{-- Core Stats --}}
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">

    <div class="bg-white border border-gray-100 rounded-2xl p-4 shadow-sm text-center">
        <p class="text-xs text-gray-400 font-medium mb-1">إجمالي الأعضاء</p>
        <p class="text-3xl font-black text-gray-800">{{ number_format($totalMembers) }}</p>
        <p class="text-xs text-gray-400 mt-0.5">عضو</p>
    </div>

    <div class="bg-white border border-blue-100 rounded-2xl p-4 shadow-sm text-center">
        <p class="text-xs text-blue-400 font-medium mb-1">لديهم عمر مسجل</p>
        <p class="text-3xl font-black text-blue-600">{{ number_format($totalWithAge) }}</p>
        <p class="text-xs text-blue-300 mt-0.5">{{ $totalMembers > 0 ? round($totalWithAge/$totalMembers*100,1) : 0 }}%</p>
    </div>

    <div class="bg-gradient-to-br from-indigo-500 to-violet-600 rounded-2xl p-4 shadow-sm text-center">
        <p class="text-xs text-indigo-100 font-medium mb-1">متوسط العمر</p>
        <p class="text-3xl font-black text-white">{{ $avgAge }}</p>
        <p class="text-xs text-indigo-200 mt-0.5">سنة</p>
    </div>

    <div class="bg-white border border-gray-100 rounded-2xl p-4 shadow-sm text-center">
        <p class="text-xs text-gray-400 font-medium mb-1">الوسيط</p>
        <p class="text-3xl font-black text-gray-700">{{ $median }}</p>
        <p class="text-xs text-gray-400 mt-0.5">سنة</p>
    </div>

    <div class="bg-white border border-emerald-100 rounded-2xl p-4 shadow-sm text-center">
        <p class="text-xs text-emerald-500 font-medium mb-1">أصغر عمر</p>
        <p class="text-3xl font-black text-emerald-600">{{ $minAge }}</p>
        <p class="text-xs text-emerald-400 mt-0.5">سنة</p>
    </div>

    <div class="bg-white border border-rose-100 rounded-2xl p-4 shadow-sm text-center">
        <p class="text-xs text-rose-500 font-medium mb-1">أكبر عمر</p>
        <p class="text-3xl font-black text-rose-600">{{ $maxAge }}</p>
        <p class="text-xs text-rose-400 mt-0.5">سنة</p>
    </div>

</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">

    {{-- Age Groups --}}
    <div class="lg:col-span-2 bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
        <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100">
            <div class="w-7 h-7 rounded-lg bg-indigo-100 flex items-center justify-center">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <h2 class="text-sm font-bold text-gray-700">توزيع الأعمار حسب الفئة</h2>
        </div>
        <div class="p-5 space-y-4">
            @foreach($groups as $group)
            @php $max = collect($groups)->max('count'); @endphp
            <a href="{{ route('members.index') }}" class="block group">
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-sm font-semibold text-gray-700">{{ $group['label'] }}</span>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-400">{{ $group['pct'] }}%</span>
                        <span class="text-sm font-bold text-gray-800 w-12 text-left">{{ number_format($group['count']) }}</span>
                    </div>
                </div>
                <div class="h-3 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-700"
                         style="width: {{ $max > 0 ? round($group['count']/$max*100) : 0 }}%; background: {{ $group['color'] }}"></div>
                </div>
            </a>
            @endforeach
        </div>
    </div>

    {{-- Side cards --}}
    <div class="space-y-4">

        {{-- Youngest --}}
        @if($youngest)
        <div class="bg-white border border-emerald-100 rounded-2xl shadow-sm overflow-hidden">
            <div class="flex items-center gap-2.5 px-5 py-3.5 border-b border-emerald-100 bg-emerald-50/50">
                <div class="w-7 h-7 rounded-lg bg-emerald-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/>
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-emerald-700">الأصغر سناً</h3>
            </div>
            <div class="p-4 flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center shrink-0">
                    <span class="text-xl font-black text-emerald-600">{{ $youngest->age }}</span>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-800">{{ $youngest->full_name }}</p>
                    <p class="text-xs text-gray-400">ملف: {{ $youngest->dossier_number ?? '—' }}</p>
                </div>
            </div>
        </div>
        @endif

        {{-- Oldest --}}
        @if($oldest)
        <div class="bg-white border border-rose-100 rounded-2xl shadow-sm overflow-hidden">
            <div class="flex items-center gap-2.5 px-5 py-3.5 border-b border-rose-100 bg-rose-50/50">
                <div class="w-7 h-7 rounded-lg bg-rose-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-rose-700">الأكبر سناً</h3>
            </div>
            <div class="p-4 flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-rose-100 flex items-center justify-center shrink-0">
                    <span class="text-xl font-black text-rose-600">{{ $oldest->age }}</span>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-800">{{ $oldest->full_name }}</p>
                    <p class="text-xs text-gray-400">ملف: {{ $oldest->dossier_number ?? '—' }}</p>
                </div>
            </div>
        </div>
        @endif

        {{-- Without age --}}
        @if($totalWithoutAge > 0)
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-bold text-amber-800">{{ number_format($totalWithoutAge) }} عضو بدون عمر مسجل</p>
                <p class="text-xs text-amber-600">{{ $totalMembers > 0 ? round($totalWithoutAge/$totalMembers*100,1) : 0 }}% من إجمالي الأعضاء</p>
            </div>
        </div>
        @endif

    </div>
</div>

{{-- Distribution chart by decade + Top ages --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

    {{-- Decade distribution --}}
    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
        <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100">
            <div class="w-7 h-7 rounded-lg bg-blue-100 flex items-center justify-center">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <h2 class="text-sm font-bold text-gray-700">التوزيع بالعقود</h2>
        </div>
        <div class="p-5">
            @php $decadeMax = collect($decades)->max('count'); @endphp
            <div class="flex items-end gap-2 h-40">
                @foreach($decades as $d)
                <div class="flex-1 flex flex-col items-center gap-1">
                    <span class="text-xs font-bold text-gray-600">{{ $d['count'] > 0 ? $d['count'] : '' }}</span>
                    <div class="w-full rounded-t-lg transition-all duration-500 bg-gradient-to-t from-indigo-500 to-blue-400"
                         style="height: {{ $decadeMax > 0 ? max(4, round($d['count']/$decadeMax*100)) : 4 }}%"></div>
                    <span class="text-xs text-gray-400 leading-tight text-center">{{ $d['label'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Most common ages --}}
    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
        <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100">
            <div class="w-7 h-7 rounded-lg bg-violet-100 flex items-center justify-center">
                <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                </svg>
            </div>
            <h2 class="text-sm font-bold text-gray-700">أكثر الأعمار شيوعاً</h2>
        </div>
        <div class="divide-y divide-gray-50">
            @php $topMax = $topAges->max('cnt'); @endphp
            @foreach($topAges as $i => $item)
            <div class="flex items-center gap-3 px-5 py-3">
                <span class="w-6 text-center text-xs font-black {{ $i === 0 ? 'text-amber-500' : 'text-gray-300' }}">{{ $i + 1 }}</span>
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 {{ $i === 0 ? 'bg-amber-100' : 'bg-gray-100' }}">
                    <span class="text-base font-black {{ $i === 0 ? 'text-amber-600' : 'text-gray-600' }}">{{ $item->age }}</span>
                </div>
                <div class="flex-1">
                    <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full bg-gradient-to-r from-violet-400 to-indigo-500"
                             style="width: {{ $topMax > 0 ? round($item->cnt/$topMax*100) : 0 }}%"></div>
                    </div>
                </div>
                <span class="text-sm font-bold text-gray-700 w-10 text-left">{{ $item->cnt }}</span>
                <span class="text-xs text-gray-400">عضو</span>
            </div>
            @endforeach
        </div>
    </div>

</div>

@endsection
