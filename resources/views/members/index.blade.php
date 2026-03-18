@extends('layouts.app')

@section('title', 'الأعضاء — مسالك النور')

@section('breadcrumb')
    <a href="{{ route('members.index') }}" class="text-emerald-700 font-medium">الأعضاء</a>
@endsection

@section('content')

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">الأعضاء</h1>
            <p class="text-sm text-gray-400 mt-0.5">إجمالي النتائج: {{ $members->total() }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('members.import.show') }}"
               class="bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 text-sm font-semibold px-4 py-2.5 rounded-lg transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                استيراد Excel
            </a>
            <a href="{{ route('members.duplicates') }}"
               class="bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 text-sm font-semibold px-4 py-2.5 rounded-lg transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
                كشف التكرارات
            </a>
            <a href="{{ route('members.create') }}"
               class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                إضافة عضو جديد
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('members.index') }}" id="filter-form"
          class="bg-white border border-gray-100 rounded-2xl shadow-sm p-5 mb-6">

        {{-- Search bar --}}
        <div class="relative mb-4">
            <span class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                </svg>
            </span>
            <input type="text" name="search" value="{{ $search ?? '' }}"
                   placeholder="بحث بالاسم، رقم الهوية، الهاتف، أو رقم الملف..."
                   class="w-full pr-10 pl-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50
                          focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:bg-white transition">
        </div>

        {{-- Filter row --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-4">

            {{-- Verification status --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">حالة التحقق</label>
                <select name="verification_status_id"
                        class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50
                               focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:bg-white transition">
                    <option value="">— الكل —</option>
                    @foreach($verificationStatuses as $vs)
                        <option value="{{ $vs->id }}" {{ $verificationId == $vs->id ? 'selected' : '' }}>
                            {{ $vs->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Marital status --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">الحالة الاجتماعية</label>
                <select name="marital_status"
                        class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50
                               focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:bg-white transition">
                    <option value="">— الكل —</option>
                    @foreach($maritalStatuses as $ms)
                        <option value="{{ $ms->name }}" {{ $maritalStatus === $ms->name ? 'selected' : '' }}>
                            {{ $ms->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Gender --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">الجنس</label>
                <select name="gender"
                        class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50
                               focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:bg-white transition">
                    <option value="">— الكل —</option>
                    <option value="ذكر"   {{ $gender === 'ذكر'   ? 'selected' : '' }}>ذكر</option>
                    <option value="أنثى"  {{ $gender === 'أنثى'  ? 'selected' : '' }}>أنثى</option>
                </select>
            </div>

        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-2">
            <button type="submit"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                </svg>
                تطبيق الفلاتر
            </button>

            @if($search || $verificationId || $maritalStatus || $gender)
                <a href="{{ route('members.index') }}"
                   class="bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium px-5 py-2.5 rounded-xl transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    مسح الفلاتر
                </a>
            @endif

            {{-- Active filter badges --}}
            <div class="flex items-center gap-2 flex-wrap mr-auto">
                @if($search)
                    <span class="inline-flex items-center gap-1 text-xs bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full px-3 py-1">
                        بحث: {{ $search }}
                    </span>
                @endif
                @if($verificationId)
                    <span class="inline-flex items-center gap-1 text-xs bg-blue-50 text-blue-700 border border-blue-200 rounded-full px-3 py-1">
                        التحقق: {{ $verificationStatuses->firstWhere('id', $verificationId)?->name }}
                    </span>
                @endif
                @if($maritalStatus)
                    <span class="inline-flex items-center gap-1 text-xs bg-purple-50 text-purple-700 border border-purple-200 rounded-full px-3 py-1">
                        الحالة: {{ $maritalStatus }}
                    </span>
                @endif
                @if($gender)
                    <span class="inline-flex items-center gap-1 text-xs bg-orange-50 text-orange-700 border border-orange-200 rounded-full px-3 py-1">
                        الجنس: {{ $gender }}
                    </span>
                @endif
            </div>
        </div>
    </form>

    {{-- Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        @if($members->isEmpty())
            <div class="text-center py-20">
                <svg class="w-12 h-12 text-gray-200 mx-auto mb-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-5-3.87M9 20H4v-2a4 4 0 015-3.87m6-4.13a4 4 0 11-8 0 4 4 0 018 0zm6-4a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <p class="text-gray-400 text-sm">لا توجد نتائج مطابقة للفلاتر المحددة</p>
                <a href="{{ route('members.index') }}" class="text-emerald-600 text-sm mt-2 inline-block hover:underline">مسح الفلاتر</a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="text-right px-4 py-3 font-semibold text-gray-500 text-xs">#</th>
                            <th class="text-right px-4 py-3 font-semibold text-gray-500 text-xs">الاسم الكامل</th>
                            <th class="text-right px-4 py-3 font-semibold text-gray-500 text-xs">رقم الهوية</th>
                            <th class="text-right px-4 py-3 font-semibold text-gray-500 text-xs">رقم الهاتف</th>
                            <th class="text-right px-4 py-3 font-semibold text-gray-500 text-xs">الجنس</th>
                            <th class="text-right px-4 py-3 font-semibold text-gray-500 text-xs">الحالة الاجتماعية</th>
                            <th class="text-right px-4 py-3 font-semibold text-gray-500 text-xs">رقم الملف</th>
                            <th class="text-right px-4 py-3 font-semibold text-gray-500 text-xs">حالة التحقق</th>
                            <th class="text-right px-4 py-3 font-semibold text-gray-500 text-xs">المندوب</th>
                            <th class="text-right px-4 py-3 font-semibold text-gray-500 text-xs">المبلغ المقدر</th>
                            <th class="text-right px-4 py-3 font-semibold text-gray-500 text-xs">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($members as $member)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 text-gray-400 text-xs">{{ $member->id }}</td>
                                <td class="px-4 py-3 font-semibold text-gray-800">{{ $member->full_name }}</td>
                                <td class="px-4 py-3 text-gray-500 font-mono text-xs">{{ $member->national_id ?? '—' }}</td>
                                <td class="px-4 py-3 text-gray-500">{{ $member->phone ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    @if($member->gender)
                                        <span @class([
                                            'px-2 py-0.5 rounded-full text-xs font-medium',
                                            'bg-blue-50 text-blue-700'   => $member->gender === 'ذكر',
                                            'bg-pink-50 text-pink-700'   => $member->gender === 'أنثى',
                                            'bg-gray-100 text-gray-600'  => !in_array($member->gender, ['ذكر','أنثى']),
                                        ])>{{ $member->gender }}</span>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($member->marital_status)
                                        <span class="px-2 py-0.5 rounded-full text-xs bg-purple-50 text-purple-700">
                                            {{ $member->marital_status }}
                                        </span>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-500 font-mono text-xs">{{ $member->dossier_number ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    @if($member->verificationStatus)
                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium"
                                              style="background:{{ $member->verificationStatus->color }}20; color:{{ $member->verificationStatus->color }}">
                                            {{ $member->verificationStatus->name }}
                                        </span>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-500 text-xs">{{ $member->representative?->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-gray-600 font-mono text-xs">
                                    {{ $member->estimated_amount ? number_format($member->estimated_amount, 0) : '—' }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('members.show', $member) }}"
                                           class="text-blue-600 hover:text-blue-800 font-medium transition-colors text-xs">عرض</a>
                                        <a href="{{ route('members.edit', $member) }}"
                                           class="text-emerald-600 hover:text-emerald-800 font-medium transition-colors text-xs">تعديل</a>
                                        <form method="POST" action="{{ route('members.destroy', $member) }}"
                                              onsubmit="return confirm('هل أنت متأكد من حذف هذا العضو؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="text-red-500 hover:text-red-700 font-medium transition-colors text-xs">
                                                حذف
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($members->hasPages())
                <div class="px-5 py-4 border-t border-gray-100">
                    {{ $members->links() }}
                </div>
            @endif
        @endif
    </div>

@endsection
