<?php

return [
    /*
    |--------------------------------------------------------------------------
    | MatrimonyCI Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration spécifique à l'application MatrimonyCI
    |
    */

    'moderation' => [
        'enabled' => env('REKOGNITION_ENABLED', true),
        'service' => 'aws-rekognition', // aws-rekognition, deepai, manual
        'min_confidence' => env('REKOGNITION_MIN_CONFIDENCE', 80),
        'max_attempts' => 3,
        'async' => true,
    ],

    'violations' => [
        'max_warnings' => env('MAX_WARNINGS_BEFORE_BAN', 3),
        'suspension_duration_days' => env('SUSPENSION_DURATION_DAYS', 7),
        'max_violations_for_ban' => env('MAX_PROFILE_VIOLATIONS_FOR_BAN', 5),
    ],

    'subscriptions' => [
        'tiers' => [
            'free' => [
                'name' => 'Gratuit',
                'price' => 0,
                'currency' => 'XOF', // Franc CFA
                'duration_days' => null, // Illimité
                'features' => [
                    'profile_creation' => true,
                    'photo_upload' => true,
                    'max_photos' => 3,
                    'like' => true,
                    'comment' => false,
                    'message' => false,
                    'advanced_search' => false,
                    'profile_verification' => false,
                ],
            ],
            'premium_monthly' => [
                'name' => 'Premium Mensuel',
                'price' => 5000,
                'currency' => 'XOF',
                'duration_days' => 30,
                'features' => [
                    'profile_creation' => true,
                    'photo_upload' => true,
                    'max_photos' => 20,
                    'video_upload' => true,
                    'max_videos' => 5,
                    'like' => true,
                    'comment' => true,
                    'message' => true,
                    'advanced_search' => true,
                    'profile_verification' => true,
                    'priority_matching' => false,
                ],
            ],
            'premium_annual' => [
                'name' => 'Premium Annuel',
                'price' => 50000,
                'currency' => 'XOF',
                'duration_days' => 365,
                'features' => [
                    'profile_creation' => true,
                    'photo_upload' => true,
                    'max_photos' => 50,
                    'video_upload' => true,
                    'max_videos' => 20,
                    'like' => true,
                    'comment' => true,
                    'message' => true,
                    'advanced_search' => true,
                    'profile_verification' => true,
                    'priority_matching' => true,
                    'featured_profile' => true,
                ],
            ],
        ],
    ],

    'uploads' => [
        'max_file_size' => 52428800, // 50MB en bytes
        'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'mov', 'avi'],
        'photo' => [
            'min_width' => 400,
            'min_height' => 400,
            'max_width' => 4000,
            'max_height' => 4000,
            'allowed_mimes' => ['image/jpeg', 'image/png', 'image/gif'],
        ],
        'video' => [
            'max_duration_seconds' => 60,
            'allowed_mimes' => ['video/mp4', 'video/quicktime', 'video/x-msvideo'],
        ],
        'storage' => env('FILESYSTEM_DISK', 'local'),
    ],

    'rate_limiting' => [
        'likes_per_hour' => 100,
        'comments_per_hour' => 50,
        'messages_per_hour' => 100,
        'reports_per_day' => 10,
        'api_calls_per_minute' => 60,
    ],

    'mobile_money' => [
        'default' => 'orange-money',
        'providers' => [
            'orange-money' => [
                'enabled' => true,
                'api_url' => env('ORANGE_MONEY_API_URL'),
                'client_id' => env('ORANGE_MONEY_CLIENT_ID'),
                'client_secret' => env('ORANGE_MONEY_CLIENT_SECRET'),
                'merchant_id' => env('ORANGE_MONEY_MERCHANT_ID'),
                'currency' => 'XOF',
            ],
            'mtn-money' => [
                'enabled' => true,
                'api_url' => env('MTN_MONEY_API_URL'),
                'primary_key' => env('MTN_MONEY_PRIMARY_KEY'),
                'secondary_key' => env('MTN_MONEY_SECONDARY_KEY'),
                'user_id' => env('MTN_MONEY_USER_ID'),
                'currency' => 'XOF',
            ],
            'moov-money' => [
                'enabled' => true,
                'api_url' => env('MOOV_MONEY_API_URL'),
                'api_key' => env('MOOV_MONEY_API_KEY'),
                'account_id' => env('MOOV_MONEY_ACCOUNT_ID'),
                'currency' => 'XOF',
            ],
            'wave' => [
                'enabled' => true,
                'api_url' => env('WAVE_MONEY_API_URL'),
                'api_key' => env('WAVE_MONEY_API_KEY'),
                'merchant_id' => env('WAVE_MONEY_MERCHANT_ID'),
                'currency' => 'XOF',
            ],
        ],
    ],

    'features' => [
        'terms_acceptance_required' => true,
        'email_verification_required' => true,
        'phone_verification_required' => true,
        'profile_photo_required' => true,
        'auto_moderation' => true,
    ],
];
