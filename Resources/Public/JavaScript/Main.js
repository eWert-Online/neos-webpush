/**
 * @prettier
 */

function __webPushRequestPermission() {
  if (!('serviceWorker' in navigator)) {
    return;
  }

  if (!('PushManager' in window)) {
    return;
  }

  new Promise((resolve, reject) => {
    const permissionPromise = Notification.requestPermission(result => {
      resolve(result);
    });
    if (permissionPromise) {
      permissionPromise.then(resolve);
    }
  }).then(result => {
    if (result === 'granted') {
      registerServiceWorker().then(registration => {
        subscribeUserToPush(registration).then(pushSubscription => {
          sendSubscriptionToServer(pushSubscription);
        });
      });
    }
  });

  function registerServiceWorker() {
    return new Promise((resolve, reject) => {
      navigator.serviceWorker
        .register(window.EwertWebPush.swUrl)
        .then(function(registration) {
          console.log('Service worker successfully registered.');
          return resolve(registration);
        })
        .catch(function(err) {
          console.error('Unable to register service worker.', err);
          reject(err);
        });
    });
  }

  function subscribeUserToPush(registration) {
    return new Promise(resolve => {
      const subscribeOptions = {
        userVisibleOnly: true,
        applicationServerKey: urlBase64ToUint8Array(window.EwertWebPush.publicKey),
      };

      registration.pushManager.subscribe(subscribeOptions).then(function(pushSubscription) {
        console.log('Received PushSubscription: ', JSON.stringify(pushSubscription));
        return resolve(pushSubscription);
      });
    });
  }

  function sendSubscriptionToServer(subscription) {
    console.log('Sending subscription to server...');
    var sub = subscription.toJSON();
    return fetch('/webpush/subscribtion/new', {
      method: 'POST',
      headers: new Headers({
        'Content-Type': 'application/json',
      }),
      body: JSON.stringify({
        endpoint: sub.endpoint,
        expirationTime: sub.expirationTime,
        p256dh: sub.keys.p256dh,
        auth: sub.keys.auth,
      }),
    });
  }

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
}
