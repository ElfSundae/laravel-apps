# Laravel Multi Applications

[![Latest Version on Packagist](https://img.shields.io/packagist/v/elfsundae/laravel-apps.svg?style=flat-square)](https://packagist.org/packages/elfsundae/laravel-apps)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![StyleCI](https://styleci.io/repos/112607947/shield)](https://styleci.io/repos/112607947)

## Installation

You can install this package using the [Composer](https://getcomposer.org) manager:

```sh
$ composer require elfsundae/laravel-apps
```

For Lumen or earlier Laravel than v5.5, you need to register the service provider manually:

```php
ElfSundae\Laravel\Apps\AppsServiceProvider::class,
```

Then publish the configuration file and assets:

```sh
$ php artisan vendor:publish --tag=laravel-apps
```

## License

This package is open-sourced software licensed under the [MIT License](LICENSE.md).
