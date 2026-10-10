<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Payment Gateway
    |--------------------------------------------------------------------------
    |
    | This option controls the default payment gateway used when initiating a
    | new payment. Per the Gateway Lock-in Rule, retried payments will always
    | reuse their original gateway instead of this default value.
    |
    */

    'default' => env('PAYMENT_GATEWAY', 'payhero'),

    /*
    |--------------------------------------------------------------------------
    | Payment Gateways Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration options for supported payment gateways.
    |
    */

    'gateways' => [

        'payhero' => [
            'api_username' => env('PAYHERO_API_USERNAME', ''),
            'api_password' => env('PAYHERO_API_PASSWORD', ''),
            'channel_id' => env('PAYHERO_CHANNEL_ID', ''),
            'callback_url' => env('PAYHERO_CALLBACK_URL', env('APP_URL', 'http://127.0.0.1:8000') . '/api/payments/callback/payhero'),
            'base_url' => env('PAYHERO_BASE_URL', 'https://backend.payhero.co.ke/api/v2'),
        ],

        'daraja' => [
            'consumer_key' => env('DARAJA_CONSUMER_KEY', ''),
            'consumer_secret' => env('DARAJA_CONSUMER_SECRET', ''),
            'passkey' => env('DARAJA_PASSKEY', ''),
            'shortcode' => env('DARAJA_SHORTCODE', ''),
            'callback_url' => env('DARAJA_CALLBACK_URL', env('APP_URL', 'http://127.0.0.1:8000') . '/api/payments/callback/daraja'),
            'env' => env('DARAJA_ENV', 'sandbox'),
        ],

    ],

];
