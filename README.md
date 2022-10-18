# Realm Chat SDK

[![Latest Version on Packagist](https://img.shields.io/packagist/v/raditzfarhan/realm-chat.svg?style=flat-square)](https://packagist.org/packages/raditzfarhan/realm-chat)
[![Total Downloads](https://img.shields.io/packagist/dt/raditzfarhan/realm-chat.svg?style=flat-square)](https://packagist.org/packages/raditzfarhan/realm-chat)
![GitHub Actions](https://github.com/raditzfarhan/realm-chat/actions/workflows/main.yml/badge.svg)

This is Realm Chat SDK.

## Installation

You can install the package via composer:

```bash
composer require raditzfarhan/realm-chat
```

## Available Methods
- addDevice
- sendMessage
- sendButtonMessage
- sendTemplateMessage
- recentChat
- getContact
- checkNumber

## Usage

```php
use RaditzFarhan\RealmChat\RealmChat;


$realmChat = new RealmChat($apiKey);

$addDevice = $realmChat->addDevice('device name'); 

$realmChat->setDeviceId('deviceId');

$sendMessage = $realmChat->sendMessage(
    number: '6012XXXXXXX',
    message: 'Hello!'
);

```

### Testing

```bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email raditzfarhan@gmail.com instead of using the issue tracker.

## Credits

-   [Raditz Farhan](https://github.com/raditzfarhan)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
