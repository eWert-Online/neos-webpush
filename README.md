# Neos WebPush

This package provides a backend module to manage and send Web Push Notifications.

## Installation

1. Run the following command in your site package

    `composer require ewert/neos-webpush --no-update`

2. Then run `composer update` in your projects root folder.

3. Then you can add the `WebPushAdministrator` role to the users who need access to the new backend module.

## Configuration
Browsers need to verify your identity. A standard called VAPID can authenticate you for all browsers. <br />
You'll need to create and provide a public and private key for your server. <br />
These keys must be safely stored and should not change. <br />

When you open up the Backend Module for the first time, an example configuration like the following with autogenerated keys will be created for you. <br />

```yaml
Ewert:
  WebPush:
    vapid:
      publicKey: ''
      privateKey: ''
```

## Screenshots
Overview:
![Push Module Screenshot](Documentation/Images/Overview.png?raw=true "Push Module Screenshot")

New Message
![New Push Message Screenshot](Documentation/Images/New_Message.png?raw=true "New Push Message Screenshot")
