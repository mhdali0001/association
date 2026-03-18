@extends('layouts.app')

@section('title', 'كشف التكرارات — مسالك النور')

@section('breadcrumb')
    <a href="{{ route('members.index') }}" class="hover:text-emerald-700 transition-colors">الأعضاء</a>
    <span class="text-gray-300 mx-1">/</span>
    <span class="text-gray-700">كشف التكرارات</span>
@endsection

@section('content')

{{-- Header --}}
<div class="flex items-start justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
            <span class="w-9 h-9 rounded-xl bg-red-100 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
            </span>
            كشف التكرارات
        </h1>
        <p class="text-sm text-gray-400 mt-1 mr-12">
            تم العثور على
            <span class="font-bold text-red-600">{{ $totalGroups }}</span>
            مجموعة مكررة في قاعدة البيانات
        </p>
    </div>
    <a href="{{ route('members.index') }}"
       class="text-sm text-gray-500 hover:text-gray-700 border border-gray-200 hover:border-gray-300 px-4 py-2 rounded-lg transition-colors">
        ← رجوع للأعضاء
    </a>
</div>

{{-- Filters --}}
<form method="GET" action="{{ route('members.duplicates') }}"
      class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-6 flex flex-wrap gap-3 items-end">

    {{-- Search --}}
    <div class="flex-1 min-w-[180px]">
        <label class="block text-xs font-medium text-gray-500 mb-1">بحث</label>
        <input type="text" name="search" value="{{ $search }}"
               placeholder="اسم، هوية، هاتف…"
               class="w-full border-2 border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
    </div>

    {{-- Type --}}
    <div class="min-w-[160px]">
        <label class="block text-xs font-medium text-gray-500 mb-1">نوع التكرار</label>
        <select name="type" class="w-full border-2 border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            <option value="all"         {{ $type === 'all'         ? 'selected' : '' }}>الكل</option>
            <option value="national_id" {{ $type === 'national_id' ? 'selected' : '' }}>رقم الهوية</option>
            <option value="phone"       {{ $type === 'phone'       ? 'selected' : '' }}>رقم الهاتف</option>
            <option value="name"        {{ $type === 'name'        ? 'selected' : '' }}>الاسم</option>
        </select>
    </div>

    {{-- Verification status --}}
    <div class="min-w-[180px]">
        <label class="block text-xs font-medium text-gray-500 mb-1">حالة التحقق</label>
        <select name="verification_status_id" class="w-full border-2 border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            <option value="">— الكل —</option>
            @foreach($verificationStatuses as $vs)
                <option value="{{ $vs->id }}" {{ $verificationId == $vs->id ? 'selected' : '' }}>
                    {{ $vs->name }}
                </option>
            @endforeach
        </select>
    </div>

    <button type="submit"
            class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-5 py-2 rounded-xl transition-colors">
        تطبيق
    </button>

    @if($search || $verificationId || $type !== 'all')
        <a href="{{ route('members.duplicates') }}"
           class="text-sm text-gray-400 hover:text-gray-600 px-4 py-2 rounded-xl border border-gray-200 hover:border-gray-300 transition-colors">
            مسح
        </a>
    @endif
</form>

{{-- Summary cards --}}
<div class="grid grid-cols-3 gap-4 mb-8">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-red-50 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"/>
            </svg>
        </div>
        <div>
            <p class="text-2xl font-black text-gray-900">{{ $byNationalId->count() }}</p>
            <p class="text-xs text-gray-400 mt-0.5">تكرار في رقم الهوية</p>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-orange-50 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
            </svg>
        </div>
        <div>
            <p class="text-2xl font-black text-gray-900">{{ $byPhone->count() }}</p>
            <p class="text-xs text-gray-400 mt-0.5">تكرار في رقم الهاتف</p>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-yellow-50 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
        </div>
        <div>
            <p class="text-2xl font-black text-gray-900">{{ $byName->count() }}</p>
            <p class="text-xs text-gray-400 mt-0.5">تكرار في الاسم</p>
        </div>
    </div>
</div>

@if($totalGroups === 0)
    <div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-12 text-center">
        <svg class="w-12 h-12 text-emerald-400 mx-auto mb-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-emerald-700 font-semibold text-lg">لا توجد تكرارات!</p>
        <p class="text-emerald-500 text-sm mt-1">جميع البيانات فريدة وغير مكررة</p>
    </div>
