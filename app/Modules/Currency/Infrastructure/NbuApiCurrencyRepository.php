<?php

namespace Modules\Currency\Infrastructure;

use Illuminate\Support\Facades\Http;
use Modules\Currency\Domain\Contracts\CurrencyRateReader;
use Modules\Currency\Domain\CurrencyRate;

readonly class NbuApiCurrencyRepository implements CurrencyRateReader
{
    private const int TIMEOUT_SECONDS = 3;

    /**
     * @return CurrencyRate[]
     */
    public function getAll(): array
    {
        $response = Http::timeout(self::TIMEOUT_SECONDS)
            ->get(config('currency.urls.nbu'), ['json' => ''])
            ->throw();

        return array_map(
            fn (array $item) => new CurrencyRate(
                name: $item['txt'],
                rate: (float) $item['rate'],
                currencyCode: $item['cc'],
                exchangeDate: $item['exchangedate'],
            ),
            $response->json(),
        );
    }
}
