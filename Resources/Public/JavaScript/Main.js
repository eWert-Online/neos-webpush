document.addEventListener('DOMContentLoaded', function() {
  // Let's check if the browser supports notifications
  if (!('Notification' in window) || !navigator.serviceWorker) {
    console.warn('This browser does not support desktop notifications :(');
    return;
  }

  if (Notification.permission === 'granted') {
    // Register and get the notification details and send them to the server.
    navigator.serviceWorker
      .register(window.EwertWebPush.swUrl)
      .then(function(registration) {
        return registration.pushManager.subscribe({
          userVisibleOnly: true,
          applicationServerKey: urlBase64ToUint8Array(
            'BMBlr6YznhYMX3NgcWIDRxZXs0sh7tCv7_YCsWcww0ZCv9WGg-tRCXfMEHTiBPCksSqeve1twlbmVAZFv7GSuj0'
          )
        });
      })
      .then(function(subscription) {
        var sub = subscription.toJSON();
        fetch('/webpush/subscribtion/new', {
          method: 'POST',
          headers: new Headers({
            'Content-Type': 'application/json'
          }),
          body: JSON.stringify({
            endpoint: sub.endpoint,
            expirationTime: sub.expirationTime,
            p256dh: sub.keys.p256dh,
            auth: sub.keys.auth
          })
        }).then(function(data) {
          console.log('returned from server:');
          console.log(data);
        });
      });
  } else if (Notification.permission === 'blocked') {
    console.warn('Permission was denied.');
    return;
  } else {
    Notification.requestPermission(function(status) {
      console.info('Notification Permission status:', status);
    });
  }
});

function urlBase64ToUint8Array(base64String) {
  var padding = '='.repeat((4 - (base64String.length % 4)) % 4);
  var base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');

  var rawData = window.atob(base64);
  var outputArray = new Uint8Array(rawData.length);

  for (var i = 0; i < rawData.length; ++i) {
    outputArray[i] = rawData.charCodeAt(i);
  }
  return outputArray;
}
