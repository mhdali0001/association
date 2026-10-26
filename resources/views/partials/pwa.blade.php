{{-- PWA: تثبيت النظام كتطبيق على الموبايل والكمبيوتر — أي عنصر يحمل data-pwa-install يصبح زر تثبيت --}}
<link rel="manifest" href="/manifest.webmanifest">
<meta name="theme-color" content="#166534">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-title" content="مسالك النور">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<script>
(function () {
    var deferredPrompt = null;
    var isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
    var isIos = /iphone|ipad|ipod/i.test(navigator.userAgent) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);

    function installButtons() { return document.querySelectorAll('[data-pwa-install]'); }
    function showButtons(show) { installButtons().forEach(function (b) { b.hidden = !show; }); }

    // أندرويد وكروم وإيدج: المتصفح يعلن أن التطبيق قابل للتثبيت
    window.addEventListener('beforeinstallprompt', function (e) {
        if (!installButtons().length) return; // صفحة بلا زر: نترك المتصفح يعرض اقتراحه المعتاد
        e.preventDefault();
        deferredPrompt = e;
        showButtons(true);
    });
    window.addEventListener('appinstalled', function () { deferredPrompt = null; showButtons(false); });

    document.addEventListener('DOMContentLoaded', function () {
        // آيفون وآيباد: لا يوجد طلب تثبيت تلقائي، فالزر يشرح الطريقة
        if (isIos && !isStandalone) showButtons(true);

        installButtons().forEach(function (button) {
            button.addEventListener('click', function () {
                if (deferredPrompt) {
                    deferredPrompt.prompt();
                    deferredPrompt.userChoice.finally(function () { deferredPrompt = null; showButtons(false); });
                } else if (isIos) {
                    alert('لتثبيت التطبيق: اضغط زر المشاركة في المتصفح، ثم اختر «إضافة إلى الشاشة الرئيسية».');
                }
            });
        });
    });

    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            navigator.serviceWorker.register('/sw.js').catch(function () {});
        });
    }
})();
</script>
