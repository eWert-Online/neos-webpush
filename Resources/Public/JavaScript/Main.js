/**
 * @prettier
 */

(function() {
  'EwertWebPush' in window ||
    ((window.EwertWebPush = function() {
      window.EwertWebPush.q.push(arguments);
    }),
    (window.EwertWebPush.q = []));

  /**
   * Requests push permissions from the user and sends it (if granted) to the server
   *
   * @return  {void}
   */
  window.EwertWebPush.requestPermission = function() {
    if (!('serviceWorker' in navigator)) {
      return;
    }

    if (!('PushManager' in window)) {
      return;
    }

    window.EwertWebPush.registerServiceWorker().then(function(registration) {
      return new Promise(function(resolve, reject) {
        var permissionPromise = Notification.requestPermission(function(result) {
          resolve(result);
        });
        if (permissionPromise) {
          permissionPromise.then(resolve);
        }
      }).then(function(result) {
        if (result === 'granted') {
          window.EwertWebPush._subscribeUserToPush(registration).then(function(pushSubscription) {
            window.EwertWebPush._sendSubscriptionToServer(pushSubscription);
          });
        }
      });
    });
  };

  /**
   * Registers a service worker and waits until it's activated
   *
   * @return  {Promise<ServiceWorkerRegistration>}  The registered and active Service Worker
   */
  window.EwertWebPush.registerServiceWorker = function() {
    if ('serviceWorker' in navigator) {
      return new Promise(function(resolve, reject) {
        navigator.serviceWorker
          .register(window.EwertWebPush.swUrl)
          .then(function(registration) {
            var serviceWorker;
            if (registration.installing) {
              serviceWorker = registration.installing;
            } else if (registration.waiting) {
              serviceWorker = registration.waiting;
            } else if (registration.active) {
              serviceWorker = registration.active;
            }

            if (serviceWorker) {
              if (serviceWorker.state === 'activated') {
                return resolve(registration);
              }
              var listener = serviceWorker.addEventListener('statechange', function(e) {
                console.log(serviceWorker.state);
                if (serviceWorker.state === 'activated') {
                  serviceWorker.removeEventListener('statechange', listener);
                  return resolve(registration);
                }
              });
            }
          })
          .catch(function(err) {
            console.error('Unable to register service worker.', err);
            reject(err);
          });
      });
    }
  };

  /**
   * Subscribes the user to push
   *
   * @param   {ServiceWorkerRegistration}  registration
   * @return  {Promise<PushSubscription>}
   */
  window.EwertWebPush._subscribeUserToPush = function(registration) {
    return new Promise(function(resolve, reject) {
      var subscribeOptions = {
        userVisibleOnly: true,
        applicationServerKey: window.EwertWebPush._urlBase64ToUint8Array(window.EwertWebPush.publicKey),
      };

      registration.pushManager
        .subscribe(subscribeOptions)
        .then(function(pushSubscription) {
          console.log('Received PushSubscription: ', JSON.stringify(pushSubscription));
          return resolve(pushSubscription);
        })
        .catch(function(e) {
          throw new Error(e);
        });
    });
  };

  /**
   * Sends the provided subscription to the server
   *
   * @param   {PushSubscription}  subscription
   * @return  {Promise<Response>}
   */
  window.EwertWebPush._sendSubscriptionToServer = function(subscription) {
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
  };

  /**
   * @param   {String}  base64String
   * @return  {Array<Number>}
   */
  window.EwertWebPush._urlBase64ToUint8Array = function(base64String) {
    var padding = '='.repeat((4 - (base64String.length % 4)) % 4);
    var base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');

    var rawData = window.atob(base64);
    var outputArray = new Uint8Array(rawData.length);

    for (var i = 0; i < rawData.length; ++i) {
      outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
  };
})();
