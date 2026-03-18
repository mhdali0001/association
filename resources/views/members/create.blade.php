@extends('layouts.app')

@section('title', 'إضافة عضو — مسالك النور')
@section('max-width', 'max-w-5xl')

@section('breadcrumb')
    <a href="{{ route('members.index') }}" class="hover:text-emerald-700 transition-colors">الأعضاء</a>
    <span class="text-gray-300 mx-1">/</span>
    <span class="text-gray-700">إضافة عضو جديد</span>
@endsection

@section('content')

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <h1 class="text-xl font-bold text-gray-800 mb-6">إضافة مستفيد جديد</h1>

        @if($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 rounded-xl px-4 py-3 text-sm text-red-700">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('members.store') }}" enctype="multipart/form-data">
            @csrf
            @include('members._form')
            <div class="flex items-center gap-3 mt-8 pt-6 border-t border-gray-100">
                <button type="submit"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white font-medium px-8 py-2.5 rounded-lg transition-colors">
                    حفظ المستفيد
                </button>
                <a href="{{ route('members.index') }}"
                   class="text-gray-600 hover:text-gray-800 px-4 py-2 rounded-lg border border-gray-200 hover:border-gray-300 transition-colors text-sm">
                    إلغاء
                </a>
            </div>
        </form>
    </div>

@endsection
