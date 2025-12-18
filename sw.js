const CACHE_NAME = 'todoapp-v1';
const urlsToCache = [
  'img/icon.png',
  'manifest.json',
  'layout.php' // Cache structure if needed, but mainly assets
];

self.addEventListener('install', function (event) {
  self.skipWaiting(); // Force waiting SW to become active
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(function (cache) {
        return cache.addAll(urlsToCache);
      })
  );
});

self.addEventListener('activate', function (event) {
  event.waitUntil(self.clients.claim()); // Force active SW to take control of all clients
});

self.addEventListener('fetch', function (event) {
  // Strategy: Network First, falling back to cache
  // This is crucial for dynamic content like index.php
  event.respondWith(
    fetch(event.request)
      .catch(function () {
        return caches.match(event.request);
      })
  );
});
