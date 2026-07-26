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

    'google_analytics' => [
        'measurement_id' => env('GOOGLE_ANALYTICS_MEASUREMENT_ID'),
        'property_id' => env('GOOGLE_ANALYTICS_PROPERTY_ID'),
        'credentials_path' => env('GOOGLE_ANALYTICS_CREDENTIALS_PATH'),
    ],

    'google_maps' => [
        'api_key' => env('GOOGLE_MAPS_API_KEY'),
    ],

    'midtrans' => [
        'server_key' => env('MIDTRANS_SERVER_KEY'),
        'client_key' => env('MIDTRANS_CLIENT_KEY'),
        'api_url' => env('MIDTRANS_API_URL', 'https://api.midtrans.com'),
        'timeout' => (int) env('MIDTRANS_TIMEOUT', 4),
        'local_verification_enabled' => (bool) env('MIDTRANS_LOCAL_VERIFICATION_ENABLED', false),
    ],

];
