<?php

namespace Modules\Currency\Infrastructure;

use Illuminate\Support\Facades\Cache;
use Modules\Currency\Domain\Contracts\CurrencyRateReader;

readonly class CachedCurrencyRateRepository implements CurrencyRateReader
{
    public function __construct(
        private CurrencyRateReader $inner,
        private string             $providerSignature,
        private int                $ttlSeconds = 600,
        private string             $cacheKeyPrefix = 'currency:rates',
    ) {}

    public function getAll(): array
    {
        $today = now()->toDateString();
        $cacheKey = "{$this->cacheKeyPrefix}:{$this->providerSignature}:{$today}";

        return Cache::remember(
            $cacheKey,
            now()->addSeconds($this->ttlSeconds),
            fn (): array => $this->inner->getAll(),
        );
    }
}
