@extends('layouts.app')

@section('title', $delegate . ' — مسالك النور')

@section('breadcrumb')
    <a href="{{ route('delegates.index') }}" class="hover:text-sky-700 transition-colors">المندوبون</a>
    <span class="text-gray-300 mx-1">/</span>
    <span class="text-gray-700">{{ $delegate }}</span>
@endsection

@section('content')

{{-- Hero --}}
<div class="relative bg-gradient-to-l from-sky-600 via-blue-500 to-indigo-500 rounded-3xl p-6 mb-6 overflow-hidden shadow-lg">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute -top-6 -left-6 w-36 h-36 bg-white rounded-full"></div>
        <div class="absolute -bottom-10 left-20 w-52 h-52 bg-white rounded-full"></div>
    </div>
    <div class="relative flex items-center justify-between flex-wrap gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-white/20 border-2 border-white/40 flex items-center justify-center shadow-lg shrink-0">
                <span class="text-white font-black text-2xl">{{ mb_substr($delegate, 0, 1) }}</span>
            </div>
            <div>
                <h1 class="text-2xl font-black text-white">{{ $delegate }}</h1>
                <p class="text-sky-100 text-sm mt-0.5">{{ $members->total() }} عضو عند هذا المندوب</p>
            </div>
        </div>
        <a href="{{ route('delegates.index') }}"
           class="text-sm text-white/80 hover:text-white bg-white/10 hover:bg-white/20 border border-white/30 px-4 py-2 rounded-xl transition-colors">
            رجوع
        </a>
    </div>
</div>

{{-- Members table --}}
<div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
    <div class="flex items-center gap-2 px-5 py-3.5 border-b border-gray-100">
        <div class="w-7 h-7 rounded-lg bg-sky-100 flex items-center justify-center">
            <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
        </div>
        <span class="text-sm font-bold text-gray-700">الأعضاء ({{ $members->total() }})</span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-right">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="text-right font-semibold text-gray-500 text-sm px-4 py-3">#</th>
                    <th class="text-right font-semibold text-gray-500 text-sm px-4 py-3">الاسم</th>
                    <th class="text-right font-semibold text-gray-500 text-sm px-4 py-3">رقم الاضبارة</th>
                    <th class="text-right font-semibold text-gray-500 text-sm px-4 py-3">الهاتف</th>
                    <th class="text-right font-semibold text-gray-500 text-sm px-4 py-3">حالة التحقق</th>
                    <th class="text-right font-semibold text-gray-500 text-sm px-4 py-3">الحالة النهائية</th>
                    <th class="text-right font-semibold text-gray-500 text-sm px-4 py-3">المبلغ المقدر</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($members as $member)
                <tr class="hover:bg-gray-50/60 transition-colors">
                    <td class="px-4 py-3.5 text-sm text-gray-400 font-medium">{{ $member->dossier_number ?? '—' }}</td>
                    <td class="px-4 py-3.5">
                        <span class="font-semibold text-gray-800 text-sm">{{ $member->full_name }}</span>
                    </td>
                    <td class="px-4 py-3.5 text-sm text-gray-600">{{ $member->dossier_number ?? '—' }}</td>
                    <td class="px-4 py-3.5 text-sm text-gray-600 font-mono">{{ $member->phone ?? '—' }}</td>
                    <td class="px-4 py-3.5">
                        @if($member->verificationStatus)
                            <span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full border"
                                  style="color:{{ $member->verificationStatus->color }}; border-color:{{ $member->verificationStatus->color }}40; background:{{ $member->verificationStatus->color }}15">
                                <span class="w-1.5 h-1.5 rounded-full" style="background:{{ $member->verificationStatus->color }}"></span>
                                {{ $member->verificationStatus->name }}
                            </span>
                        @else
                            <span class="text-gray-300 text-sm">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3.5">
                        @if($member->finalStatus)
                            <span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full border"
                                  style="color:{{ $member->finalStatus->color }}; border-color:{{ $member->finalStatus->color }}40; background:{{ $member->finalStatus->color }}15">
                                <span class="w-1.5 h-1.5 rounded-full" style="background:{{ $member->finalStatus->color }}"></span>
                                {{ $member->finalStatus->name }}
                            </span>
                        @else
                            <span class="text-gray-300 text-sm">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3.5 text-sm font-semibold text-gray-700">
                        {{ $member->estimated_amount ? number_format($member->estimated_amount, 0) . ' ل.س' : '—' }}
                    </td>
                    <td class="px-4 py-3.5">
                        <a href="{{ route('members.show', $member) }}"
                           class="inline-flex items-center gap-1 text-xs font-semibold text-sky-600 hover:text-sky-700 bg-sky-50 hover:bg-sky-100 border border-sky-200 rounded-lg px-2.5 py-1 transition-colors">
                            عرض
                        </a>
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
</div>

@endsection
