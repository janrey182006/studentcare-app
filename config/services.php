<?php

return [

    'postmark' => ['token' => env('POSTMARK_API_KEY')],

    'resend' => ['key' => env('RESEND_API_KEY')],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    // Google reCAPTCHA v2 ("I'm not a robot" checkbox)
    'recaptcha' => [
        'enabled'  => env('RECAPTCHA_ENABLED', true),
        'site_key' => env('RECAPTCHA_SITE_KEY'),
        'secret'   => env('RECAPTCHA_SECRET_KEY'),
    ],

    // Email one-time password after login/registration
    'otp' => [
        'enabled'     => env('OTP_ENABLED', true),
        'demo_emails' => array_filter(array_map('trim', explode(',', (string) env('OTP_DEMO_EMAILS', '')))),
    ],
   'recaptcha' => [
    'enabled' => env('RECAPTCHA_ENABLED', false),
    'site_key' => env('RECAPTCHA_SITE_KEY', ''),
    'secret' => env('RECAPTCHA_SECRET_KEY', ''),
],

];
