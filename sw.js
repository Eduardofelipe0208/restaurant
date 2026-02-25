const CACHE_NAME = 'gochaburguer-v1';
const ASSETS = [
    './',
    './index.php',
    './assets/css/style.css',
    './assets/js/app.js',
    './assets/js/cart.js',
    './assets/js/pwa.js',
    './assets/uploads/placeholder.jpg',
    './assets/uploads/icon-192.png',
    './assets/uploads/icon-512.png'
];

self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => cache.addAll(ASSETS))
    );
});

// Stale-While-Revalidate Strategy
self.addEventListener('fetch', event => {
    // Dynamic API data should be network-first
    if (event.request.url.includes('/api/')) {
        event.respondWith(
            fetch(event.request).catch(() => caches.match(event.request))
        );
        return;
    }

    event.respondWith(
        caches.match(event.request).then(response => {
            const fetchPromise = fetch(event.request).then(networkResponse => {
                const responseClone = networkResponse.clone();
                caches.open(CACHE_NAME).then(cache => {
                    cache.put(event.request, responseClone);
                });
                return networkResponse;
            });
            // Return cached version if found, while updating cache behind the scenes
            return response || fetchPromise;
        })
    );
});
