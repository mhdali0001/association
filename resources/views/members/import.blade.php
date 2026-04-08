@extends('layouts.app')

@section('title', 'استيراد الأعضاء — مسالك النور')

@section('breadcrumb')
    <a href="{{ route('members.index') }}" class="hover:text-emerald-700 transition-colors">الأعضاء</a>
    <span class="text-gray-300 mx-1">/</span>
    <span class="text-gray-700">استيراد من Excel</span>
@endsection

@section('content')

<div class="max-w-3xl mx-auto">

    {{-- Header --}}
    <div class="flex items-start justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                <span class="w-9 h-9 rounded-xl bg-green-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-green-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </span>
                استيراد الأعضاء من Excel
            </h1>
            <p class="text-sm text-gray-400 mt-1 mr-12">رفع ملف Excel أو CSV لاستيراد بيانات الأعضاء دفعةً واحدة</p>
        </div>
        <a href="{{ route('members.index') }}"
           class="text-sm text-gray-500 hover:text-gray-700 border border-gray-200 px-4 py-2 rounded-lg transition-colors shrink-0">
            ← رجوع
        </a>
    </div>

    {{-- Result: Success / Errors --}}
    @if(session('import_imported') !== null)
    <div class="space-y-4 mb-6">

        {{-- Summary bar --}}
        <div class="grid grid-cols-3 gap-3">
            <div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-4 text-center">
                <p class="text-2xl font-black text-emerald-700">{{ count(session('import_imported')) }}</p>
                <p class="text-xs text-emerald-600 mt-1">تم الاستيراد</p>
            </div>
            <div class="bg-yellow-50 border border-yellow-100 rounded-2xl p-4 text-center">
                <p class="text-2xl font-black text-yellow-700">{{ count(session('import_skipped')) }}</p>
                <p class="text-xs text-yellow-600 mt-1">تم التخطي</p>
            </div>
            <div class="bg-red-50 border border-red-100 rounded-2xl p-4 text-center">
                <p class="text-2xl font-black text-red-700">{{ count(session('import_errors')) }}</p>
                <p class="text-xs text-red-600 mt-1">أخطاء</p>
            </div>
        </div>

        {{-- Imported names --}}
        @if(count(session('import_imported')))
        <div class="bg-white border border-emerald-100 rounded-2xl overflow-hidden">
            <div class="bg-emerald-50 border-b border-emerald-100 px-5 py-3 flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                <span class="text-sm font-semibold text-emerald-700">الأعضاء الذين تم استيرادهم</span>
            </div>
            <div class="p-4 flex flex-wrap gap-2">
                @foreach(session('import_imported') as $name)
                    <span class="text-xs bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-lg px-2.5 py-1">{{ $name }}</span>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Skipped --}}
        @if(count(session('import_skipped')))
        <div class="bg-white border border-yellow-100 rounded-2xl overflow-hidden">
            <div class="bg-yellow-50 border-b border-yellow-100 px-5 py-3 flex items-center gap-2">
                <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
                <span class="text-sm font-semibold text-yellow-700">صفوف تم تخطيها (هوية مكررة)</span>
            </div>
            <ul class="divide-y divide-yellow-50">
                @foreach(session('import_skipped') as $msg)
                    <li class="px-5 py-2.5 text-xs text-yellow-800">{{ $msg }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Errors --}}
        @if(count(session('import_errors')))
        <div class="bg-white border border-red-100 rounded-2xl overflow-hidden">
            <div class="bg-red-50 border-b border-red-100 px-5 py-3 flex items-center gap-2">
                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                <span class="text-sm font-semibold text-red-700">أخطاء</span>
            </div>
            <ul class="divide-y divide-red-50">
                @foreach(session('import_errors') as $err)
                    <li class="px-5 py-2.5 text-xs text-red-800">{{ $err }}</li>
                @endforeach
            </ul>
        </div>
        @endif

    </div>
    @endif

    {{-- Validation errors --}}
    @if($errors->any())
    <div class="bg-red-50 border border-red-100 rounded-2xl p-4 mb-6 flex gap-3">
        <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
        </svg>
        <p class="text-sm text-red-700 font-medium">{{ $errors->first() }}</p>
    </div>
    @endif

    {{-- Upload form --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
        <div class="bg-gray-50 border-b border-gray-100 px-6 py-3 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-700">رفع الملف</h2>
            <a href="{{ route('members.import.template') }}"
               class="flex items-center gap-1.5 text-xs text-emerald-700 hover:text-emerald-900 font-medium bg-emerald-50 border border-emerald-200 px-3 py-1.5 rounded-lg transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                تحميل نموذج CSV
            </a>
        </div>
        <div class="p-6">
            <form method="POST" action="{{ route('members.import.store') }}" enctype="multipart/form-data">
                @csrf

                {{-- Drop zone --}}
                <label for="file-input"
                       class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-200 rounded-2xl cursor-pointer bg-gray-50 hover:bg-emerald-50 hover:border-emerald-300 transition-all group"
                       id="drop-zone">
                    <svg class="w-10 h-10 text-gray-300 group-hover:text-emerald-400 transition-colors mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-sm font-medium text-gray-500 group-hover:text-emerald-700 transition-colors" id="drop-label">
                        اسحب الملف هنا أو انقر للاختيار
                    </p>
                    <p class="text-xs text-gray-400 mt-1">xlsx, xls, csv — حجم أقصى 5 ميغابايت</p>
                    <input id="file-input" name="file" type="file" accept=".xlsx,.xls,.csv" class="hidden">
                </label>

                <div class="mt-4 flex justify-end">
                    <button type="submit"
                            class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm px-7 py-3 rounded-xl shadow-sm hover:shadow-md transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        بدء الاستيراد
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Column guide --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="bg-gray-50 border-b border-gray-100 px-6 py-3">
            <h2 class="text-sm font-semibold text-gray-700">دليل الأعمدة المدعومة</h2>
        </div>
        <div class="p-5">
            <p class="text-xs text-gray-500 mb-4">يجب أن يكون الصف الأول في الملف هو صف العناوين. الأعمدة المطلوبة باللون الأخضر.</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                @php
                $columns = [
                    ['ar' => 'الاسم_الكامل',         'en' => 'full_name',         'required' => true,  'note' => 'الاسم الكامل للمستفيد'],
                    ['ar' => 'رقم_الهوية',            'en' => 'national_id',        'required' => false, 'note' => 'رقم الهوية الوطنية'],
                    ['ar' => 'رقم_الهاتف',            'en' => 'phone',              'required' => false, 'note' => 'رقم الهاتف'],
                    ['ar' => 'رقم_الملف',             'en' => 'dossier_number',     'required' => false, 'note' => 'رقم الإضبارة / الملف'],
                    ['ar' => 'العمر',                  'en' => 'age',                'required' => false, 'note' => 'العمر بالسنوات'],
                    ['ar' => 'الجنس',                  'en' => 'gender',             'required' => false, 'note' => 'ذكر أو أنثى'],
                    ['ar' => 'الحالة_الاجتماعية',     'en' => 'marital_status',     'required' => false, 'note' => 'أعزب، متزوج، مطلق…'],
                    ['ar' => 'نوع_المرض',              'en' => 'disease_type',       'required' => false, 'note' => 'نوع المرض أو الحالة الصحية'],
                    ['ar' => 'العنوان',                'en' => 'current_address',    'required' => false, 'note' => 'العنوان الحالي'],
                    ['ar' => 'اسم_الأم',               'en' => 'mother_name',        'required' => false, 'note' => 'اسم الأم'],
                    ['ar' => 'الوظيفة',                'en' => 'job',                'required' => false, 'note' => 'الوظيفة أو مصدر الدخل'],
                    ['ar' => 'وضع_السكن',              'en' => 'housing_status',     'required' => false, 'note' => 'مالك، مستأجر…'],
                    ['ar' => 'عدد_المعالين',           'en' => 'dependents_count',          'required' => false, 'note' => 'عدد أفراد الأسرة'],
                    ['ar' => 'المدخل',                 'en' => 'representative',            'required' => false, 'note' => 'اسم المدخل كما هو مسجل في النظام'],
                    ['ar' => 'مندوب',                  'en' => 'delegate',                  'required' => false, 'note' => 'اسم المندوب (نص حر)'],
                    ['ar' => 'حالة_التحقق',            'en' => 'verification_status',       'required' => false, 'note' => 'اسم حالة التحقق كما هو مسجل في النظام'],
                    ['ar' => 'الشبكة',                 'en' => 'network',                   'required' => false, 'note' => 'MTN أو SYRIATEL فقط'],
                    ['ar' => 'الجمعية',                'en' => 'association',               'required' => false, 'note' => 'اسم الجمعية كما هو مسجل في النظام'],
                    ['ar' => 'منتسب_لجمعية_أخرى',     'en' => 'other_association',         'required' => false, 'note' => 'نعم / لا'],
                    ['ar' => 'وصف_الحالات_الخاصة',    'en' => 'special_cases_description', 'required' => false, 'note' => 'وصف الحالة الخاصة إن وجدت'],
                    ['ar' => 'حساب_شام_كاش',           'en' => 'sham_cash_account',         'required' => false, 'note' => 'نعم / لا'],
                    ['ar' => 'الآيبان',                    'en' => 'iban',                'required' => false, 'note' => 'رقم الحساب البنكي (IBAN)'],
                    ['ar' => 'الباركود',                   'en' => 'barcode',             'required' => false, 'note' => 'رقم الباركود أو رقم المحفظة'],
                    ['ar' => 'درجة_العمل',                 'en' => 'work_score',          'required' => false, 'note' => 'درجة الوضع الوظيفي (رقم)'],
                    ['ar' => 'درجة_السكن',                 'en' => 'housing_score',       'required' => false, 'note' => 'درجة وضع السكن (رقم)'],
                    ['ar' => 'درجة_المعالين',              'en' => 'dependents_score',       'required' => false, 'note' => 'درجة عدد المعالين (رقم)'],
                    ['ar' => 'درجة_حالة_المعيل',          'en' => 'dependent_status_score', 'required' => false, 'note' => 'درجة حالة المعيل (أقصى 2)'],
                    ['ar' => 'درجة_المرض',                 'en' => 'illness_score',          'required' => false, 'note' => 'درجة الحالة المرضية (رقم)'],
                    ['ar' => 'درجة_الحالات_الخاصة',       'en' => 'special_cases_score', 'required' => false, 'note' => 'درجة الحالات الخاصة (رقم)'],
                ];
                @endphp
                @foreach($columns as $col)
                <div class="flex items-start gap-3 p-3 rounded-xl {{ $col['required'] ? 'bg-emerald-50 border border-emerald-100' : 'bg-gray-50 border border-gray-100' }}">
                    <div class="shrink-0 mt-0.5">
                        @if($col['required'])
                            <span class="w-4 h-4 rounded-full bg-emerald-500 flex items-center justify-center">
                                <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                            </span>
                        @else
                            <span class="w-4 h-4 rounded-full bg-gray-300 flex items-center justify-center">
                                <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                            </span>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <code class="text-xs font-mono font-bold {{ $col['required'] ? 'text-emerald-800' : 'text-gray-700' }}">{{ $col['ar'] }}</code>
                            <span class="text-gray-300 text-xs">|</span>
                            <code class="text-xs font-mono text-gray-400">{{ $col['en'] }}</code>
                            @if($col['required'])
                                <span class="text-xs bg-emerald-100 text-emerald-700 rounded px-1.5 py-0.5 font-semibold">مطلوب</span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $col['note'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-4 bg-blue-50 border border-blue-100 rounded-xl p-4 text-xs text-blue-700 leading-relaxed">
                <p class="font-semibold mb-1">ملاحظات مهمة:</p>
                <ul class="list-disc list-inside space-y-1 text-blue-600">
                    <li>يمكن استخدام الأعمدة باللغة العربية أو الإنجليزية.</li>
                    <li>يُسمح بتكرار رقم الهوية — سيتم إنشاء سجل جديد في كلتا الحالتين.</li>
                    <li>سيتم تعيين حالة التحقق الافتراضية تلقائياً لكل عضو مستورد.</li>
                    <li>يمكن تعديل بيانات كل عضو بعد الاستيراد.</li>
                </ul>
            </div>
        </div>
    </div>

</div>

<script>
    const input     = document.getElementById('file-input');
    const dropLabel = document.getElementById('drop-label');
    const dropZone  = document.getElementById('drop-zone');

    input.addEventListener('change', () => {
        dropLabel.textContent = input.files[0]?.name ?? 'اسحب الملف هنا أو انقر للاختيار';
    });

    ['dragover','dragenter'].forEach(e => {
        dropZone.addEventListener(e, ev => { ev.preventDefault(); dropZone.classList.add('border-emerald-400','bg-emerald-50'); });
    });
    ['dragleave','drop'].forEach(e => {
        dropZone.addEventListener(e, ev => {
            ev.preventDefault();
            dropZone.classList.remove('border-emerald-400','bg-emerald-50');
            if (e === 'drop' && ev.dataTransfer.files.length) {
                input.files = ev.dataTransfer.files;
                dropLabel.textContent = ev.dataTransfer.files[0].name;
            }
        });
    });
</script>

@endsection
