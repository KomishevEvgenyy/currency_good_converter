<?php

namespace Modules\Currency\Infrastructure;

use Modules\Currency\Domain\CurrencyRate;
use Modules\Currency\Enum\CurrencyTypeEnum;

final class ErApiCurrencyRateResponseMapper
{
    /**
     * @return CurrencyRate[]
     */
    public function map(mixed $payload): array
    {
        if (! is_array($payload)) {
            throw new \RuntimeException('ER API returned invalid payload: expected top-level object array');
        }

        $rates = $payload['rates'] ?? null;
        $exchangeDate = $payload['time_last_update_utc'] ?? null;

        if (! is_array($rates) || $rates === []) {
            throw new \RuntimeException('ER API returned empty or invalid rates payload');
        }

        if (! is_string($exchangeDate) || trim($exchangeDate) === '') {
            throw new \RuntimeException('ER API returned invalid payload: [time_last_update_utc] must be a non-empty string');
        }

        $result = [];

        foreach ($rates as $code => $ratePerUah) {
            $currencyCode = strtoupper((string) $code);

            if ($currencyCode === '' || ! is_numeric($ratePerUah) || (float) $ratePerUah <= 0) {
                throw new \RuntimeException(
                    "ER API returned invalid rate row for currency [{$currencyCode}]: rate must be numeric and greater than zero"
                );
            }

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

        return $result;
    }
}
