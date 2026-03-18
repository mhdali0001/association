@extends('layouts.app')

@section('title', 'إضافة مصروف — مسالك النور')

@section('breadcrumb')
    <a href="{{ route('expenses.index') }}" class="text-emerald-600 hover:underline">المصروفات</a>
    <span class="mx-2 text-gray-400">/</span>
    <span class="text-gray-700">إضافة مصروف</span>
@endsection

@section('content')

<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">إضافة مصروف جديد</h1>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <form method="POST" action="{{ route('expenses.store') }}">
            @csrf
            @include('expenses._form')
            <div class="flex gap-3 mt-6">
                <button type="submit"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-6 py-2.5 rounded-xl transition-colors">
                    حفظ
                </button>
                <a href="{{ route('expenses.index') }}"
                   class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-6 py-2.5 rounded-xl transition-colors">
                    إلغاء
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
