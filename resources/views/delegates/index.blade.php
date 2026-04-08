@extends('layouts.app')

@section('title', 'المندوبون — مسالك النور')

@section('breadcrumb')
    <span class="text-gray-700">المندوبون</span>
@endsection

@section('content')

{{-- Hero --}}
<div class="relative bg-gradient-to-l from-sky-600 via-blue-500 to-indigo-500 rounded-3xl p-6 mb-6 overflow-hidden shadow-lg">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute -top-6 -left-6 w-36 h-36 bg-white rounded-full"></div>
        <div class="absolute -bottom-10 left-20 w-52 h-52 bg-white rounded-full"></div>
        <div class="absolute top-4 right-10 w-20 h-20 bg-white rounded-full"></div>
    </div>
    <div class="relative flex items-start justify-between flex-wrap gap-4">
        <div>
            <h1 class="text-2xl font-black text-white">المندوبون</h1>
            <p class="text-sky-100 text-sm mt-0.5">قائمة المندوبين وعدد الأعضاء عند كل مندوب</p>
        </div>
        <div class="flex gap-3">
            <div class="bg-white/15 border border-white/30 rounded-2xl px-4 py-2 text-center">
                <p class="text-2xl font-black text-white">{{ number_format($totalDelegates) }}</p>
                <p class="text-sky-100 text-xs">مندوب</p>
            </div>
            <div class="bg-white/15 border border-white/30 rounded-2xl px-4 py-2 text-center">
                <p class="text-2xl font-black text-white">{{ number_format($totalMembers) }}</p>
                <p class="text-sky-100 text-xs">عضو مرتبط</p>
            </div>
        </div>
    </div>
</div>

{{-- Search --}}
<div class="bg-white border border-gray-100 rounded-2xl shadow-sm mb-5 p-4">
    <form method="GET" action="{{ route('delegates.index') }}">
        <div class="relative">
            <span class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                </svg>
            </span>
            <input type="text" name="search" value="{{ $search }}"
                   placeholder="بحث باسم المندوب..."
                   class="w-full pr-10 pl-4 py-3 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-sky-400 focus:bg-white transition placeholder-gray-300">
        </div>
    </form>
</div>

{{-- Grid --}}
@if($delegates->isEmpty())
    <div class="flex flex-col items-center justify-center py-20 text-center bg-white rounded-2xl border border-gray-100 shadow-sm">
        <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center mb-4">
            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-5-3.87M9 20H4v-2a4 4 0 015-3.87m6-4.13a4 4 0 11-8 0 4 4 0 018 0zm6-4a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <p class="text-gray-400 font-semibold">لا يوجد مندوبون</p>
    </div>
@else
    {{-- Table --}}
    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
        <div class="flex items-center gap-2 px-5 py-3.5 border-b border-gray-100">
            <div class="w-7 h-7 rounded-lg bg-sky-100 flex items-center justify-center">
                <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-5-3.87M9 20H4v-2a4 4 0 015-3.87m6-4.13a4 4 0 11-8 0 4 4 0 018 0zm6-4a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <span class="text-sm font-bold text-gray-700">{{ $totalDelegates }} مندوب — {{ $totalMembers }} عضو</span>
        </div>

        @php $maxCount = $delegates->max('members_count'); @endphp

        <div class="divide-y divide-gray-50">
            @foreach($delegates as $index => $delegate)
            <a href="{{ route('delegates.show', $delegate->delegate) }}"
               class="flex items-center gap-4 px-5 py-4 hover:bg-sky-50/40 transition-colors group">

                {{-- Rank --}}
                <span class="w-7 text-center text-xs font-bold text-gray-300">{{ $index + 1 }}</span>

                {{-- Avatar --}}
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-sky-400 to-blue-500 flex items-center justify-center shadow-sm shrink-0">
                    <span class="text-white font-black text-base">{{ mb_substr($delegate->delegate, 0, 1) }}</span>
                </div>

                {{-- Name --}}
                <div class="flex-1 min-w-0">
                    <p class="font-bold text-gray-800 text-sm group-hover:text-sky-700 transition-colors">{{ $delegate->delegate }}</p>
                    <div class="mt-1.5 h-1.5 bg-gray-100 rounded-full overflow-hidden w-full max-w-xs">
                        @php $pct = $maxCount > 0 ? round(($delegate->members_count / $maxCount) * 100) : 0; @endphp
                        <div class="h-full bg-gradient-to-r from-sky-400 to-blue-500 rounded-full transition-all duration-500"
                             style="width: {{ $pct }}%"></div>
                    </div>
                </div>

                {{-- Count badge --}}
                <div class="flex items-center gap-2 shrink-0">
                    <div class="text-right">
                        <span class="text-2xl font-black text-sky-600">{{ number_format($delegate->members_count) }}</span>
                        <span class="text-xs text-gray-400 mr-1">عضو</span>
                    </div>
                    <svg class="w-4 h-4 text-gray-300 group-hover:text-sky-400 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                    </svg>
                </div>
            </a>
            @endforeach
        </div>
    </div>
@endif

@endsection
