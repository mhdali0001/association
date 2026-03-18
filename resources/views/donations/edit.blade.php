@extends('layouts.app')

@section('title', 'تعديل تبرع — مسالك النور')

@section('breadcrumb')
    <a href="{{ route('donations.index') }}" class="text-emerald-600 hover:underline">التبرعات</a>
    <span class="mx-2 text-gray-400">/</span>
    <span class="text-gray-700">تعديل</span>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">تعديل التبرع</h1>
        <p class="text-sm text-gray-400 mt-0.5">{{ $donation->member->full_name }} — {{ $donation->donation_month->translatedFormat('F Y') }}</p>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <form method="POST" action="{{ route('donations.update', $donation) }}">
            @csrf @method('PUT')
            @include('donations._form')
            <div class="flex gap-3 mt-6">
                <button type="submit"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-6 py-2.5 rounded-xl transition-colors">
                    تحديث
                </button>
                <a href="{{ route('donations.index') }}"
                   class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-6 py-2.5 rounded-xl transition-colors">
                    إلغاء
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
