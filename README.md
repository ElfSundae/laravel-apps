# Laravel Apps

[![Latest Version on Packagist](https://img.shields.io/packagist/v/elfsundae/laravel-apps.svg?style=flat-square)](https://packagist.org/packages/elfsundae/laravel-apps)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![tests](https://github.com/ElfSundae/laravel-apps/actions/workflows/tests.yml/badge.svg)](https://github.com/ElfSundae/laravel-apps/actions/workflows/tests.yml)
[![StyleCI](https://styleci.io/repos/112607947/shield)](https://styleci.io/repos/112607947)
[![SymfonyInsight Grade](https://img.shields.io/symfony/i/grade/0dca9ec7-57c6-4162-97a5-8d0a3159844d?style=flat-square)](https://insight.symfony.com/projects/0dca9ec7-57c6-4162-97a5-8d0a3159844d)
[![Quality Score](https://img.shields.io/scrutinizer/g/ElfSundae/laravel-apps.svg?style=flat-square)](https://scrutinizer-ci.com/g/ElfSundae/laravel-apps)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/ElfSundae/laravel-apps/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/ElfSundae/laravel-apps/?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/elfsundae/laravel-apps.svg?style=flat-square)](https://packagist.org/packages/elfsundae/laravel-apps)

This package provides basic support for Laravel multi-application.

<!-- MarkdownTOC -->

- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
    - [Obtain Application Manager Instance](#obtain-application-manager-instance)
    - [Retrieve Application URL Configuration](#retrieve-application-url-configuration)
    - [Determine The Current Application Identifier](#determine-the-current-application-identifier)
    - [Selectively Register Service Providers](#selectively-register-service-providers)
    - [Define Application Routes](#define-application-routes)
    - [Generate URL](#generate-url)
    - [Custom Root URL For asset\(\) Helper](#custom-root-url-for-asset-helper)
    - [Extend Application Manager](#extend-application-manager)
- [Testing](#testing)
- [License](#license)

<!-- /MarkdownTOC -->

## Installation

You can install this package using the [Composer](https://getcomposer.org) manager:

```sh
$ composer require elfsundae/laravel-apps
```

For earlier Laravel than v5.5, you need to register the service provider manually:

```php
ElfSundae\Apps\AppsServiceProvider::class,
```

Then publish the [configuration file](config/apps.php) to your `config` directory:

```sh
$ php artisan vendor:publish --tag=laravel-apps
```

## Configuration

The `url` configuration option defines the root URL of each application:

```php
'url' => [
    'web' => 'https://example.com',
    'admin' => 'https://example.com/admin',
    'mobile' => 'https://m.example.com',
    'api' => 'https://api.example.com',
    'assets' => 'https://assets.foo.net',
],
```

The `providers` array lists the class names of service providers for each application, you may configure this to [selectively register service providers](#selectively-register-service-providers).

The `config` option may be used to override the default configurations for each application. Additionally, you may wish to put all of your application defaults in one place instead of editing separate configuration files, just put them in the `default` key:

```php
'config' => [

    'default' => [
        'app.timezone' => 'Asia/Shanghai',
        'app.log' => env('APP_LOG', 'daily'),
        'app.log_max_files' => 50,
        'filesystems.disks.public.url' => env('APP_URL_ASSETS', env('APP_URL')).'/storage',

        'debugbar.options.auth.show_name' => false,
        'debugbar.options.route.label' => false,
    ],

    'admin' => [
        'auth.defaults' => [
            'guard' => 'admin',
            'passwords' => 'admin_users',
        ],
        'filesystems.default' => 'public',
        'session.domain' => env('SESSION_DOMAIN_ADMIN', null),
    ],

    'api' => [
        'auth.defaults.guard' => 'api',
        'filesystems.default' => 's3',
    ],

],
```

## Usage

### Obtain Application Manager Instance

You may obtain the application manager instance using the `Apps` facade, the `apps()` helper function or injecting the `ElfSundae\Apps\AppManager` dependency.

### Retrieve Application URL Configuration

```php
use ElfSundae\Apps\Facades\Apps;

// Get all application URLs
Apps::urls();

// Get all application identifiers
apps()->ids();

// Get URL root for the assets app
apps()->root('assets');

// Get URL domain for the api app
apps()->domain('api');

// Get URL prefix for the admin app
apps()->prefix('admin');
```

### Determine The Current Application Identifier

The application identifier to the current request can be determined via the `id` method on the app manager, or using the corresponding `app_id` helper function:

```php
$appId = Apps::id();

$appId = app_id();
```

You may also pass arguments to the `id` method to check if the current application identifier matches a given value. The method will return `true` if the identifier matches any of the given values:

```php
if (Apps::id('admin')) {
    // Currently requesting admin app
}

if (app_id('web', 'admin')) {
    // Currently requesting either web app OR admin app
}
```

### Selectively Register Service Providers

Instead of adding all service providers to the `config/app.php` file, you may want to selectively register service providers for certain sub applications to optimize performance. To do so, simply list the providers to the `providers` array in the `config/apps.php` configuration file:

```php
'providers' => [

    'admin' => [
        Rap2hpoutre\LaravelLogViewer\LaravelLogViewerServiceProvider::class,
        Yajra\DataTables\DataTablesServiceProvider::class,
        App\Providers\AdminServiceProvider::class,
    ],

    'api' => [
        App\Providers\ApiServiceProvider::class,
    ],

],
```

:warning: If your application runs on Laravel 5.5+ which support [package discovery](https://laravel.com/docs/5.5/packages#package-discovery), you also need to disable discovery for the optional packages in the `composer.json` file:

```js
"extra": {
    "laravel": {
        "dont-discover": [
            "rap2hpoutre/laravel-log-viewer",
            "yajra/laravel-datatables-oracle"
        ]
    }
}
```

> Don't worry about the [deferred service providers](https://laravel.com/docs/providers#deferred-providers), as the deferred providers are only loaded when needed.

### Define Application Routes

The `routes` method on the app manager helps you define route group for each application. In general, you will call it in the `map` method of your `RouteServiceProvider`:

```
class RouteServiceProvider extends ServiceProvider
{
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        apps()->routes();
    }
}
```

The route files named with the application identifiers in the `routes` directory will be automatically loaded, such as `routes/web.php`, `routes/admin.php`.

By default, the `routes` method will assign the existing middleware group named with the application identifier or `web` to the route group, and the namespace applied to your controller routes will be `StudlyCase` of the application identifier.

For example, `apps()->routes()` is equivalent to:

```php
// web: https://example.com
Route::group([
    'domain' => 'example.com',
    'middleware' => 'web',
    'namespace' => $this->namespace.'\Web',
], function ($router) {
    require base_path('routes/web.php');
});

// api: https://api.example.com
Route::group([
    'domain' => 'api.example.com',
    'middleware' => 'api',
    'namespace' => $this->namespace.'\Api',
], function ($router) {
    require base_path('routes/api.php');
});

// admin: https://example.com/admin
Route::group([
    'domain' => 'example.com',
    'prefix' => 'admin',
    'middleware' => 'web', // suppose if the "admin" middleware group does not exist
    'namespace' => $this->namespace.'\Admin',
], function ($router) {
    require base_path('routes/admin.php');
});

// ...
```

Of course, you are free to specify any route attributes:

```php
apps()->routes([
    'web' => [
        'namespace' => $this->namespace,
    ],
    'admin' => [
        'middleware' => ['web', 'admin.ip'],
        'as' => 'admin.',
        'where' => [
            'id' => '[0-9]+',
        ],
    ],
]);
```

In addition to an array, you can pass a Closure to the `routes` method:

```php
apps()->routes(function ($id, $apps) {
    return ['as' => $id.'.'];
});
```

### Generate URL

You can use the `url` method or the corresponding `app_url` helper function to generate an absolute URL to a path for a specified application:

```php
apps()->url('admin', 'user', [$user]); // https://example.com/admin/user/123

app_url('api', 'posts'); // https://api.example.com/posts
```

The `asset` method generates a URL with the root URL of the `assets` application:

```php
apps()->asset('js/app.js'); // https://assets.foo.net/js/app.js
```

### Custom Root URL For asset() Helper

The Laravel built-in `URL::asset` method or the corresponding `asset`, `secure_asset` helper functions are designed to generate URL for the application assets. In most applications, we will probably specify a cookie-free domain or use CDN for the assets, however we can not set custom root URL for these built-in assets methods, and for now there is no elegant way to extend the core `UrlGenerator`.

You may use `URL::assetFrom`, `Apps::asset`, or a custom helper function to generate assets URLs, but it is awfully boring to replace all `asset()` calls to your own assets method for the third-party packages. Maybe a better workaround is overwriting the built-in `asset` helper: define your `asset` function before including the Composer autoloader file, in your `public/index.php` file:

```php
function asset($path, $secure = null)
{
    return apps()->asset($path, $secure);
}

require __DIR__.'/../vendor/autoload.php';
```

This package ships with an [`asset.php`](asset.php) file you may include to use the root URL of the `assets` application for the `asset()` helper:

```php
require __DIR__.'/../vendor/elfsundae/laravel-apps/asset.php';

require __DIR__.'/../vendor/autoload.php';
```

> FYI, related PR [laravel/framework#22372](https://github.com/laravel/framework/pull/22372).

### Extend Application Manager

The [`AppManager`](src/AppManager.php) class is macroable, that means you can use the `macro` method to extend it:

```php
Apps::macro('route', function ($name, $parameters = []) {
    return URL::route($this->id().'.'.$name, $parameters);
});
```

## Testing

```sh
$ composer test
```

## License

This package is open-sourced software licensed under the [MIT License](LICENSE.md).
