@extends('layouts.app')

@section('title', 'الملاحظات — مسالك النور')

@section('breadcrumb')
    <span class="text-gray-700">الملاحظات</span>
@endsection

@section('content')

@php
    $uid = auth()->id();
    $isAdmin = auth()->user()->role === 'admin';
    $hasFilters = $search !== '' || $authorId || $from || $to;
    $initials = function ($name) {
        $parts = preg_split('/\s+/', trim((string) $name));
        $first = mb_substr($parts[0] ?? '', 0, 1);
        $last  = count($parts) > 1 ? mb_substr(end($parts), 0, 1) : '';
        return $first . $last ?: '؟';
    };
@endphp

{{-- ── Hero ── --}}
<div class="relative overflow-hidden rounded-2xl sm:rounded-3xl bg-gradient-to-l from-emerald-700 via-emerald-600 to-teal-500 p-5 sm:p-7 mb-5 sm:mb-6 shadow-lg">
    <div class="absolute inset-0 opacity-10 pointer-events-none">
        <div class="absolute -top-10 -right-8 w-40 h-40 sm:w-44 sm:h-44 bg-white rounded-full"></div>
        <div class="absolute -bottom-16 right-32 w-56 h-56 bg-white rounded-full"></div>
        <div class="absolute top-6 left-10 w-24 h-24 bg-white rounded-full"></div>
    </div>

    <div class="relative flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div class="flex items-start gap-3">
            <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl bg-white/15 border border-white/25 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-white">الملاحظات</h1>
                <p class="text-emerald-50/80 text-xs sm:text-sm mt-1 leading-relaxed">سجل ملاحظات الأعمال التي تخص المستفيدين، مع إمكانية إرفاق ملفات</p>
            </div>
        </div>

        <a href="{{ route('notes.create') }}"
           class="inline-flex items-center justify-center gap-2 bg-white text-emerald-700 hover:bg-emerald-50 text-sm font-bold px-5 py-2.5 rounded-xl transition-colors shadow-sm w-full sm:w-auto shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            ملاحظة جديدة
        </a>
    </div>

    <div class="relative grid grid-cols-2 sm:grid-cols-4 gap-2 sm:gap-3 mt-5 sm:mt-6">
        @foreach([
            ['الكل', $stats['total'], 'M4 6h16M4 12h16M4 18h7'],
            ['هذا الشهر', $stats['this_month'], 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
            ['بمرفقات', $stats['with_files'], 'M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13'],
            ['ملاحظاتي', $stats['mine'], 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
        ] as [$label, $value, $icon])
        <div class="bg-white/10 border border-white/15 rounded-xl sm:rounded-2xl px-3 py-2.5 sm:px-4 sm:py-3">
            <div class="flex items-center gap-1.5 text-emerald-50/70 text-[11px] sm:text-xs">
                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/></svg>
                <span class="truncate">{{ $label }}</span>
            </div>
            <p class="text-white font-black text-xl sm:text-2xl leading-none mt-1.5">{{ number_format($value) }}</p>
        </div>
        @endforeach
    </div>
</div>

{{-- ── Active person filter ── --}}
@if($person)
<div class="mb-4 flex flex-wrap items-center justify-between gap-2 text-sm bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-2.5">
    <span class="inline-flex items-center gap-2 min-w-0">
        <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
        <span class="truncate">ملاحظات المستفيد: <span class="font-bold">{{ $person->full_name }}</span></span>
    </span>
    <a href="{{ route('notes.index', array_filter(['search' => $search, 'author' => $authorId, 'from' => $from, 'to' => $to])) }}"
       class="inline-flex items-center gap-1 text-emerald-600 hover:text-emerald-900 font-bold shrink-0">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        إزالة
    </a>
</div>
@endif

{{-- ── Category chips ── --}}
@php
    $chipParams = array_filter(['search' => $search, 'author' => $authorId, 'from' => $from, 'to' => $to, 'person' => $person?->id]);
    $dotMap = ['blue'=>'bg-blue-400','indigo'=>'bg-indigo-400','emerald'=>'bg-emerald-400','red'=>'bg-red-400','violet'=>'bg-violet-400','amber'=>'bg-amber-400','gray'=>'bg-gray-400'];
@endphp
<div class="flex gap-2 overflow-x-auto pb-1 mb-4 -mx-1 px-1">
    <a href="{{ route('notes.index', $chipParams) }}"
       class="shrink-0 inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1.5 rounded-full border transition-colors {{ !$category ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
        الكل
    </a>
    @foreach($categories as $key => $c)
        <a href="{{ route('notes.index', array_merge($chipParams, ['category' => $key])) }}"
           class="shrink-0 inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1.5 rounded-full border transition-colors {{ $category === $key ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
            <span class="w-1.5 h-1.5 rounded-full {{ $category === $key ? 'bg-white' : ($dotMap[$c['color']] ?? 'bg-gray-400') }}"></span>
            {{ $c['label'] }}
        </a>
    @endforeach
</div>

{{-- ── Filters ── --}}
<form method="GET" action="{{ route('notes.index') }}" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 sm:p-5 mb-5 sm:mb-6">
    @if($category)<input type="hidden" name="category" value="{{ $category }}">@endif
    @if($person)<input type="hidden" name="person" value="{{ $person->id }}">@endif
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="lg:col-span-2">
            <label class="block text-xs font-bold text-gray-500 mb-1">بحث</label>
            <div class="relative">
                <svg class="w-4 h-4 text-gray-400 absolute top-1/2 -translate-y-1/2 right-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="search" value="{{ $search }}" placeholder="النص أو العنوان أو اسم المستفيد…"
                       class="w-full border border-gray-200 rounded-xl ps-9 pe-3 py-2.5 text-sm bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:outline-none transition-colors">
            </div>
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-500 mb-1">الكاتب</label>
            <select name="author" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:outline-none transition-colors">
                <option value="">كل الكتّاب</option>
                @foreach($authors as $a)
                    <option value="{{ $a->id }}" {{ (string) $authorId === (string) $a->id ? 'selected' : '' }}>{{ $a->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-500 mb-1">الفترة</label>
            <div class="grid grid-cols-2 gap-2">
                <input type="date" name="from" value="{{ $from }}" title="من"
                       class="w-full min-w-0 border border-gray-200 rounded-xl px-2 py-2.5 text-sm bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:outline-none transition-colors">
                <input type="date" name="to" value="{{ $to }}" title="إلى"
                       class="w-full min-w-0 border border-gray-200 rounded-xl px-2 py-2.5 text-sm bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:outline-none transition-colors">
            </div>
        </div>
    </div>
    <div class="flex flex-wrap items-center gap-2 mt-4">
        <button type="submit" class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold px-5 py-2 rounded-xl transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.879a1 1 0 00-.293-.707L3.293 7.707A1 1 0 013 7V4z"/></svg>
            تطبيق
        </button>
        @if($hasFilters)
        <a href="{{ route('notes.index', array_filter(['category' => $category, 'person' => $person?->id])) }}"
           class="inline-flex items-center gap-1.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-bold px-4 py-2 rounded-xl transition-colors">
            مسح الفلاتر
        </a>
        @endif
        <a href="{{ route('notes.export', request()->query()) }}"
           class="inline-flex items-center gap-1.5 bg-white border border-emerald-200 text-emerald-700 hover:bg-emerald-50 text-sm font-bold px-4 py-2 rounded-xl transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            تصدير Excel
        </a>
        <span class="text-xs text-gray-400 w-full sm:w-auto sm:mr-auto">{{ number_format($notes->total()) }} نتيجة</span>
    </div>
</form>

{{-- ── List ── --}}
@if($notes->isEmpty())
    <div class="bg-white rounded-2xl border border-dashed border-gray-200 p-10 sm:p-14 text-center">
        <div class="w-14 h-14 mx-auto rounded-2xl bg-emerald-50 flex items-center justify-center mb-4">
            <svg class="w-7 h-7 text-emerald-400" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        </div>
        <p class="text-gray-500 text-sm">{{ $hasFilters || $person ? 'لا توجد ملاحظات مطابقة للفلاتر.' : 'لا توجد ملاحظات بعد.' }}</p>
        <a href="{{ route('notes.create') }}" class="inline-flex items-center gap-1.5 mt-4 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold px-5 py-2.5 rounded-xl transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            أضف أول ملاحظة
        </a>
    </div>
@else
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 sm:gap-4">
        @foreach($notes as $note)
            @php $canManage = $isAdmin || $note->created_by === $uid; @endphp
            <article class="group relative bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:border-gray-200 transition-all overflow-hidden flex flex-col">
                <span class="absolute inset-y-0 right-0 w-1 {{ $note->pinned ? 'bg-amber-400' : 'bg-emerald-400/60' }}"></span>

                <div class="p-4 sm:p-5 pr-5 sm:pr-6 flex flex-col h-full">
                    <div class="flex items-start justify-between gap-2">
                        <a href="{{ route('notes.show', $note) }}" class="min-w-0">
                            <h2 class="text-[15px] sm:text-base font-bold text-gray-900 group-hover:text-emerald-700 transition-colors truncate flex items-center gap-1.5">
                                @if($note->pinned)
                                    <svg class="w-4 h-4 text-amber-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.958a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.368 2.447a1 1 0 00-.363 1.118l1.287 3.958c.3.922-.755 1.688-1.539 1.118l-3.367-2.447a1 1 0 00-1.176 0l-3.367 2.447c-.784.57-1.838-.196-1.539-1.118l1.287-3.958a1 1 0 00-.363-1.118L2.343 9.385c-.783-.57-.38-1.81.588-1.81h4.162a1 1 0 00.951-.69l1.286-3.958z"/></svg>
                                @endif
                                {{ $note->title ?: 'ملاحظة' }}
                            </h2>
                        </a>

                        @if($canManage)
                        <div class="flex items-center gap-0.5 shrink-0 -mt-1 -ml-1 opacity-100 sm:opacity-60 sm:group-hover:opacity-100 transition-opacity">
                            <a href="{{ route('notes.edit', $note) }}" title="تعديل"
                               class="p-2 sm:p-1.5 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form method="POST" action="{{ route('notes.destroy', $note) }}" onsubmit="return confirm('حذف هذه الملاحظة؟')">
                                @csrf @method('DELETE')
                                <button type="submit" title="حذف" class="p-2 sm:p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>

                    <div class="flex flex-wrap items-center gap-x-2 gap-y-1.5 mt-2 text-xs text-gray-400">
                        @include('notes._category-badge', ['note' => $note])
                        <span class="inline-flex items-center gap-1.5">
                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 font-bold text-[10px]">{{ $initials($note->creator?->name) }}</span>
                            {{ $note->creator?->name ?? '—' }}
                        </span>
                        <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                        <span title="{{ $note->exactDate() }}">{{ $note->relativeDate() }}</span>
                    </div>

                    <a href="{{ route('notes.show', $note) }}" class="block mt-3 flex-1">
                        <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-line line-clamp-3">{{ $note->body }}</p>
                    </a>

                    <div class="flex flex-wrap items-center gap-1.5 mt-4 pt-3 border-t border-gray-50">
                        @forelse($note->members->take(4) as $m)
                            <a href="{{ route('notes.index', ['person' => $m->id]) }}"
                               class="inline-flex items-center gap-1 text-xs bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-full px-2.5 py-0.5 hover:bg-emerald-100 transition-colors max-w-[45%] truncate">
                                {{ $m->full_name }}
                            </a>
                        @empty
                            <span class="text-xs text-gray-300">بدون أشخاص</span>
                        @endforelse
                        @if($note->members->count() > 4)
                            <span class="text-xs text-gray-400">+{{ $note->members->count() - 4 }}</span>
                        @endif

                        @if($note->attachments_count)
                            <span class="inline-flex items-center gap-1 text-xs text-gray-500 bg-gray-50 border border-gray-100 rounded-full px-2.5 py-0.5 mr-auto">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                {{ $note->attachments_count }}
                            </span>
                        @endif
                    </div>
                </div>
            </article>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $notes->links() }}
    </div>
@endif

@endsection
