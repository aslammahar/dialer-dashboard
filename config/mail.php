<?php

return [

    'default' => env('MAIL_MAILER', 'smtp'),

    'mailers' => [
        'smtp' => [
            'transport' => 'smtp',
            'host' => env('MAIL_HOST', 'smtp.hostinger.com'),
            'port' => env('MAIL_PORT', 465), // Use 465 for SSL or 587 for TLS
            'encryption' => env('MAIL_ENCRYPTION', 'ssl'), // Use 'ssl' for port 465 or 'tls' for port 587
            'username' => env('MAIL_USERNAME', 'hr@bicodev.com'),
            'password' => env('MAIL_PASSWORD', 'Hrjanjua123.'),
            'timeout' => null,
            'local_domain' => env('MAIL_EHLO_DOMAIN', 'bicodev.com'), // Optional
        ],

        'ses' => [
            'transport' => 'ses',
        ],

        'mailgun' => [
            'transport' => 'mailgun',
        ],

        'postmark' => [
            'transport' => 'postmark',
        ],

        'sendmail' => [
            'transport' => 'sendmail',
            'path' => env('MAIL_SENDMAIL_PATH', '/usr/sbin/sendmail -bs -i'),
        ],

        'log' => [
            'transport' => 'log',
            'channel' => env('MAIL_LOG_CHANNEL'),
        ],

        'array' => [
            'transport' => 'array',
        ],

        'failover' => [
            'transport' => 'failover',
            'mailers' => [
                'smtp',
                'log',
            ],
        ],
    ],

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'hr@bicodev.com'),
        'name' => env('MAIL_FROM_NAME', 'BiCodeV'),
    ],

    'markdown' => [
        'theme' => 'default',

        'paths' => [
            resource_path('views/vendor/mail'),
        ],
    ],

];
