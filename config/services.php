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

    'apinexus' =>
    [
        'usuario' => 'maulet',
        'password' => 'm4u13t',
        'api_token' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1bmlxdWVfbmFtZSI6Im1hdWxldCIsInByaW1hcnlzaWQiOiIzIiwibmJmIjoxNjkwNTYzNzUzLCJleHAiOjE2OTA2NTAxNTMsImlhdCI6MTY5MDU2Mzc1MywiaXNzIjoiaHR0cDovL2xvY2FsaG9zdDo1NzE1NSIsImF1ZCI6Imh0dHA6Ly9sb2NhbGhvc3Q6NTcxNTUifQ.w4KV8m6BzFQk5JLfVjC6KnICcRFSSgv43xq_bfrnqEk'
    ],
];
