@extends('layouts.app')

@section('title', 'الموظفون — مسالك النور')

@section('breadcrumb')
    <span class="text-gray-700">الموظفون</span>
@endsection

@section('content')

@if(session('success'))
    <div class="mb-5 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium rounded-2xl px-5 py-3.5">
        <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('success') }}
    </div>
@endif

{{-- Hero --}}
<div class="relative bg-gradient-to-l from-slate-900 via-slate-800 to-slate-700 rounded-3xl overflow-hidden shadow-xl mb-6">
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-12 -right-12 w-64 h-64 bg-white/5 rounded-full"></div>
        <div class="absolute -bottom-20 left-8 w-80 h-80 bg-white/5 rounded-full"></div>
        <div class="absolute top-8 left-52 w-32 h-32 bg-white/5 rounded-full"></div>
        <div class="absolute inset-0 opacity-5" style="background-image:linear-gradient(rgba(255,255,255,.2) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.2) 1px,transparent 1px);background-size:40px 40px"></div>
    </div>
    <div class="relative p-7">
        <div class="flex items-start justify-between flex-wrap gap-4 mb-7">
            <div>
                <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-2">إدارة الموارد البشرية</p>
                <h1 class="text-3xl font-black text-white leading-none mb-1">الموظفون</h1>
                <p class="text-slate-400 text-sm">رواتب · إضافات · خصومات · سلف</p>
            </div>
            <button onclick="document.getElementById('modal-create').classList.remove('hidden')"
                    class="flex items-center gap-2 bg-white hover:bg-slate-50 text-slate-800 text-sm font-bold px-5 py-3 rounded-2xl transition-all shadow-lg hover:shadow-xl shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                إضافة موظف
            </button>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            <div class="bg-white/10 border border-white/15 rounded-2xl p-4">
                <div class="w-8 h-8 rounded-xl bg-white/15 flex items-center justify-center mb-3">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <p class="text-white font-black text-2xl leading-none">{{ $employees->count() }}</p>
                <p class="text-slate-300 text-xs mt-1.5">موظف</p>
                <p class="text-slate-500 text-xs mt-0.5">{{ $employees->where('is_active', true)->count() }} نشط · {{ $employees->where('is_active', false)->count() }} غير نشط</p>
            </div>
            <div class="bg-white/10 border border-white/15 rounded-2xl p-4">
                <div class="w-8 h-8 rounded-xl bg-emerald-500/30 flex items-center justify-center mb-3">
                    <svg class="w-4 h-4 text-emerald-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <p class="text-slate-300 text-xs mb-1.5">إجمالي المدفوع</p>
                @if($totalPaidSYP > 0)
                    <p class="text-white font-black text-lg leading-tight">{{ number_format($totalPaidSYP) }} <span class="text-xs font-normal text-slate-400">ل.س</span></p>
                @endif
                @if($totalPaidUSD > 0)
                    <p class="text-white font-black text-lg leading-tight">{{ number_format($totalPaidUSD, 2) }} <span class="text-xs font-normal text-slate-400">$</span></p>
                @endif
                @if($totalPaidSYP == 0 && $totalPaidUSD == 0)
                    <p class="text-white font-black text-lg">—</p>
                @endif
            </div>
            <div class="bg-white/10 border border-white/15 rounded-2xl p-4">
                <div class="w-8 h-8 rounded-xl bg-red-500/30 flex items-center justify-center mb-3">
                    <svg class="w-4 h-4 text-red-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="text-slate-300 text-xs mb-1.5">إجمالي الخصومات</p>
                @if($totalDeductedSYP > 0)
                    <p class="text-white font-black text-lg leading-tight">{{ number_format($totalDeductedSYP) }} <span class="text-xs font-normal text-slate-400">ل.س</span></p>
                @endif
                @if($totalDeductedUSD > 0)
                    <p class="text-white font-black text-lg leading-tight">{{ number_format($totalDeductedUSD, 2) }} <span class="text-xs font-normal text-slate-400">$</span></p>
                @endif
                @if($totalDeductedSYP == 0 && $totalDeductedUSD == 0)
                    <p class="text-white font-black text-lg">—</p>
                @endif
            </div>
            @php
                $globalNetSYP = $totalPaidSYP - $totalDeductedSYP;
                $globalNetUSD = $totalPaidUSD - $totalDeductedUSD;
            @endphp
            <div class="bg-white/10 border border-white/15 rounded-2xl p-4">
                <div class="w-8 h-8 rounded-xl bg-amber-500/30 flex items-center justify-center mb-3">
                    <svg class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
                <p class="text-slate-300 text-xs mb-1.5">صافي الإنفاق</p>
                @if($globalNetSYP != 0 || ($totalPaidSYP == 0 && $totalPaidUSD == 0))
                    <p class="text-white font-black text-lg leading-tight">{{ number_format($globalNetSYP) }} <span class="text-xs font-normal text-slate-400">ل.س</span></p>
                @endif
                @if($globalNetUSD != 0)
                    <p class="text-white font-black text-lg leading-tight">{{ number_format($globalNetUSD, 2) }} <span class="text-xs font-normal text-slate-400">$</span></p>
                @endif
            </div>
        </div>
    </div>
