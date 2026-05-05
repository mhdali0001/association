@extends('layouts.app')

@section('title', 'الأعضاء المنقوصة نقاطهم — مسالك النور')
@section('max-width', 'max-w-7xl')

@section('breadcrumb')
    <a href="{{ route('members.index') }}" class="hover:text-amber-700 transition-colors">الأعضاء</a>
    <span class="text-gray-300 mx-1">/</span>
    <span class="text-gray-700">انقاص النقاط</span>
@endsection

@section('content')

{{-- Hero --}}
<div class="relative bg-gradient-to-l from-red-600 via-red-500 to-orange-500 rounded-3xl p-6 mb-6 overflow-hidden shadow-lg">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute -top-6 -left-6 w-36 h-36 bg-white rounded-full"></div>
        <div class="absolute -bottom-10 left-20 w-52 h-52 bg-white rounded-full"></div>
        <div class="absolute top-4 right-10 w-20 h-20 bg-white rounded-full"></div>
    </div>
    <div class="relative flex items-start justify-between flex-wrap gap-4">
        <div>
            <h1 class="text-2xl font-black text-white">الأعضاء المنقوصة نقاطهم</h1>
            <p class="text-red-100 text-sm mt-0.5">الأعضاء الذين تم تطبيق انقاص نقاط عليهم</p>
        </div>
        <div class="flex gap-3 flex-wrap">
            <div class="bg-white/15 border border-white/25 rounded-xl px-4 py-2.5 text-center min-w-[140px]">
                <p class="text-white font-black text-xl leading-none">{{ number_format($totalCount) }}</p>
                <p class="text-red-200 text-xs mt-0.5">إجمالي الأعضاء</p>
            </div>
            <div class="bg-white/15 border border-white/25 rounded-xl px-4 py-2.5 text-center min-w-[140px]">
                <p class="text-white font-black text-xl leading-none">{{ number_format($totalDeduction, 1) }}</p>
                <p class="text-orange-200 text-xs mt-0.5">مجموع النقاط المنقوصة</p>
            </div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-5">
    <form method="GET" action="{{ route('members.score-deductions') }}" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-bold text-gray-500 mb-1">بحث (اسم / رقم اضبارة / هوية)</label>
            <input type="text" name="search" value="{{ $search }}" placeholder="ابحث..."
                   class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-red-300 focus:outline-none bg-gray-50">
        </div>
        <div class="min-w-[200px]">
            <label class="block text-xs font-bold text-gray-500 mb-1">سبب الانقاص</label>
            <input type="text" name="reason" value="{{ $reasonFilter }}" placeholder="ابحث في السبب..."
                   class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-red-300 focus:outline-none bg-gray-50"
                   list="reason-list">
            <datalist id="reason-list">
                @foreach($reasonList as $r)
                    <option value="{{ $r }}">
                @endforeach
            </datalist>
        </div>
        <div class="min-w-[160px]">
            <label class="block text-xs font-bold text-gray-500 mb-1">الترتيب</label>
            <select name="sort" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-red-300 focus:outline-none bg-gray-50">
                <option value="deduction_desc" {{ $sortBy === 'deduction_desc' ? 'selected' : '' }}>الانقاص (الأعلى أولاً)</option>
                <option value="deduction_asc"  {{ $sortBy === 'deduction_asc'  ? 'selected' : '' }}>الانقاص (الأقل أولاً)</option>
                <option value="name"           {{ $sortBy === 'name'           ? 'selected' : '' }}>الاسم</option>
                <option value="dossier"        {{ $sortBy === 'dossier'        ? 'selected' : '' }}>رقم الاضبارة</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit"
                    class="flex items-center gap-1.5 bg-red-600 hover:bg-red-700 text-white text-sm font-bold px-5 py-2 rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                بحث
            </button>
            @if($search || $reasonFilter)
            <a href="{{ route('members.score-deductions') }}"
               class="flex items-center gap-1.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-bold px-4 py-2 rounded-xl transition-colors">
                مسح
            </a>
            @endif
        </div>
    </form>
