<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Primary rate provider
    |--------------------------------------------------------------------------
    |
    | Name must match a key under "providers". This is the first source used
    | by the composed CurrencyRateReader binding.
    |
    */
    'primary' => env('CURRENCY_PRIMARY', 'nbu'),

    /*
    |--------------------------------------------------------------------------
    | Fallback rate provider
    |--------------------------------------------------------------------------
    |
    | Used when the primary provider throws (timeout, HTTP error, invalid data).
    | Must be a different implementation if you want redundancy.
    |
    */
    'fallback' => env('CURRENCY_FALLBACK', 'er_api'),

    /*
    |--------------------------------------------------------------------------
    | Currency rates cache
    |--------------------------------------------------------------------------
    |
    | TTL controls intra-day freshness. Cache keys are date-scoped, so a new
    | calendar day always bypasses the previous day's cache automatically.
    |
    */
    'cache' => [
        'ttl_seconds' => 600,
        'key_prefix' => 'currency:rates',
    ],

    /*
    |--------------------------------------------------------------------------
    | Provider registry
    |--------------------------------------------------------------------------
    |
    | Register named providers here. Each maps to a class implementing
    | Modules\Currency\Domain\Contracts\CurrencyRateReader.
    |
    | To add a provider: add a key and class, set primary/fallback env vars.
    |
    */
    'providers' => [
        'nbu' => [
            'class' => \Modules\Currency\Infrastructure\NbuApiCurrencyRepository::class,
        ],
        'er_api' => [
            'class' => \Modules\Currency\Infrastructure\ErApiCurrencyRepository::class,
        ],
    ],

    'urls' => [
        'nbu' => env('NBU_API_URL'),
        'er_api' => env('ER_API_URL'),
    ],
];
