@extends('layouts.app')

@section('title', 'ملاحظة جديدة — مسالك النور')

@section('breadcrumb')
    <a href="{{ route('notes.index') }}" class="text-emerald-600 hover:underline">الملاحظات</a>
    <span class="mx-2 text-gray-400">/</span>
    <span class="text-gray-700">ملاحظة جديدة</span>
@endsection

@section('content')

<div class="max-w-3xl mx-auto space-y-4 sm:space-y-5">

    <a href="{{ route('notes.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-emerald-700 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        رجوع إلى الملاحظات
    </a>

    <div class="flex items-center gap-3">
        <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-emerald-100 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        </div>
        <div>
            <h1 class="text-lg sm:text-xl font-black text-gray-900">ملاحظة جديدة</h1>
            <p class="text-xs sm:text-sm text-gray-400">اكتب الملاحظة، اربطها بمستفيدين، وأرفق ملفات إن لزم</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="h-1.5 bg-gradient-to-l from-emerald-500 to-teal-400"></div>
        <form method="POST" action="{{ route('notes.store') }}" enctype="multipart/form-data" class="p-4 sm:p-6">
            @csrf
            @include('notes._form')
            <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 mt-6 sm:mt-7 pt-5 border-t border-gray-100">
                <button type="submit"
                        class="inline-flex items-center justify-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-6 py-2.5 rounded-xl transition-colors w-full sm:w-auto">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    حفظ الملاحظة
                </button>
                <a href="{{ route('notes.index') }}"
                   class="inline-flex items-center justify-center bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-6 py-2.5 rounded-xl transition-colors w-full sm:w-auto">
                    إلغاء
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
