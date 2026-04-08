<?php

namespace Modules\Currency\Infrastructure;

use Illuminate\Support\Facades\Http;
use Modules\Currency\Domain\Contracts\CurrencyRateReader;

readonly class NbuApiCurrencyRepository implements CurrencyRateReader
{
    public function __construct(
        private NbuCurrencyRateResponseMapper $mapper,
    ) {}

    /**
     * @return \Modules\Currency\Domain\CurrencyRate[]
     */
    public function getAll(): array
    {
        $response = Http::connectTimeout((int) config('currency.http.connect_timeout_seconds', 3))
            ->timeout((int) config('currency.http.timeout_seconds', 3))
            ->acceptJson()
            ->get(config('currency.urls.nbu'), ['json' => ''])
            ->throw();

        return $this->mapper->map($response->json());
    }
}
