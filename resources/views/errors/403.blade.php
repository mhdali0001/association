<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 — غير مصرح | مسالك النور</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *, *::before, *::after { font-family: 'Tajawal', sans-serif; box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f1f5f9;
        }
    </style>
</head>
<body>
    <div class="text-center px-6">

        {{-- Icon --}}
        <div class="flex justify-center mb-8">
            <div class="relative">
                <div class="w-32 h-32 rounded-full bg-red-50 border-2 border-red-100 flex items-center justify-center">
                    <svg class="w-16 h-16 text-red-400" fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <div class="absolute -top-1 -left-1 w-8 h-8 rounded-full bg-red-500 flex items-center justify-content-center text-white font-black text-sm shadow-md"
                     style="display:flex;align-items:center;justify-content:center;">
                    !
                </div>
            </div>
        </div>

        {{-- Code --}}
        <p class="text-8xl font-black text-red-200 leading-none mb-2 select-none">403</p>

        {{-- Title --}}
        <h1 class="text-2xl font-black text-gray-800 mb-3">غير مصرح لك بالوصول</h1>

        {{-- Description --}}
        <p class="text-gray-500 text-sm leading-relaxed max-w-sm mx-auto mb-8">
            هذه الصفحة مخصصة لمدير النظام فقط.
            <br>إذا كنت تعتقد أن هذا خطأ، تواصل مع المسؤول.
        </p>

        {{-- Actions --}}
        <div class="flex items-center justify-center gap-3 flex-wrap">
            <a href="{{ route('dashboard') }}"
               class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-6 py-2.5 rounded-xl transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                الذهاب للوحة التحكم
            </a>
            <a href="javascript:history.back()"
               class="inline-flex items-center gap-2 bg-white hover:bg-gray-50 text-gray-600 text-sm font-semibold px-6 py-2.5 rounded-xl border border-gray-200 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                رجوع
            </a>
        </div>

        {{-- Footer --}}
        <p class="mt-12 text-xs text-gray-300">
            &copy; {{ date('Y') }} جمعية مسالك النور
        </p>

    </div>
</body>
</html>
