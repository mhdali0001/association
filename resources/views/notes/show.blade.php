@extends('layouts.app')

@section('title', ($note->title ?: 'ملاحظة') . ' — مسالك النور')

@section('breadcrumb')
    <a href="{{ route('notes.index') }}" class="text-emerald-600 hover:underline">الملاحظات</a>
    <span class="mx-2 text-gray-400">/</span>
    <span class="text-gray-700 truncate">{{ $note->title ?: 'ملاحظة' }}</span>
@endsection

@section('content')

@php
    $canManage = auth()->user()->role === 'admin' || $note->created_by === auth()->id();
    $parts = preg_split('/\s+/', trim((string) $note->creator?->name));
    $initials = mb_substr($parts[0] ?? '؟', 0, 1) . (count($parts) > 1 ? mb_substr(end($parts), 0, 1) : '');
@endphp

<div class="max-w-3xl mx-auto space-y-4 sm:space-y-5">

    <a href="{{ route('notes.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-emerald-700 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        رجوع إلى الملاحظات
    </a>

    {{-- Main card --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="h-1.5 {{ $note->pinned ? 'bg-amber-400' : 'bg-gradient-to-l from-emerald-500 to-teal-400' }}"></div>

        <div class="p-4 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-1.5">
                        @include('notes._category-badge', ['note' => $note])
                        @if($note->pinned)
                            <span class="inline-flex items-center gap-1 text-[11px] font-bold bg-amber-50 text-amber-600 border border-amber-200 rounded-full px-2 py-0.5">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.958a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.368 2.447a1 1 0 00-.363 1.118l1.287 3.958c.3.922-.755 1.688-1.539 1.118l-3.367-2.447a1 1 0 00-1.176 0l-3.367 2.447c-.784.57-1.838-.196-1.539-1.118l1.287-3.958a1 1 0 00-.363-1.118L2.343 9.385c-.783-.57-.38-1.81.588-1.81h4.162a1 1 0 00.951-.69l1.286-3.958z"/></svg>
                                مثبّتة
                            </span>
                        @endif
                    </div>
                    <h1 class="text-lg sm:text-xl font-black text-gray-900 mt-1.5">{{ $note->title ?: 'ملاحظة' }}</h1>
                    <div class="flex flex-wrap items-center gap-x-2 gap-y-1 mt-2 text-xs text-gray-400">
                        <span class="inline-flex items-center gap-1.5">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 font-bold text-[10px]">{{ $initials ?: '؟' }}</span>
                            <span class="text-gray-600 font-medium">{{ $note->creator?->name ?? '—' }}</span>
                        </span>
                        <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                        <span title="{{ $note->exactDate() }}">{{ $note->relativeDate() }} · {{ $note->exactDate() }}</span>
                    </div>
                </div>

                @if($canManage)
                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('notes.edit', $note) }}"
                       class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-4 py-2 rounded-xl transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        تعديل
                    </a>
                    <form method="POST" action="{{ route('notes.destroy', $note) }}" class="flex-1 sm:flex-none" onsubmit="return confirm('هل أنت متأكد من حذف هذه الملاحظة؟')">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-1.5 text-sm bg-red-50 hover:bg-red-100 text-red-600 font-semibold px-4 py-2 rounded-xl transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            حذف
                        </button>
                    </form>
                </div>
                @endif
            </div>

            <div class="mt-5 text-[15px] text-gray-800 leading-8 whitespace-pre-line break-words">{{ $note->body }}</div>

            @if($note->members->isNotEmpty())
            <div class="mt-6 pt-4 border-t border-gray-100">
                <p class="text-xs font-bold text-gray-500 mb-2">المستفيدون المعنيون ({{ $note->members->count() }})</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($note->members as $m)
                        <a href="{{ route('members.show', $m) }}"
                           class="inline-flex items-center gap-1.5 text-xs bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-full px-3 py-1 hover:bg-emerald-100 transition-colors max-w-full">
                            <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <span class="truncate">{{ $m->full_name }}</span>
                            @if($m->dossier_number)<span class="text-emerald-400 font-mono shrink-0">· {{ $m->dossier_number }}</span>@endif
                        </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    @include('notes._attachments', ['note' => $note, 'canManage' => $canManage])
</div>

@endsection
