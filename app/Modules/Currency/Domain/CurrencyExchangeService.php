<?php

namespace Modules\Currency\Domain;

use Modules\Currency\Enum\CurrencyTypeEnum;
use Modules\Currency\Domain\Contracts\CurrencyRateReader;

final readonly class CurrencyExchangeService
{
    public function __construct(
        private CurrencyRateReader $repository,
    ) {}

    public function getRateByCode(string $currencyCode): CurrencyRate
    {
        $normalizedCode = $this->normalizeCurrencyCode($currencyCode);

        if ($normalizedCode === CurrencyTypeEnum::UAH->upper()) {
            $rates = $this->repository->getAll();
            $exchangeDate = ($rates[0]->exchangeDate ?? null) !== null
                ? $rates[0]->exchangeDate
                : now()->format('d.m.Y');

            return new CurrencyRate(
                name: 'Hryvnia',
                rate: 1.0,
                currencyCode: CurrencyTypeEnum::UAH->upper(),
                exchangeDate: $exchangeDate,
            );
        }

        foreach ($this->repository->getAll() as $currency) {
            if ($this->normalizeCurrencyCode($currency->currencyCode) === $normalizedCode) {
                return $currency;
            }
        }

        throw new \RuntimeException("Rate not found for currency code: {$normalizedCode}");
    }

    public function convert(float $price, string $fromCurrency, string $toCurrency): ConvertedPrice
    {
        $fromCode = $this->normalizeCurrencyCode($fromCurrency);
        $toCode = $this->normalizeCurrencyCode($toCurrency);

        if ($fromCode === $toCode) {
            return new ConvertedPrice(
                originalPrice: $price,
                convertedPrice: round($price, 2),
                fromCurrency: $fromCode,
                toCurrency: $toCode,
                rate: 1.0,
            );
        }

        $fromRate = $this->getRateByCode($fromCode);
        $toRate = $this->getRateByCode($toCode);

        // NBU returns rates relative to UAH; convert via UAH baseline.
        $convertedPrice = $price * ($fromRate->rate / $toRate->rate);
        $effectiveRate = $fromRate->rate / $toRate->rate;

        return new ConvertedPrice(
            originalPrice: $price,
            convertedPrice: round($convertedPrice, 2),
            fromCurrency: $fromCode,
            toCurrency: $toCode,
            rate: $effectiveRate,
        );
    }

    private function normalizeCurrencyCode(string $currencyCode): string
    {
        return strtoupper(trim($currencyCode));
    }
}
