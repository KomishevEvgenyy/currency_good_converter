<?php

namespace Modules\Currency\Application\Facades;

use Modules\Currency\Enum\CurrencyTypeEnum;
use Modules\Currency\Domain\ConvertedPrice;
use Modules\Currency\Domain\CurrencyExchangeService;
use Modules\Currency\Domain\CurrencyRate;

readonly class CurrencyExchangeFacade
{
    public function __construct(
        private CurrencyExchangeService $service,
    ) {}

    public function getRateByCode(string $currencyCode): CurrencyRate
    {
        return $this->service->getRateByCode($currencyCode);
    }

    public function convert(float $price, string $fromCurrency, string $toCurrency): ConvertedPrice
    {
        return $this->service->convert($price, $fromCurrency, $toCurrency);
    }

    /**
     * Catalog product prices from DummyJSON are in USD. Express the same price in the requested listing currency.
     */
    public function catalogPriceInCurrency(float $priceUsd, string $targetCurrencyCode): float
    {
        $code = strtoupper(trim($targetCurrencyCode));

        if ($code === CurrencyTypeEnum::USD->upper()) {
            return round($priceUsd, 2);
        }

        return $this->convert($priceUsd, CurrencyTypeEnum::USD->upper(), $code)->convertedPrice;
    }
}
