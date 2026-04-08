<?php

namespace Modules\Currency\Domain;

readonly class ConvertedPrice
{
    public function __construct(
        public float $originalPrice,
        public float $convertedPrice,
        public string $fromCurrency,
        public string $toCurrency,
        public float $rate,
    ) {}
}
