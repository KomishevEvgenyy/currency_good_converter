<?php

namespace Modules\Currency\Infrastructure;

use Modules\Currency\Enum\CurrencyTypeEnum;
use Illuminate\Support\Facades\Http;
use Modules\Currency\Domain\Contracts\CurrencyRateReader;
use Modules\Currency\Domain\CurrencyRate;

readonly class ErApiCurrencyRepository implements CurrencyRateReader
{
    private const int TIMEOUT_SECONDS = 3;

    /**
     * @return CurrencyRate[]
     */
    public function getAll(): array
    {
        $url = config('currency.urls.er_api') . '/' . CurrencyTypeEnum::UAH->upper();
        $response = Http::timeout(self::TIMEOUT_SECONDS)
            ->get($url)
            ->throw();

        $payload = $response->json();
        $rates = $payload['rates'] ?? null;

        if (! is_array($rates) || $rates === []) {
            throw new \RuntimeException('ER API returned empty or invalid rates payload');
        }

        $exchangeDate = (string) ($payload['time_last_update_utc'] ?? now()->toDateString());
        $result = [];

        foreach ($rates as $code => $ratePerUah) {
            if (! is_numeric($ratePerUah) || (float) $ratePerUah <= 0) {
                continue;
            }

            $currencyCode = strtoupper((string) $code);
            $uahPerCurrency = $currencyCode === CurrencyTypeEnum::UAH->upper()
                ? 1.0
                : 1 / (float) $ratePerUah;

            $result[] = new CurrencyRate(
                name: $currencyCode,
                rate: $uahPerCurrency,
                currencyCode: $currencyCode,
                exchangeDate: $exchangeDate,
            );
        }

        if ($result === []) {
            throw new \RuntimeException('ER API rates could not be mapped to currency rates');
        }

        return $result;
    }
}
