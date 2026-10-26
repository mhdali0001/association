/*
 * Service Worker — مسالك النور
 * لا يخزّن صفحات النظام (فيها بيانات المستفيدين): الصفحات تُطلب من الشبكة دائماً.
 * يخزّن فقط صفحة "لا يوجد اتصال" وما تحتاجه (الشعار والخط) لتظهر عند انقطاع الإنترنت.
 * عند تعديل أي ملف في PRECACHE غيّر VERSION حتى يتحدث المخزن عند المستخدمين.
 */
const VERSION = 'v1';
const CACHE = 'masalik-' + VERSION;
const OFFLINE_URL = '/offline.html';
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
            .then(() => self.skipWaiting())
    );
});

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

    // صفحات النظام: من الشبكة، وعند انقطاع الاتصال تظهر صفحة "لا يوجد اتصال"
    if (request.mode === 'navigate') {
        event.respondWith(fetch(request).catch(() => caches.match(OFFLINE_URL)));
        return;
    }

    // ملفات صفحة "لا يوجد اتصال": من الشبكة، ومن المخزن عند انقطاعها
    if (PRECACHE.includes(url.pathname)) {
        event.respondWith(fetch(request).catch(() => caches.match(url.pathname)));
    }
});
