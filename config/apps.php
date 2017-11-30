<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application URLs
    |--------------------------------------------------------------------------
    |
    | The root URL for each sub application.
    |
    | The domain of each URL will be automatically avaliable in "apps.domain"
    | configuration.
    |
    */

    'url' => [
        'web' => env('APP_URL'),
        'admin' => env('APP_URL_ADMIN', env('APP_URL')),
        'api' => env('APP_URL_API', env('APP_URL')),
        'assets' => env('APP_URL_ASSETS', env('APP_URL')),
        'cdn' => env('APP_URL_CDN', env('APP_URL_ASSETS')),
    ],

];
