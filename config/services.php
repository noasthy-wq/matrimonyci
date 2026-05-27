<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    */

    'postmark' => [
        'secret' => env('POSTMARK_SECRET'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'mailgun' => [
        'secret' => env('MAILGUN_SECRET'),
        'domain' => env('MAILGUN_DOMAIN'),
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\Models\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook' => [
            'secret' => env('STRIPE_WEBHOOK_SECRET'),
            'tolerance' => env('STRIPE_WEBHOOK_TOLERANCE', 300),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | OAuth Configuration
    |--------------------------------------------------------------------------
    */

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],

    'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect' => env('FACEBOOK_REDIRECT_URI'),
        'fields' => 'id,name,email,picture.type(large)',
    ],

    /*
    |--------------------------------------------------------------------------
    | AWS Services
    |--------------------------------------------------------------------------
    */

    'aws' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'eu-west-1'),
        'bucket' => env('AWS_BUCKET'),
    ],

    'rekognition' => [
        'enabled' => env('REKOGNITION_ENABLED', true),
        'region' => env('AWS_DEFAULT_REGION', 'eu-west-1'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Mobile Money Services
    |--------------------------------------------------------------------------
    */

    'orange_money' => [
        'api_url' => env('ORANGE_MONEY_API_URL'),
        'client_id' => env('ORANGE_MONEY_CLIENT_ID'),
        'client_secret' => env('ORANGE_MONEY_CLIENT_SECRET'),
        'merchant_id' => env('ORANGE_MONEY_MERCHANT_ID'),
    ],

    'mtn_money' => [
        'api_url' => env('MTN_MONEY_API_URL'),
        'primary_key' => env('MTN_MONEY_PRIMARY_KEY'),
        'secondary_key' => env('MTN_MONEY_SECONDARY_KEY'),
        'user_id' => env('MTN_MONEY_USER_ID'),
    ],

    'moov_money' => [
        'api_url' => env('MOOV_MONEY_API_URL'),
        'api_key' => env('MOOV_MONEY_API_KEY'),
        'account_id' => env('MOOV_MONEY_ACCOUNT_ID'),
    ],

    'wave' => [
        'api_url' => env('WAVE_MONEY_API_URL'),
        'api_key' => env('WAVE_MONEY_API_KEY'),
        'merchant_id' => env('WAVE_MONEY_MERCHANT_ID'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Twilio Services
    |--------------------------------------------------------------------------
    */

    'twilio' => [
        'sid' => env('TWILIO_SID'),
        'auth_token' => env('TWILIO_AUTH_TOKEN'),
        'from' => env('TWILIO_PHONE_NUMBER'),
    ],
];
