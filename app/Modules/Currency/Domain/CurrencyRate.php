<?php

namespace Modules\Currency\Domain;

readonly class CurrencyRate
{
    public function __construct(
        public string $name,
        public float $rate,
        public string $currencyCode,
        public string $exchangeDate,
    ) {}
}
