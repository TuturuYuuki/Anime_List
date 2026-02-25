// service-worker.js - PWA Service Worker untuk Anime & Waifu Vault

const CACHE_NAME = 'anime-waifu-vault-v1';
const STATIC_CACHE = 'static-v1';

// Aset yang di-cache saat install
const STATIC_ASSETS = [
    '/',
    '/index.php',
    '/search_api.js',
    '/manifest.json',
    'https://cdn.tailwindcss.com',
    'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Nunito:wght@400;600;700;800&display=swap'
];

// ============ INSTALL ============
self.addEventListener('install', event => {
    console.log('[SW] Installing...');
    event.waitUntil(
        caches.open(STATIC_CACHE)
            .then(cache => {
                return cache.addAll(STATIC_ASSETS.map(url => {
                    return new Request(url, { mode: 'cors' });
                })).catch(err => {
                    console.warn('[SW] Some assets failed to cache:', err);
                });
            })
            .then(() => self.skipWaiting())
    );
});

// ============ ACTIVATE ============
self.addEventListener('activate', event => {
    console.log('[SW] Activating...');
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames
                    .filter(name => name !== CACHE_NAME && name !== STATIC_CACHE)
                    .map(name => caches.delete(name))
            );
        }).then(() => self.clients.claim())
    );
});

// ============ FETCH STRATEGY ============
self.addEventListener('fetch', event => {
    const { request } = event;
    const url = new URL(request.url);

    // Skip non-GET requests (POST untuk form submit, dsb)
    if (request.method !== 'GET') return;

    // Skip API calls ke Jikan dan request API lokal - selalu online
    if (
        url.hostname === 'api.jikan.moe' ||
        url.pathname.includes('api.php') ||
        url.pathname.includes('api.dicebear.com')
    ) {
        event.respondWith(
            fetch(request).catch(() => {
                return new Response(JSON.stringify({ error: 'Offline' }), {
                    headers: { 'Content-Type': 'application/json' }
                });
            })
        );
        return;
    }

    // Untuk gambar uploads: Cache first
    if (url.pathname.includes('/uploads/')) {
        event.respondWith(cacheFirst(request));
        return;
    }

    // Untuk halaman utama: Network first, fallback ke cache
    if (url.pathname.endsWith('.php') || url.pathname === '/') {
        event.respondWith(networkFirst(request));
        return;
    }

    // Untuk aset statis (JS, CSS, font): Cache first
    event.respondWith(cacheFirst(request));
});

// ============ STRATEGIES ============

/**
 * Network First - Coba network dulu, fallback ke cache
 */
async function networkFirst(request) {
    try {
        const networkResponse = await fetch(request);
        if (networkResponse.ok) {
            const cache = await caches.open(CACHE_NAME);
            cache.put(request, networkResponse.clone());
        }
        return networkResponse;
    } catch (err) {
        const cached = await caches.match(request);
        if (cached) return cached;
        return offlinePage();
    }
}

/**
 * Cache First - Cek cache dulu, fallback ke network
 */
async function cacheFirst(request) {
    const cached = await caches.match(request);
    if (cached) return cached;

    try {
        const networkResponse = await fetch(request);
        if (networkResponse.ok) {
            const cache = await caches.open(CACHE_NAME);
            cache.put(request, networkResponse.clone());
        }
        return networkResponse;
    } catch (err) {
        return new Response('', { status: 408, statusText: 'Offline' });
    }
}

/**
 * Halaman offline fallback
 */
function offlinePage() {
    return new Response(`
        <!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Offline - Anime & Waifu Vault</title>
            <style>
                body {
                    font-family: 'Inter', sans-serif;
                    background: #0f0a1e;
                    color: #e2d9f3;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    min-height: 100vh;
                    margin: 0;
                    text-align: center;
                    padding: 20px;
                }
                .card {
                    background: rgba(255,255,255,0.06);
                    border: 1px solid rgba(167,139,250,0.2);
                    border-radius: 20px;
                    padding: 40px 32px;
                    max-width: 360px;
                }
                h1 { color: #c4b5fd; margin-bottom: 8px; font-size: 1.5rem; }
                p { color: rgba(255,255,255,0.5); font-size: 0.9rem; line-height: 1.6; }
                button {
                    margin-top: 20px;
                    background: linear-gradient(135deg, #7c3aed, #6d28d9);
                    color: white;
                    border: none;
                    padding: 12px 24px;
                    border-radius: 10px;
                    font-size: 0.9rem;
                    cursor: pointer;
                    font-weight: 600;
                }
            </style>
        </head>
        <body>
            <div class="card">
                <div style="font-size: 3rem; margin-bottom: 16px;">🌸</div>
                <h1>Sedang Offline</h1>
                <p>Kamu sedang offline, senpai. Hubungkan ke internet untuk mengakses Anime & Waifu Vault.</p>
                <button onclick="location.reload()">Coba Lagi</button>
            </div>
        </body>
        </html>
    `, {
        headers: { 'Content-Type': 'text/html; charset=utf-8' },
        status: 200
    });
}

// ============ BACKGROUND SYNC (Future Use) ============
self.addEventListener('sync', event => {
    if (event.tag === 'sync-animes') {
        console.log('[SW] Background sync: animes');
    }
});

// ============ PUSH NOTIFICATION (Future Use) ============
self.addEventListener('push', event => {
    const data = event.data ? event.data.json() : {};
    const options = {
        body: data.body || 'Ada update baru di Anime Vault!',
        icon: '/icons/icon-192.png',
        badge: '/icons/icon-72.png',
        vibrate: [100, 50, 100],
        data: { url: data.url || '/' }
    };
    event.waitUntil(
        self.registration.showNotification(data.title || 'Anime & Waifu Vault 🌸', options)
    );
});

self.addEventListener('notificationclick', event => {
    event.notification.close();
    event.waitUntil(
        clients.openWindow(event.notification.data.url || '/')
    );
});