</div>

{{-- Table --}}
@if($members->isEmpty())
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/>
            </svg>
        </div>
        <p class="text-gray-400 text-sm font-medium">لا يوجد أعضاء منقوصة نقاطهم</p>
    </div>
@else
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-100 bg-gray-50/50">
        <p class="text-sm font-bold text-gray-600">
            {{ number_format($members->total()) }} عضو
            @if($search || $reasonFilter)
                <span class="text-xs text-gray-400 font-normal mr-1">(نتائج البحث)</span>
            @endif
        </p>
        <p class="text-xs text-gray-400">صفحة {{ $members->currentPage() }} من {{ $members->lastPage() }}</p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50/30">
                    <th class="text-right font-semibold text-gray-500 px-4 py-3 whitespace-nowrap">#</th>
                    <th class="text-right font-semibold text-gray-500 px-4 py-3 whitespace-nowrap">الاسم</th>
                    <th class="text-right font-semibold text-gray-500 px-4 py-3 whitespace-nowrap">رقم الاضبارة</th>
                    <th class="text-right font-semibold text-gray-500 px-4 py-3 whitespace-nowrap">النقاط المنقوصة</th>
                    <th class="text-right font-semibold text-gray-500 px-4 py-3">سبب الانقاص</th>
                    <th class="text-right font-semibold text-gray-500 px-4 py-3 whitespace-nowrap">النقاط الحالية</th>
                    <th class="text-right font-semibold text-gray-500 px-4 py-3 whitespace-nowrap">المبلغ المقدر</th>
                    <th class="text-right font-semibold text-gray-500 px-4 py-3 whitespace-nowrap">المبلغ النهائي</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($members as $index => $member)
                <tr class="hover:bg-red-50/30 transition-colors">
                    <td class="px-4 py-3 text-gray-400 text-xs">
                        {{ ($members->currentPage() - 1) * $members->perPage() + $loop->iteration }}
                    </td>
                    <td class="px-4 py-3">
                        <p class="font-semibold text-gray-800 whitespace-nowrap">{{ $member->full_name }}</p>
                        @if($member->verificationStatus)
                            <span class="text-xs px-1.5 py-0.5 rounded-md font-medium"
                                  style="background: {{ $member->verificationStatus->color }}22; color: {{ $member->verificationStatus->color }};">
                                {{ $member->verificationStatus->name }}
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-600 font-mono text-xs whitespace-nowrap">
                        {{ $member->dossier_number ?: '—' }}
                    </td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center gap-1 bg-red-100 text-red-700 font-black text-sm px-2.5 py-1 rounded-lg">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/>
                            </svg>
                            {{ number_format($member->score_deduction, 1) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-600 text-xs max-w-[200px]">
                        {{ $member->score_deduction_reason ?: '—' }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="font-bold text-gray-700">{{ number_format($member->score ?? $member->total_score, 1) }}</span>
                    </td>
                    <td class="px-4 py-3 text-gray-700 whitespace-nowrap">
                        {{ number_format($member->estimated_amount ?? 0) }} ل.س
                    </td>
                    <td class="px-4 py-3 text-purple-700 font-semibold whitespace-nowrap">
                        {{ number_format($member->final_amount) }} ل.س
                    </td>
                    <td class="px-4 py-3">
                        <a href="{{ route('members.show', $member->id) }}"
                           class="inline-flex items-center gap-1 text-xs font-bold text-gray-500 hover:text-red-600 bg-gray-100 hover:bg-red-50 border border-gray-200 hover:border-red-200 px-2.5 py-1.5 rounded-lg transition-colors whitespace-nowrap">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            عرض
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($members->hasPages())
    <div class="border-t border-gray-100 px-5 py-3 bg-gray-50/30">
        {{ $members->links() }}
    </div>
    @endif
</div>
@endif

@endsection
