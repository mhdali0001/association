@extends('layouts.app')

@section('title', 'الأسماء المتشابهة — مسالك النور')

@section('breadcrumb')
    <a href="{{ route('members.index') }}" class="hover:text-emerald-700 transition-colors">الأعضاء</a>
    <span class="text-gray-300 mx-1">/</span>
    <a href="{{ route('members.duplicates') }}" class="hover:text-emerald-700 transition-colors">كشف التكرارات</a>
    <span class="text-gray-300 mx-1">/</span>
    <span class="text-gray-700">الأسماء المتشابهة</span>
@endsection

@section('content')

@php
    $levelStyles = [
        'high'   => ['label' => 'احتمال مرتفع', 'badge' => 'bg-red-100 text-red-700',       'card' => 'border-red-100',    'head' => 'bg-red-50 border-red-100'],
        'medium' => ['label' => 'احتمال متوسط', 'badge' => 'bg-orange-100 text-orange-700', 'card' => 'border-orange-100', 'head' => 'bg-orange-50 border-orange-100'],
        'low'    => ['label' => 'احتمال ضعيف',  'badge' => 'bg-gray-100 text-gray-600',     'card' => 'border-gray-100',   'head' => 'bg-gray-50 border-gray-100'],
    ];
    $typeLabels = [
        'normalized' => 'اختلاف همزة / تاء مربوطة / مسافة',
        'spelling'   => 'خطأ إملائي',
        'partial'    => 'اسم ناقص',
    ];
    $hasFilters = $search || $verificationId || $type !== 'all' || $level !== 'all' || $similarity !== 85;
@endphp

