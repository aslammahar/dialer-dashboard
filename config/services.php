<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'vicidial' => [
        'base_urls' => [
            1 => env('VICIDIAL_BASE_URL_1'),
            3 => env('VICIDIAL_BASE_URL_3'),
            4 => env('VICIDIAL_BASE_URL_4', env('VICIDIAL_BASE_URL')),
        ],
        'user' => env('VICIDIAL_USER'),
        'pass' => env('VICIDIAL_PASS'),
        'timeout' => env('VICIDIAL_TIMEOUT', 15),
    ],

 'dialer' => [
    'username'              => env('DIALER_BASIC_AUTH_USER'),
    'password'              => env('DIALER_BASIC_AUTH_PASS'),
    'cache_ttl'             => env('DIALER_CACHE_TTL', 300), // seconds — avoids hammering the dialer on every page load
    'monthly_sales_target'  => env('DIALER_MONTHLY_SALES_TARGET', 0),
    'monthly_sales_current' => env('DIALER_MONTHLY_SALES_CURRENT', 0),
],

];
