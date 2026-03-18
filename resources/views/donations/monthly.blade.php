@extends('layouts.app')

@section('title', 'المتابعة الشهرية — التبرعات')

@section('breadcrumb')
    <a href="{{ route('donations.index') }}" class="text-emerald-600 hover:underline">التبرعات</a>
    <span class="mx-2 text-gray-400">/</span>
    <span class="text-gray-700">المتابعة الشهرية</span>
@endsection

@section('content')

{{-- Header + Month Selector --}}
<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">المتابعة الشهرية للتبرعات</h1>
        <p class="text-sm text-gray-400 mt-0.5">من تبرعنا لهم ومن لم نتبرع لهم بعد</p>
    </div>
    <form method="GET" action="{{ route('donations.monthly') }}" class="flex items-center gap-2">
        <input type="month" name="month" value="{{ $month }}"
               class="border-2 border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        <button type="submit"
                class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2 rounded-xl transition-colors">
            عرض
        </button>
    </form>
</div>

{{-- Summary Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
    <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-5 text-center">
        <p class="text-3xl font-bold text-emerald-700">{{ $donatedMembers->count() }}</p>
        <p class="text-sm text-emerald-600 mt-1">تم التبرع لهم</p>
    </div>
    <div class="bg-red-50 border border-red-200 rounded-2xl p-5 text-center">
        <p class="text-3xl font-bold text-red-600">{{ $notDonatedMembers->count() }}</p>
        <p class="text-sm text-red-500 mt-1">لم يُتبرع لهم بعد</p>
    </div>
    <div class="bg-gray-50 border border-gray-200 rounded-2xl p-5 text-center">
        <p class="text-3xl font-bold text-gray-700">{{ $donatedMembers->count() + $notDonatedMembers->count() }}</p>
        <p class="text-sm text-gray-500 mt-1">إجمالي الأعضاء</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- ✅ Donated --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="bg-emerald-50 border-b border-emerald-100 px-5 py-3 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-emerald-800 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span>
                تم التبرع لهم
            </h2>
            <span class="text-xs bg-emerald-100 text-emerald-700 rounded-full px-2.5 py-0.5">{{ $donatedMembers->count() }}</span>
        </div>

        @if($donatedMembers->isEmpty())
            <p class="text-center text-gray-400 text-sm py-8">لا أحد بعد هذا الشهر.</p>
        @else
            <ul class="divide-y divide-gray-50">
                @foreach($donatedMembers as $member)
                @php $donations = $monthDonations->get($member->id, collect()); @endphp
                <li class="px-5 py-3 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $member->full_name }}</p>
                            <div class="flex flex-wrap gap-1 mt-0.5">
                                @foreach($donations as $d)
                                    <span class="text-xs text-gray-400">
                                        {{ number_format($d->amount, 0) }} ل.س
                                        @if($d->type === 'sham_cash')
                                            <span class="text-blue-500">(شام كاش)</span>
                                        @endif
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('donations.create') }}?member_id={{ $member->id }}"
                       class="shrink-0 text-xs text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 px-2 py-1 rounded-lg transition-colors">
                        + إضافة
                    </a>
                </li>
                @endforeach
            </ul>
        @endif
    </div>

    {{-- ❌ Not Donated --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="bg-red-50 border-b border-red-100 px-5 py-3 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-red-700 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-red-500 inline-block"></span>
                لم يُتبرع لهم بعد
            </h2>
            <span class="text-xs bg-red-100 text-red-600 rounded-full px-2.5 py-0.5">{{ $notDonatedMembers->count() }}</span>
        </div>

        @if($notDonatedMembers->isEmpty())
            <div class="text-center py-10">
                <p class="text-2xl mb-2">🎉</p>
                <p class="text-sm text-emerald-600 font-medium">رائع! تم التبرع لجميع الأعضاء هذا الشهر.</p>
            </div>
        @else
            <ul class="divide-y divide-gray-50">
                @foreach($notDonatedMembers as $member)
                <li class="px-5 py-3 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-gray-700 truncate">{{ $member->full_name }}</p>
                    </div>
                    <a href="{{ route('donations.create') }}?member_id={{ $member->id }}"
                       class="shrink-0 text-xs bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-lg transition-colors font-medium">
                        تسجيل تبرع
                    </a>
                </li>
                @endforeach
            </ul>
        @endif
    </div>

</div>

@endsection
