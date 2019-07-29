self.addEventListener('push', function(event) {
  //  Keep the service worker alive until the notification is created.
  event.waitUntil(
    //  Show a notification with title ‘ServiceWorker Cookbook’ and body ‘Alea iacta est’.
    self.registration.showNotification('ServiceWorker Cookbook', {
      body: 'Alea iacta est'
    })
  );
});

self.addEventListener('notificationclick', function(e) {
  var notification = e.notification;
  var url = notification.data && notification.data.url;
  var action = e.action;

  if (action !== 'close' && url) {
    clients.openWindow(url);
    notification.close();
  } else {
    notification.close();
  }
});
