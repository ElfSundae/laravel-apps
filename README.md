# Laravel Multi Applications

[![Latest Version on Packagist](https://img.shields.io/packagist/v/elfsundae/laravel-apps.svg?style=flat-square)](https://packagist.org/packages/elfsundae/laravel-apps)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/ElfSundae/laravel-apps/master.svg?style=flat-square)](https://travis-ci.org/ElfSundae/laravel-apps)
[![StyleCI](https://styleci.io/repos/112607947/shield)](https://styleci.io/repos/112607947)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/b0dfa271-15a4-422a-8007-443d511d800d.svg?style=flat-square)](https://insight.sensiolabs.com/projects/b0dfa271-15a4-422a-8007-443d511d800d)
[![Quality Score](https://img.shields.io/scrutinizer/g/ElfSundae/laravel-apps.svg?style=flat-square)](https://scrutinizer-ci.com/g/ElfSundae/laravel-apps)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/ElfSundae/laravel-apps/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/ElfSundae/laravel-apps/?branch=master)

## Installation

You can install this package using the [Composer](https://getcomposer.org) manager:

```sh
$ composer require elfsundae/laravel-apps
```

For Lumen or earlier Laravel than v5.5, you need to register the service provider manually:

```php
ElfSundae\Laravel\Apps\AppsServiceProvider::class,
```

Then publish the configuration file:

```sh
$ php artisan vendor:publish --tag=laravel-apps
```

## License

This package is open-sourced software licensed under the [MIT License](LICENSE.md).