@else

    {{-- ═══════════════ SECTION 1: National ID ═══════════════ --}}
    @if($byNationalId->count())
    <div class="mb-8">
        <div class="flex items-center gap-3 mb-4">
            <span class="w-7 h-7 rounded-lg bg-red-100 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"/>
                </svg>
            </span>
            <h2 class="text-base font-bold text-gray-800">تكرار رقم الهوية</h2>
            <span class="text-xs bg-red-100 text-red-700 rounded-full px-2.5 py-0.5 font-semibold">
                {{ $byNationalId->count() }} مجموعة
            </span>
        </div>

        <div class="space-y-4">
            @foreach($byNationalId as $nid => $group)
            <div class="bg-white rounded-2xl border border-red-100 shadow-sm overflow-hidden">
                <div class="bg-red-50 border-b border-red-100 px-5 py-2.5 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-semibold text-red-600">رقم الهوية:</span>
                        <span class="font-mono text-sm font-bold text-red-700">{{ $nid }}</span>
                    </div>
                    <span class="text-xs bg-red-100 text-red-700 rounded-full px-2.5 py-0.5 font-semibold">
                        {{ $group->count() }} سجلات
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="text-right px-4 py-2.5 font-semibold text-gray-500 text-xs">#</th>
                                <th class="text-right px-4 py-2.5 font-semibold text-gray-500 text-xs">الاسم الكامل</th>
                                <th class="text-right px-4 py-2.5 font-semibold text-gray-500 text-xs">رقم الهاتف</th>
                                <th class="text-right px-4 py-2.5 font-semibold text-gray-500 text-xs">رقم الملف</th>
                                <th class="text-right px-4 py-2.5 font-semibold text-gray-500 text-xs">حالة التحقق</th>
                                <th class="text-right px-4 py-2.5 font-semibold text-gray-500 text-xs">تاريخ التسجيل</th>
                                <th class="text-right px-4 py-2.5 font-semibold text-gray-500 text-xs">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($group as $member)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-2.5 text-gray-400 text-xs">{{ $member->id }}</td>
                                <td class="px-4 py-2.5 font-semibold text-gray-800">{{ $member->full_name }}</td>
                                <td class="px-4 py-2.5 text-gray-500">{{ $member->phone ?? '—' }}</td>
                                <td class="px-4 py-2.5 text-gray-500 font-mono text-xs">{{ $member->dossier_number ?? '—' }}</td>
                                <td class="px-4 py-2.5">
                                    @if($member->verificationStatus)
                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium"
                                              style="background:{{ $member->verificationStatus->color }}18; color:{{ $member->verificationStatus->color }}">
                                            {{ $member->verificationStatus->name }}
                                        </span>
                                    @else
                                        <span class="text-gray-300 text-xs">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2.5 text-gray-400 text-xs">{{ $member->created_at->format('Y/m/d') }}</td>
                                <td class="px-4 py-2.5">
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('members.show', $member) }}"
                                           class="text-blue-600 hover:text-blue-800 text-xs font-medium">عرض</a>
                                        <a href="{{ route('members.edit', $member) }}"
                                           class="text-emerald-600 hover:text-emerald-800 text-xs font-medium">تعديل</a>
                                        <form method="POST" action="{{ route('members.destroy', $member) }}"
                                              onsubmit="return confirm('هل أنت متأكد من حذف هذا العضو؟')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-medium">حذف</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ═══════════════ SECTION 2: Phone ═══════════════ --}}
    @if($byPhone->count())
    <div class="mb-8">
        <div class="flex items-center gap-3 mb-4">
            <span class="w-7 h-7 rounded-lg bg-orange-100 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
            </span>
            <h2 class="text-base font-bold text-gray-800">تكرار رقم الهاتف</h2>
            <span class="text-xs bg-orange-100 text-orange-700 rounded-full px-2.5 py-0.5 font-semibold">
                {{ $byPhone->count() }} مجموعة
            </span>
        </div>

        <div class="space-y-4">
            @foreach($byPhone as $phone => $group)
            <div class="bg-white rounded-2xl border border-orange-100 shadow-sm overflow-hidden">
                <div class="bg-orange-50 border-b border-orange-100 px-5 py-2.5 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-semibold text-orange-600">رقم الهاتف:</span>
                        <span class="font-mono text-sm font-bold text-orange-700">{{ $phone }}</span>
                    </div>
                    <span class="text-xs bg-orange-100 text-orange-700 rounded-full px-2.5 py-0.5 font-semibold">
                        {{ $group->count() }} سجلات
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="text-right px-4 py-2.5 font-semibold text-gray-500 text-xs">#</th>
                                <th class="text-right px-4 py-2.5 font-semibold text-gray-500 text-xs">الاسم الكامل</th>
                                <th class="text-right px-4 py-2.5 font-semibold text-gray-500 text-xs">رقم الهوية</th>
                                <th class="text-right px-4 py-2.5 font-semibold text-gray-500 text-xs">رقم الملف</th>
                                <th class="text-right px-4 py-2.5 font-semibold text-gray-500 text-xs">حالة التحقق</th>
                                <th class="text-right px-4 py-2.5 font-semibold text-gray-500 text-xs">تاريخ التسجيل</th>
                                <th class="text-right px-4 py-2.5 font-semibold text-gray-500 text-xs">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($group as $member)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-2.5 text-gray-400 text-xs">{{ $member->id }}</td>
                                <td class="px-4 py-2.5 font-semibold text-gray-800">{{ $member->full_name }}</td>
                                <td class="px-4 py-2.5 text-gray-500 font-mono text-xs">{{ $member->national_id ?? '—' }}</td>
                                <td class="px-4 py-2.5 text-gray-500 font-mono text-xs">{{ $member->dossier_number ?? '—' }}</td>
                                <td class="px-4 py-2.5">
                                    @if($member->verificationStatus)
                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium"
                                              style="background:{{ $member->verificationStatus->color }}18; color:{{ $member->verificationStatus->color }}">
                                            {{ $member->verificationStatus->name }}
                                        </span>
                                    @else
                                        <span class="text-gray-300 text-xs">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2.5 text-gray-400 text-xs">{{ $member->created_at->format('Y/m/d') }}</td>
                                <td class="px-4 py-2.5">
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('members.show', $member) }}"
                                           class="text-blue-600 hover:text-blue-800 text-xs font-medium">عرض</a>
                                        <a href="{{ route('members.edit', $member) }}"
                                           class="text-emerald-600 hover:text-emerald-800 text-xs font-medium">تعديل</a>
                                        <form method="POST" action="{{ route('members.destroy', $member) }}"
                                              onsubmit="return confirm('هل أنت متأكد من حذف هذا العضو؟')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-medium">حذف</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ═══════════════ SECTION 3: Name ═══════════════ --}}
    @if($byName->count())
    <div class="mb-8">
        <div class="flex items-center gap-3 mb-4">
            <span class="w-7 h-7 rounded-lg bg-yellow-100 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </span>
            <h2 class="text-base font-bold text-gray-800">تكرار الاسم</h2>
            <span class="text-xs bg-yellow-100 text-yellow-700 rounded-full px-2.5 py-0.5 font-semibold">
                {{ $byName->count() }} مجموعة
            </span>
        </div>

        <div class="space-y-4">
            @foreach($byName as $name => $group)
            <div class="bg-white rounded-2xl border border-yellow-100 shadow-sm overflow-hidden">
                <div class="bg-yellow-50 border-b border-yellow-100 px-5 py-2.5 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-semibold text-yellow-700">الاسم:</span>
                        <span class="text-sm font-bold text-yellow-800">{{ $name }}</span>
                    </div>
                    <span class="text-xs bg-yellow-100 text-yellow-700 rounded-full px-2.5 py-0.5 font-semibold">
                        {{ $group->count() }} سجلات
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="text-right px-4 py-2.5 font-semibold text-gray-500 text-xs">#</th>
                                <th class="text-right px-4 py-2.5 font-semibold text-gray-500 text-xs">رقم الهوية</th>
                                <th class="text-right px-4 py-2.5 font-semibold text-gray-500 text-xs">رقم الهاتف</th>
                                <th class="text-right px-4 py-2.5 font-semibold text-gray-500 text-xs">رقم الملف</th>
                                <th class="text-right px-4 py-2.5 font-semibold text-gray-500 text-xs">الحالة الاجتماعية</th>
                                <th class="text-right px-4 py-2.5 font-semibold text-gray-500 text-xs">حالة التحقق</th>
                                <th class="text-right px-4 py-2.5 font-semibold text-gray-500 text-xs">تاريخ التسجيل</th>
                                <th class="text-right px-4 py-2.5 font-semibold text-gray-500 text-xs">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($group as $member)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-2.5 text-gray-400 text-xs">{{ $member->id }}</td>
                                <td class="px-4 py-2.5 text-gray-500 font-mono text-xs">{{ $member->national_id ?? '—' }}</td>
                                <td class="px-4 py-2.5 text-gray-500">{{ $member->phone ?? '—' }}</td>
                                <td class="px-4 py-2.5 text-gray-500 font-mono text-xs">{{ $member->dossier_number ?? '—' }}</td>
                                <td class="px-4 py-2.5">
                                    @if($member->marital_status)
                                        <span class="px-2 py-0.5 rounded-full text-xs bg-purple-50 text-purple-700">
                                            {{ $member->marital_status }}
                                        </span>
                                    @else
                                        <span class="text-gray-300 text-xs">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2.5">
                                    @if($member->verificationStatus)
                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium"
                                              style="background:{{ $member->verificationStatus->color }}18; color:{{ $member->verificationStatus->color }}">
                                            {{ $member->verificationStatus->name }}
                                        </span>
                                    @else
                                        <span class="text-gray-300 text-xs">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2.5 text-gray-400 text-xs">{{ $member->created_at->format('Y/m/d') }}</td>
                                <td class="px-4 py-2.5">
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('members.show', $member) }}"
                                           class="text-blue-600 hover:text-blue-800 text-xs font-medium">عرض</a>
                                        <a href="{{ route('members.edit', $member) }}"
                                           class="text-emerald-600 hover:text-emerald-800 text-xs font-medium">تعديل</a>
                                        <form method="POST" action="{{ route('members.destroy', $member) }}"
                                              onsubmit="return confirm('هل أنت متأكد من حذف هذا العضو؟')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-medium">حذف</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

@endif

@endsection
