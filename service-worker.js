

const CACHE_NAME   = 'anime-waifu-vault-v42';
const STATIC_CACHE = 'static-v42';
const CDN_CACHE    = 'cdn-v42';

// ── ASET KRITIKAL LOKAL ─────────────────────────────────────────────────────
const CRITICAL_ASSETS = [
    './',
    './index.php?cachebust=17',
    './search_api.js?v=20260328',
    './auth.js?v=20260328',
    './vendor/tailwindcss-cdn.js',
    './manifest.php',
    './icons/icon.png',
    './icons/icon-192.png',
    './icons/icon-512.png',
];

// ── HOST CDN AMAN (Font & CSS — tidak mengandung JS eksekutable) ───────────
const CDN_CACHE_FIRST_HOSTS = [
    'fonts.googleapis.com',
    'fonts.gstatic.com',
    'cdnjs.cloudflare.com',   // hanya CropperJS CSS/JS — tidak ada CORS issue
];

// ============ INSTALL ============
self.addEventListener('install', event => {
    self.skipWaiting();
    event.waitUntil(
        caches.open(STATIC_CACHE).then(async cache => {
            // Promise.allSettled: satu aset gagal tidak batalkan seluruh install
            const results = await Promise.allSettled(
                CRITICAL_ASSETS.map(url =>
                    fetch(url, { cache: 'no-cache' })
                        .then(res => { if (res.ok) return cache.put(url, res); })
                        .catch(err => console.warn('[SW] Skip (offline?):', url, err))
                )
            );
            const ok = results.filter(r => r.status === 'fulfilled').length;
            console.log(`[SW] v41 Install: ${ok}/${CRITICAL_ASSETS.length} aset di-cache.`);
        })
    );
});

// ============ ACTIVATE ============
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(names =>
            Promise.all(
                names
                    .filter(n => n !== CACHE_NAME && n !== STATIC_CACHE && n !== CDN_CACHE)
                    .map(n => { console.log('[SW] Hapus cache lama:', n); return caches.delete(n); })
            )
        ).then(() => self.clients.claim())
    );
});

// ============ FETCH ROUTING ============
self.addEventListener('fetch', event => {
    const url = new URL(event.request.url);
    if (event.request.method !== 'GET') return;

    // ── RULE 0: cdn.jsdelivr.net → JANGAN INTERCEPT SAMA SEKALI ─────────────
    // Ini adalah fix definitif untuk bug DAD.
    // SW tidak menyentuh SortableJS (atau library JS lain dari jsdelivr).
    // Browser mengelola cache-nya sendiri via HTTP Cache-Control header,
    // yang benar dan tidak menyebabkan opaque/CORS response bug.
    if (url.hostname === 'cdn.jsdelivr.net') {
        return; // Lewati — browser handle sendiri
    }

    // ── RULE 1: Font & CSS CDN → Cache-First ─────────────────────────────────
    if (CDN_CACHE_FIRST_HOSTS.some(h => url.hostname.includes(h))) {
        event.respondWith(cacheFirst(event.request, CDN_CACHE));
        return;
    }

    // ── RULE 2: Gambar & upload → Cache-First ────────────────────────────────
    if (
        event.request.destination === 'image' ||
        url.pathname.includes('/uploads/') ||
        url.hostname.includes('dicebear.com')
    ) {
        event.respondWith(cacheFirstImage(event.request));
        return;
    }

    // ── RULE 3: PHP, JS lokal, API → Network-First ───────────────────────────
    event.respondWith(networkFirst(event.request));
});

// ============ IMPLEMENTASI STRATEGI ============

async function cacheFirst(request, cacheName = STATIC_CACHE) {
    const cached = await caches.match(request);
    if (cached) return cached;
    try {
        const res = await fetch(request);
        if (res.ok) {
            const c = await caches.open(cacheName);
            c.put(request, res.clone());
        }
        return res;
    } catch {
        return new Response('', { status: 408, statusText: 'Offline' });
    }
}

async function cacheFirstImage(request) {
    const cached = await caches.match(request);
    if (cached) return cached;
    try {
        const res = await fetch(request);
        // Gambar boleh simpan opaque (cross-origin tanpa CORS header)
        if (res.ok || res.type === 'opaque') {
            const c = await caches.open(STATIC_CACHE);
            c.put(request, res.clone());
        }
        return res;
    } catch {
        return new Response('', { status: 408, statusText: 'Offline' });
    }
}

async function networkFirst(request) {
    try {
        const res = await fetch(request);
        if (res.ok) {
            const c = await caches.open(CACHE_NAME);
            c.put(request, res.clone());
        }
        return res;
    } catch {
        const cached = await caches.match(request);
        if (cached) return cached;
        if (request.mode === 'navigate') return offlinePage();
        return new Response('', { status: 408, statusText: 'Offline' });
    }
}

function offlinePage() {
    return new Response(
        `<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Offline</title><style>body{background:#0f0a1e;color:#e2d9f3;text-align:center;padding-top:50px;font-family:sans-serif}button{margin-top:20px;padding:12px 24px;background:#7c3aed;color:#fff;border:none;border-radius:8px;font-weight:bold;cursor:pointer}</style></head><body><h1 style="font-size:3rem">🌸</h1><h2>Sedang Offline</h2><p>Silakan hubungkan ke internet, senpai.</p><button onclick="location.reload()">Coba Lagi</button></body></html>`,
        { headers: { 'Content-Type': 'text/html; charset=utf-8' } }
    );
}