{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 mb-6">
    <div>
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 flex items-center gap-2.5">
            <span class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-yellow-100 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-yellow-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </span>
            الأسماء المتشابهة
        </h1>
        <p class="text-sm text-gray-400 mt-1 mr-10 sm:mr-12">
            أسماء قد تكون لنفس الشخص لكنها مكتوبة بشكل مختلف —
            <span class="font-bold text-yellow-700">{{ $groups->total() }}</span>
            مجموعة
        </p>
    </div>
    <a href="{{ route('members.duplicates') }}"
       class="self-start sm:self-auto text-sm text-gray-500 hover:text-gray-700 border border-gray-200 hover:border-gray-300 px-4 py-2 rounded-lg transition-colors whitespace-nowrap">
        ← كشف التكرارات
    </a>
</div>

{{-- How it works --}}
<div class="bg-yellow-50 border border-yellow-100 rounded-2xl px-4 py-3 mb-6 text-xs text-yellow-800 leading-relaxed">
    تُوحَّد الأسماء أولاً (أ/إ/آ ← ا، ة ← ه، ى ← ي، حذف التشكيل، «عبد الرزاق» = «عبدالرزاق») ثم تُقارن حرفاً بحرف.
    يُرفع الاحتمال عند تطابق رقم الهوية أو الهاتف أو تقارب العمر، ويُخفَّض عند اختلاف الهوية أو اسم الأم أو فرق العمر الكبير (غالباً إخوة).
    الكلمات <mark class="bg-amber-200 text-amber-900 rounded px-1">المظللة</mark> هي موضع الاختلاف.
</div>

{{-- Filters --}}
<form method="GET" action="{{ route('members.duplicates.similar-names') }}"
      class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">بحث</label>
            <input type="text" name="search" value="{{ $search }}"
                   placeholder="اسم، هوية، هاتف…"
                   class="w-full border-2 border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">نوع الاختلاف</label>
            <select name="type" class="w-full border-2 border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                <option value="all" {{ $type === 'all' ? 'selected' : '' }}>الكل</option>
                @foreach($typeLabels as $value => $label)
                    <option value="{{ $value }}" {{ $type === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">الحد الأدنى للتشابه الإملائي</label>
            <select name="similarity" class="w-full border-2 border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                @foreach([75, 80, 85, 90, 95] as $s)
                    <option value="{{ $s }}" {{ $similarity === $s ? 'selected' : '' }}>{{ $s }}%{{ $s === 85 ? ' (افتراضي)' : '' }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">احتمال التكرار</label>
            <select name="level" class="w-full border-2 border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                <option value="all"    {{ $level === 'all'    ? 'selected' : '' }}>الكل</option>
                <option value="medium" {{ $level === 'medium' ? 'selected' : '' }}>متوسط فأعلى</option>
                <option value="high"   {{ $level === 'high'   ? 'selected' : '' }}>مرتفع فقط</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">حالة التحقق</label>
            <select name="verification_status_id" class="w-full border-2 border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                <option value="">— الكل —</option>
                @foreach($verificationStatuses as $vs)
                    <option value="{{ $vs->id }}" {{ $verificationId == $vs->id ? 'selected' : '' }}>{{ $vs->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end gap-2">
            <button type="submit"
                    class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-5 py-2 rounded-xl transition-colors">
                تطبيق
            </button>
            @if($hasFilters)
                <a href="{{ route('members.duplicates.similar-names') }}"
                   class="text-sm text-gray-400 hover:text-gray-600 px-4 py-2 rounded-xl border border-gray-200 hover:border-gray-300 transition-colors whitespace-nowrap">
                    مسح
                </a>
            @endif
        </div>
    </div>
</form>

{{-- Summary cards --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 mb-8">
    @foreach([
        ['level' => 'high', 'type' => 'all', 'count' => $counts['high'],       'label' => 'احتمال مرتفع',         'color' => 'text-red-600'],
        ['level' => 'all', 'type' => 'normalized', 'count' => $counts['normalized'], 'label' => 'همزة / تاء / مسافة', 'color' => 'text-yellow-600'],
        ['level' => 'all', 'type' => 'spelling',   'count' => $counts['spelling'],   'label' => 'خطأ إملائي',         'color' => 'text-orange-600'],
        ['level' => 'all', 'type' => 'partial',    'count' => $counts['partial'],    'label' => 'اسم ناقص',           'color' => 'text-blue-600'],
    ] as $card)
        <a href="{{ route('members.duplicates.similar-names', ['similarity' => $similarity, 'type' => $card['type'], 'level' => $card['level']]) }}"
           class="bg-white rounded-2xl border shadow-sm p-3 sm:p-5 hover:border-gray-300 transition-colors {{ $type === $card['type'] && $level === $card['level'] ? 'border-emerald-500' : 'border-gray-100' }}">
            <p class="text-xl sm:text-2xl font-black leading-none {{ $card['color'] }}">{{ $card['count'] }}</p>
            <p class="text-xs text-gray-400 mt-1 leading-tight">{{ $card['label'] }}</p>
        </a>
    @endforeach
</div>

@if($groups->isEmpty())
    <div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-12 text-center">
        <svg class="w-12 h-12 text-emerald-400 mx-auto mb-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-emerald-700 font-semibold text-lg">لا توجد أسماء متشابهة!</p>
        <p class="text-emerald-500 text-sm mt-1">لم يُعثر على أسماء مكتوبة بأشكال مختلفة بحسب الفلاتر الحالية</p>
    </div>
@else

    <div class="space-y-4">
        @foreach($groups as $group)
        @php $style = $levelStyles[$group['level']]; @endphp
        <div class="bg-white rounded-2xl border {{ $style['card'] }} shadow-sm overflow-hidden">
            {{-- Group header --}}
            <div class="{{ $style['head'] }} border-b px-4 py-2.5 flex flex-wrap items-center gap-2">
                <span class="text-xs rounded-full px-2.5 py-0.5 font-bold {{ $style['badge'] }}">
                    {{ $style['label'] }} · {{ $group['score'] }}%
                </span>
                @foreach($group['types'] as $t => $sim)
                    <span class="text-xs bg-white border border-gray-200 text-gray-600 rounded-full px-2.5 py-0.5 font-medium">
                        {{ $typeLabels[$t] }}@if($t === 'spelling') · {{ $sim }}%@endif
                    </span>
                @endforeach
                @foreach($group['positive'] as $ev)
                    <span class="text-xs bg-emerald-50 text-emerald-700 rounded-full px-2.5 py-0.5 font-medium">✓ {{ $ev }}</span>
                @endforeach
                @foreach($group['negative'] as $ev)
                    <span class="text-xs bg-gray-100 text-gray-500 rounded-full px-2.5 py-0.5 font-medium">✗ {{ $ev }}</span>
                @endforeach
                <span class="text-xs text-gray-500 font-semibold mr-auto">{{ $group['members']->count() }} سجلات</span>
            </div>

            {{-- Mobile cards --}}
            <div class="sm:hidden divide-y divide-gray-50">
                @foreach($group['members'] as $member)
                <div class="p-4">
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <div>
                            <p class="font-semibold text-gray-800 text-sm">
                                @include('members._similar_name', ['tokens' => $group['tokens'][$member->id], 'differing' => $group['differing'][$member->id] ?? []])
                            </p>
                            <p class="text-xs text-gray-400 mt-0.5">الأم: {{ $member->mother_name ?: '—' }} · العمر: {{ $member->age ?: '—' }}</p>
                        </div>
                        @if($member->verificationStatus)
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium shrink-0"
                                  style="background:{{ $member->verificationStatus->color }}18; color:{{ $member->verificationStatus->color }}">
                                {{ $member->verificationStatus->name }}
                            </span>
                        @endif
                    </div>
                    <div class="flex flex-wrap items-center gap-3 text-xs text-gray-400 mb-3">
                        <span>هوية: <span class="font-mono">{{ $member->national_id ?: '—' }}</span></span>
                        <span>·</span>
                        <span>هاتف: {{ $member->phone ?: '—' }}</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('members.show', $member) }}" class="text-blue-600 text-xs font-semibold">عرض</a>
                        <a href="{{ route('members.edit', $member) }}" class="text-emerald-600 text-xs font-semibold">تعديل</a>
                        <form method="POST" action="{{ route('members.destroy', $member) }}"
                              data-confirm-name="{{ $member->full_name }}">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 text-xs font-semibold">حذف</button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Desktop table --}}
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="text-right px-4 py-2.5 font-semibold text-gray-500 text-xs">#</th>
                            <th class="text-right px-4 py-2.5 font-semibold text-gray-500 text-xs">الاسم الكامل</th>
                            <th class="text-right px-4 py-2.5 font-semibold text-gray-500 text-xs">اسم الأم</th>
                            <th class="text-right px-4 py-2.5 font-semibold text-gray-500 text-xs">العمر</th>
                            <th class="text-right px-4 py-2.5 font-semibold text-gray-500 text-xs">رقم الهوية</th>
                            <th class="text-right px-4 py-2.5 font-semibold text-gray-500 text-xs">رقم الهاتف</th>
                            <th class="text-right px-4 py-2.5 font-semibold text-gray-500 text-xs">المنطقة</th>
                            <th class="text-right px-4 py-2.5 font-semibold text-gray-500 text-xs">حالة التحقق</th>
                            <th class="text-right px-4 py-2.5 font-semibold text-gray-500 text-xs">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($group['members'] as $member)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-2.5 text-gray-400 text-xs">{{ $member->id }}</td>
                            <td class="px-4 py-2.5 font-semibold text-gray-800 whitespace-nowrap">
                                @include('members._similar_name', ['tokens' => $group['tokens'][$member->id], 'differing' => $group['differing'][$member->id] ?? []])
                            </td>
                            <td class="px-4 py-2.5 text-gray-500">{{ $member->mother_name ?: '—' }}</td>
                            <td class="px-4 py-2.5 text-gray-500">{{ $member->age ?: '—' }}</td>
                            <td class="px-4 py-2.5 text-gray-500 font-mono text-xs">{{ $member->national_id ?: '—' }}</td>
                            <td class="px-4 py-2.5 text-gray-500">{{ $member->phone ?: '—' }}</td>
                            <td class="px-4 py-2.5 text-gray-500 text-xs">{{ $member->region?->name ?? '—' }}</td>
                            <td class="px-4 py-2.5">
                                @if($member->verificationStatus)
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium"
                                          style="background:{{ $member->verificationStatus->color }}18; color:{{ $member->verificationStatus->color }}">
                                        {{ $member->verificationStatus->name }}
                                    </span>
                                @else <span class="text-gray-300 text-xs">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-2.5">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('members.show', $member) }}" class="text-blue-600 hover:text-blue-800 text-xs font-medium">عرض</a>
                                    <a href="{{ route('members.edit', $member) }}" class="text-emerald-600 hover:text-emerald-800 text-xs font-medium">تعديل</a>
                                    <form method="POST" action="{{ route('members.destroy', $member) }}"
                                          data-confirm-name="{{ $member->full_name }}">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-medium">حذف</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $groups->links() }}
    </div>

@endif

@endsection
