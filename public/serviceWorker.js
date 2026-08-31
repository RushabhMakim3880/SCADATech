// service-worker.js
self.addEventListener('install', (event) => {
    self.skipWaiting(); // force immediate activation
    event.waitUntil(
        caches.open('app-cache').then(function (cache) {
            console.log('Service Worker installed.');
            return cache.addAll([
                '/offline.html'
            ]);
        })
    );
});

self.addEventListener('activate', (event) => {
    console.log('Service Worker activated.');
    clients.claim(); // take control of pages
});

self.addEventListener('push', (event) => {
    const data = event.data ? event.data.json() : {};
    const title = data.title || 'Notification';
    const options = {
        body: data.body || 'You have a new message!',
        icon: data.icon || '',
        badge: data.badge || '',
        image: data.image || '',
        data: {
            url: data.url || '/', // Include the URL in the data object
        },
    };

    event.waitUntil(self.registration.showNotification(title, options));
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close(); // Close the notification

    // Safely access the URL in notification data
    const urlToOpen = event.notification.data && event.notification.data.url
        ? event.notification.data.url
        : '/'; // Default URL if none is provided

    event.waitUntil(
        clients.openWindow(urlToOpen)
    );
});

self.addEventListener('fetch', function (event) {
    const req = event.request;
    const accept = req.headers.get('accept') || '';
    const url = new URL(req.url);

    // Handle API calls expecting JSON
    if (accept.includes('application/json')) {
        event.respondWith(
            fetch(req).catch(() =>
                new Response(JSON.stringify({
                    status: false,
                    message: 'You are offline. Please check your internet connection.',
                }), {
                    headers: { 'Content-Type': 'application/json' },
                    status: 503
                })
            )
        );
        return;
    }

    // Handle full-page HTML requests only
    if (
        accept.includes('text/html') &&
        req.method === 'GET' &&
        !url.pathname.match(/\.(css|js|png|jpg|jpeg|gif|svg|webp|ico|woff2?|ttf|eot|otf)$/i)
    ) {
        event.respondWith(
            fetch(req).catch(() => caches.match('/offline.html'))
        );
    }
});
