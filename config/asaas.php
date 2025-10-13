<?php

return [

    /*
    |--------------------------------------------------------------------------
    | ASAAS API Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your ASAAS API settings. You can get your API key
    | from the ASAAS dashboard at https://www.asaas.com/
    |
    */

    'api_key' => env('ASAAS_API_KEY'),

    'environment' => env('ASAAS_ENVIRONMENT', 'sandbox'), // 'sandbox' or 'production'

    'base_url' => [
        'sandbox' => 'https://sandbox.asaas.com/api/v3',
        'production' => 'https://www.asaas.com/api/v3'
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook Configuration
    |--------------------------------------------------------------------------
    |
    | Configure webhook settings for payment notifications
    |
    */

    'webhook' => [
        'url' => env('ASAAS_WEBHOOK_URL'),
        'access_token' => env('ASAAS_WEBHOOK_TOKEN'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Methods Configuration
    |--------------------------------------------------------------------------
    |
    | Configure available payment methods and their settings
    |
    */

    'payment_methods' => [
        'pix' => [
            'enabled' => env('ASAAS_PIX_ENABLED', true),
            'expiration_minutes' => env('ASAAS_PIX_EXPIRATION', 1440), // 24 hours
        ],
        'credit_card' => [
            'enabled' => env('ASAAS_CREDIT_CARD_ENABLED', true),
            'installments' => [
                'max' => env('ASAAS_MAX_INSTALLMENTS', 12),
                'min_amount' => env('ASAAS_MIN_INSTALLMENT_AMOUNT', 5.00),
            ],
        ],
        'bank_slip' => [
            'enabled' => env('ASAAS_BANK_SLIP_ENABLED', true),
            'due_days' => env('ASAAS_BANK_SLIP_DUE_DAYS', 7),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Settings
    |--------------------------------------------------------------------------
    |
    | Default settings for payments
    |
    */

    'defaults' => [
        'description' => 'Pagamento via ASAAS',
        'external_reference' => null,
        'fine' => [
            'value' => 2.00, // 2% de multa
        ],
        'interest' => [
            'value' => 1.00, // 1% de juros ao mês
        ],
        'discount' => [
            'value' => 0,
            'due_date_limit_days' => 0,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Customer Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for customer management
    |
    */

    'customer' => [
        'auto_create' => true,
        'update_existing' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Configure logging for ASAAS operations
    |
    */

    'logging' => [
        'enabled' => env('ASAAS_LOGGING_ENABLED', true),
        'log_requests' => env('ASAAS_LOG_REQUESTS', false),
        'log_responses' => env('ASAAS_LOG_RESPONSES', false),
    ],

];