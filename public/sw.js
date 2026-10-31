/*
 * Service Worker — مسالك النور
 * لا يخزّن صفحات النظام (فيها بيانات المستفيدين): الصفحات تُطلب من الشبكة دائماً.
 * يخزّن فقط صفحة "لا يوجد اتصال" وما تحتاجه (الشعار والخط) لتظهر عند انقطاع الإنترنت،
 * وصفحة "العمل الميداني" — قالب بلا بيانات مستفيدين، وقائمة الزيارة تُنزَّل منه إلى الجهاز بطلب المستخدم.
 * عند تعديل أي ملف في PRECACHE غيّر VERSION حتى يتحدث المخزن عند المستخدمين.
 */
const VERSION = 'v3';
const CACHE = 'masalik-' + VERSION;
const OFFLINE_URL = '/offline.html';
const FIELD_WORK_URL = '/field-work';
const NETWORK_TIMEOUT = 4000;
const PRECACHE = [
    OFFLINE_URL,
    '/logo/logo-128.png',
    '/fonts/tajawal-400.woff2',
    '/fonts/tajawal-700.woff2',
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE)
            .then((cache) => cache.addAll(PRECACHE))
            .then(() => cacheFieldWork())
            .then(() => self.skipWaiting())
    );
});

// الصفحات تطلب حفظ صفحة العمل الميداني (partials/pwa): أول فتح بعد التحديث يمر بالنسخة القديمة
// من هذا الملف فلا تُحفظ الصفحة عبر التصفح وحده.
self.addEventListener('message', (event) => {
    if (event.data === 'cache-field-work') {
        event.waitUntil(caches.match(FIELD_WORK_URL).then((cached) => cached || cacheFieldWork()));
    } else if (event.data === 'refresh-field-work') {
        event.waitUntil(cacheFieldWork());
    }
});

// تُحفظ فقط لمستخدم مسجّل الدخول: التحويل إلى صفحة الدخول لا يُحفظ مكانها
function cacheFieldWork() {
    return fetch(FIELD_WORK_URL, { credentials: 'same-origin', cache: 'no-store' })
        .then((response) => {
            if (response.ok && !response.redirected && response.type === 'basic') {
                return caches.open(CACHE).then((cache) => cache.put(FIELD_WORK_URL, response));
            }
        })
        .catch(() => {});
}

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((keys) => Promise.all(
                keys.filter((key) => key.startsWith('masalik-') && key !== CACHE).map((key) => caches.delete(key))
            ))
            .then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (event) => {
    const request = event.request;
    if (request.method !== 'GET') return;

    const url = new URL(request.url);
    if (url.origin !== self.location.origin) return;

    if (request.mode === 'navigate') {
        // العمل الميداني: يفتح بدون إنترنت أو بشبكة ضعيفة
        if (url.pathname === FIELD_WORK_URL) {
            event.respondWith(fieldWorkPage(request));
            return;
        }
        // صفحات النظام: من الشبكة، وعند انقطاع الاتصال تظهر صفحة "لا يوجد اتصال"
        event.respondWith(fetch(request).catch(() => offlineFallback(url)));
        return;
    }

    // ملفات صفحة "لا يوجد اتصال": من الشبكة، ومن المخزن عند انقطاعها
    if (PRECACHE.includes(url.pathname)) {
        event.respondWith(fetch(request).catch(() => caches.match(url.pathname)));
    }
});

// فتح التطبيق بدون إنترنت (يبدأ من /dashboard): إلى العمل الميداني مباشرة إن كانت محفوظة
function offlineFallback(url) {
    if (url.pathname !== '/dashboard' && url.pathname !== '/') return caches.match(OFFLINE_URL);
    return caches.match(FIELD_WORK_URL).then((page) => page
        ? Response.redirect(new URL(FIELD_WORK_URL, self.location.origin).href, 302)
        : caches.match(OFFLINE_URL));
}

// من الشبكة إن ردّت خلال ثوانٍ (وتُحدَّث النسخة المحفوظة)، وإلا النسخة المحفوظة.
// تحويل إلى صفحة الدخول (جلسة منتهية) أو خطأ لا يُحفظ مكان الصفحة.
function fieldWorkPage(request) {
    const network = fetch(request).then((response) => {
        if (response.ok && !response.redirected && response.type === 'basic') {
            const copy = response.clone();
            caches.open(CACHE).then((cache) => cache.put(FIELD_WORK_URL, copy));
        }
        // خطأ في السيرفر (مثل مهلة أول طلب): النسخة المحفوظة أنفع من صفحة الخطأ
        if (response.status >= 500) {
            return caches.match(FIELD_WORK_URL).then((cached) => cached || response);
        }
        return response;
    });
    network.catch(() => {});
    const timeout = new Promise((resolve) => setTimeout(resolve, NETWORK_TIMEOUT))
        .then(() => caches.match(FIELD_WORK_URL));

    return Promise.race([network, timeout])
        .then((response) => response || network)
        .catch(() => caches.match(FIELD_WORK_URL))
        .then((response) => response || caches.match(OFFLINE_URL));
}
