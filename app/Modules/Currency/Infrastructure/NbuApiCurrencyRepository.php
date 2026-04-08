<?php

namespace Modules\Currency\Infrastructure;

use Illuminate\Support\Facades\Http;
use Modules\Currency\Domain\Contracts\CurrencyRateReader;
use Modules\Currency\Domain\CurrencyRate;

readonly class NbuApiCurrencyRepository implements CurrencyRateReader
{
    /**
     * @return CurrencyRate[]
     */
    public function getAll(): array
    {
        $response = Http::connectTimeout((int) config('currency.http.connect_timeout_seconds', 3))
            ->timeout((int) config('currency.http.timeout_seconds', 3))
            ->acceptJson()
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
