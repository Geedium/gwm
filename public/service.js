const cacheName = 'v1';

const cachedAssets = [

];

self.addEventListener('install', (e) => {
    console.log('Service Worker: installed!')

    e.waitUntil(
        caches
            .open(cacheName)
            .then(cache => {
                console.log('Service worker: caching files...');
                cache.addAll(cachedAssets);
            })
            .then( () => self.skipWaiting() )
    );
})

self.addEventListener('activate', (e) => {
    console.log('Service Worker: activated')
})