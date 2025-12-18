const CACHE_NAME = 'todoapp-v4';
const urlsToCache = [
  'img/icon.png',
  'manifest.json',
  'offline.php'
];

self.addEventListener('install', function (event) {
  self.skipWaiting();
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(function (cache) {
        return cache.addAll(urlsToCache);
      })
  );
});

self.addEventListener('activate', function (event) {
  event.waitUntil(self.clients.claim());
});

self.addEventListener('fetch', function (event) {
  event.respondWith(
    fetch(event.request)
      .then(function(response) {
        // Network First: If successful, clone and cache default pages
        if (!response || response.status !== 200 || response.type !== 'basic') {
          return response;
        }

        // Only cache navigation requests or same-origin assets to valid pages
        if (event.request.method === 'GET' && 
           (event.request.url.indexOf('index.php') > -1 || event.request.url.endsWith('todoapp/'))) {
            var responseToCache = response.clone();
            caches.open(CACHE_NAME)
              .then(function(cache) {
                cache.put(event.request, responseToCache);
              });
        }
        return response;
      })
      .catch(function () {
        // Network Failed (Offline): Try to serve from cache
        return caches.match(event.request)
          .then(function(response) {
             if (response) {
               return response;
             }
             // If not in cache and it's a navigation, show offline page
             if (event.request.mode === 'navigate') {
               return caches.match('offline.php');
             }
          });
      })
  );
});