</div>

@if($employees->isEmpty())
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm text-center py-28">
        <div class="w-20 h-20 bg-slate-50 rounded-3xl flex items-center justify-center mx-auto mb-5">
            <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </div>
        <p class="text-gray-700 font-bold text-lg mb-1">لا يوجد موظفون بعد</p>
        <p class="text-gray-400 text-sm mb-6">ابدأ بإضافة أول موظف للفريق</p>
        <button onclick="document.getElementById('modal-create').classList.remove('hidden')"
                class="inline-flex items-center gap-2 bg-slate-700 hover:bg-slate-800 text-white text-sm font-bold px-6 py-3 rounded-2xl transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            إضافة موظف
        </button>
    </div>
@else

{{-- Search --}}
<div class="relative mb-5">
    <span class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/></svg>
    </span>
    <input type="text" id="emp-search" placeholder="ابحث باسم الموظف أو المسمى الوظيفي..."
           class="w-full pr-11 pl-4 py-3 bg-white border border-gray-200 rounded-2xl text-sm shadow-sm focus:ring-2 focus:ring-slate-300 focus:outline-none"
           oninput="filterCards(this.value)">
</div>

{{-- Grid --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5" id="emp-grid">
    @foreach($employees as $employee)
    @php
        $netSYP = $employee->netBalance('SYP');
        $netUSD = $employee->netBalance('USD');
        $hasSYP = $employee->hasCurrency('SYP');
        $hasUSD = $employee->hasCurrency('USD');
    @endphp
    <a href="{{ route('employees.show', $employee) }}"
       data-name="{{ mb_strtolower($employee->name . ' ' . $employee->job_title) }}"
       class="emp-card bg-white rounded-3xl border border-gray-100 shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 overflow-hidden group flex flex-col">

        {{-- Top accent bar --}}
        <div class="h-1 {{ $employee->is_active ? 'bg-gradient-to-r from-slate-600 to-slate-500' : 'bg-gray-200' }}"></div>

        <div class="p-5 flex-1">
            {{-- Header --}}
            <div class="flex items-start gap-3 mb-5">
                <div class="relative shrink-0">
                    <div class="w-13 h-13 w-[52px] h-[52px] rounded-2xl {{ $employee->is_active ? 'bg-gradient-to-br from-slate-600 to-slate-800' : 'bg-gray-300' }} flex items-center justify-center text-white font-black text-xl shadow-sm">
                        {{ mb_substr($employee->name, 0, 1) }}
                    </div>
                    @if($employee->is_active)
                        <div class="absolute -bottom-0.5 -left-0.5 w-3.5 h-3.5 bg-emerald-400 rounded-full border-2 border-white"></div>
                    @endif
                </div>
                <div class="min-w-0 flex-1">
                    <p class="font-bold text-gray-900 text-base leading-tight group-hover:text-slate-700 transition-colors truncate">{{ $employee->name }}</p>
                    <p class="text-gray-400 text-xs mt-0.5 truncate">{{ $employee->job_title ?: '—' }}</p>
                    @if($employee->phone)
                        <p class="text-gray-400 text-xs mt-1 flex items-center gap-1">
                            <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            {{ $employee->phone }}
                        </p>
                    @endif
                </div>
                <span class="shrink-0 text-xs font-bold px-2.5 py-1 rounded-full {{ $employee->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                    {{ $employee->is_active ? 'نشط' : 'غير نشط' }}
                </span>
            </div>

            {{-- Financial stats --}}
            <div class="space-y-0 divide-y divide-gray-50">
                <div class="flex items-center justify-between py-2.5">
                    <span class="text-xs text-gray-400">الراتب الأساسي</span>
                    <span class="text-sm font-bold text-gray-600">
                        {{ ($employee->base_salary_currency ?? 'SYP') === 'USD' ? number_format((float)$employee->base_salary, 2) : number_format((float)$employee->base_salary) }}
                        <span class="text-xs font-normal {{ ($employee->base_salary_currency ?? 'SYP') === 'USD' ? 'text-emerald-500' : 'text-gray-400' }}">
                            {{ ($employee->base_salary_currency ?? 'SYP') === 'USD' ? '$' : 'ل.س' }}
                        </span>
                    </span>
                </div>
                <div class="flex items-center justify-between py-2.5">
                    <span class="text-xs text-gray-400">عدد العمليات</span>
                    <span class="text-sm font-bold text-gray-600">{{ $employee->transactions_count }}</span>
                </div>
                <div class="flex items-start justify-between py-2.5 gap-2">
                    <span class="text-xs text-gray-400 mt-0.5 shrink-0">الرصيد الصافي</span>
                    <div class="text-left">
                        @if($hasSYP || (!$hasSYP && !$hasUSD))
                        <p class="text-sm font-black {{ $netSYP >= 0 ? 'text-emerald-600' : 'text-red-600' }} leading-tight">
                            {{ $netSYP >= 0 ? '+' : '-' }}{{ number_format(abs($netSYP)) }} <span class="text-xs font-normal text-gray-400">ل.س</span>
                        </p>
                        @endif
                        @if($hasUSD)
                        <p class="text-sm font-black {{ $netUSD >= 0 ? 'text-emerald-600' : 'text-red-600' }} leading-tight">
                            {{ $netUSD >= 0 ? '+' : '-' }}{{ number_format(abs($netUSD), 2) }} <span class="text-xs font-normal text-gray-400">$</span>
                        </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="px-5 py-3 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
            <span class="text-xs text-gray-400 font-medium">عرض الملف الوظيفي</span>
            <svg class="w-4 h-4 text-gray-400 group-hover:text-slate-600 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        </div>
    </a>
    @endforeach
</div>
@endif

{{-- Modal: إضافة موظف --}}
<div id="modal-create" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,0.5)">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h2 class="text-base font-bold text-gray-800">إضافة موظف جديد</h2>
            <button onclick="document.getElementById('modal-create').classList.add('hidden')"
                    class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('employees.store') }}" class="p-6 space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-xs font-bold text-gray-500 mb-1">الاسم <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required placeholder="اسم الموظف"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-400 focus:outline-none bg-gray-50">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">المسمى الوظيفي</label>
                    <input type="text" name="job_title" placeholder="مثال: محاسب"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-400 focus:outline-none bg-gray-50">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">الهاتف</label>
                    <input type="text" name="phone" placeholder="رقم الهاتف"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-400 focus:outline-none bg-gray-50">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">الراتب الأساسي</label>
                    <input type="number" name="base_salary" min="0" placeholder="0"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-400 focus:outline-none bg-gray-50">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">العملة</label>
                    <div class="flex rounded-xl border border-gray-200 overflow-hidden text-sm font-bold">
                        <label class="flex-1 flex items-center justify-center gap-1.5 py-2.5 cursor-pointer has-[:checked]:bg-slate-700 has-[:checked]:text-white text-gray-500 hover:bg-gray-50 transition-colors">
                            <input type="radio" name="base_salary_currency" value="SYP" checked class="sr-only">
                            ل.س
                        </label>
                        <label class="flex-1 flex items-center justify-center gap-1.5 py-2.5 cursor-pointer has-[:checked]:bg-emerald-600 has-[:checked]:text-white text-gray-500 hover:bg-gray-50 transition-colors border-r border-gray-200">
                            <input type="radio" name="base_salary_currency" value="USD" class="sr-only">
                            $ دولار
                        </label>
                    </div>
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-bold text-gray-500 mb-1">ملاحظات</label>
                    <textarea name="notes" rows="2" placeholder="ملاحظات اختيارية..."
                              class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-400 focus:outline-none bg-gray-50 resize-none"></textarea>
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="flex-1 bg-slate-700 hover:bg-slate-800 text-white text-sm font-bold py-2.5 rounded-xl transition-colors">
                    إضافة الموظف
                </button>
                <button type="button" onclick="document.getElementById('modal-create').classList.add('hidden')"
                        class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-600 text-sm font-semibold hover:bg-gray-50 transition-colors">
                    إلغاء
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function filterCards(q) {
    q = (q || '').toLowerCase().trim();
    document.querySelectorAll('.emp-card').forEach(card => {
        card.style.display = !q || (card.dataset.name || '').includes(q) ? '' : 'none';
    });
}
document.getElementById('modal-create').addEventListener('click', function(e) {
    if (e.target === this) this.classList.add('hidden');
});
</script>

@endsection
