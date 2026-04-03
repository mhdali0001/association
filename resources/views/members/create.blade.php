@extends('layouts.app')

@section('title', 'إضافة عضو — مسالك النور')
@section('max-width', 'max-w-5xl')

@section('breadcrumb')
    <a href="{{ route('members.index') }}" class="hover:text-emerald-700 transition-colors">الأعضاء</a>
    <span class="text-gray-300 mx-1">/</span>
    <span class="text-gray-700">إضافة عضو جديد</span>
@endsection

@section('content')

{{-- Hero --}}
<div class="relative bg-gradient-to-l from-emerald-600 via-emerald-500 to-teal-500 rounded-3xl p-6 mb-6 overflow-hidden shadow-lg">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute -top-6 -left-6 w-36 h-36 bg-white rounded-full"></div>
        <div class="absolute -bottom-10 left-20 w-52 h-52 bg-white rounded-full"></div>
        <div class="absolute top-4 right-10 w-20 h-20 bg-white rounded-full"></div>
    </div>
    <div class="relative flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm border border-white/30">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-5-3a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-black text-white">إضافة مستفيد جديد</h1>
                <p class="text-emerald-100 text-sm mt-0.5">أدخل بيانات المستفيد في الحقول أدناه</p>
            </div>
        </div>
        <a href="{{ route('members.index') }}"
           class="text-sm text-white/80 hover:text-white bg-white/10 hover:bg-white/20 border border-white/30 px-4 py-2 rounded-xl transition-colors backdrop-blur-sm">
            رجوع
        </a>
    </div>
</div>

@if($errors->any())
    <div class="flex items-start gap-3 mb-6 bg-red-50 border border-red-200 rounded-2xl px-5 py-4">
        <div class="w-8 h-8 bg-red-100 rounded-xl flex items-center justify-center shrink-0 mt-0.5">
            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-sm font-bold text-red-700 mb-1">يرجى تصحيح الأخطاء التالية:</p>
            <ul class="space-y-0.5">
                @foreach($errors->all() as $error)
                    <li class="text-sm text-red-600 flex items-center gap-1.5">
                        <span class="w-1 h-1 bg-red-400 rounded-full shrink-0"></span>{{ $error }}
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<form method="POST" action="{{ route('members.store') }}" enctype="multipart/form-data">
    @csrf
    @include('members._form')
    <div class="flex items-center gap-3 mt-8 pt-6 border-t border-gray-100">
        <button type="submit"
                class="flex items-center gap-2 bg-gradient-to-l from-emerald-600 to-teal-500 hover:from-emerald-700 hover:to-teal-600 text-white font-bold px-8 py-3 rounded-xl transition-all shadow-md hover:shadow-lg">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
            حفظ المستفيد
        </button>
        <a href="{{ route('members.index') }}"
           class="text-gray-500 hover:text-gray-700 px-5 py-3 rounded-xl border border-gray-200 hover:border-gray-300 hover:bg-gray-50 transition-colors text-sm font-medium">
            إلغاء
        </a>
    </div>
</form>

@endsection
