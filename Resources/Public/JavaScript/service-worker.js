self.addEventListener('push', function(event) {
  var payload = event.data.json();
  //  Keep the service worker alive until the notification is created.
  event.waitUntil(
    //  Show a notification with title ‘ServiceWorker Cookbook’ and body ‘Alea iacta est’.
    self.registration.showNotification(payload.title, {
      body: payload.body,
      icon: payload.icon,
      image: payload.image,
      badge: payload.badge,
      vibrate: payload.vibrate,
      dir: payload.dir,
      lang: payload.lang,

      tag: payload.tag,
      requireInteraction: payload.requireInteraction,
      renotify: payload.renotify,
      silent: payload.silent,

      data: {
        url: payload.url
      },
      actions: payload.actions
    })
  );
});

self.addEventListener('notificationclick', function(event) {
  var notification = event.notification;
  var url = notification.data && notification.data.url;
  var action = event.action;

  if (action !== 'close' && url) {
    clients.openWindow(url);
    notification.close();
  } else {
    notification.close();
  }
});
