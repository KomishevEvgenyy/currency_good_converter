<?php

namespace Modules\Currency\Infrastructure;

use Illuminate\Support\Facades\Http;
use Modules\Currency\Domain\Contracts\CurrencyRateReader;
use Modules\Currency\Enum\CurrencyTypeEnum;

readonly class ErApiCurrencyRepository implements CurrencyRateReader
{
    public function __construct(
        private ErApiCurrencyRateResponseMapper $mapper,
    ) {}

    /**
     * @return \Modules\Currency\Domain\CurrencyRate[]
     */
    public function getAll(): array
    {
        $url = config('currency.urls.er_api') . '/' . CurrencyTypeEnum::UAH->upper();
        $response = Http::connectTimeout((int) config('currency.http.connect_timeout_seconds', 3))
            ->timeout((int) config('currency.http.timeout_seconds', 3))
            ->acceptJson()
            ->get($url)
            ->throw();

        return $this->mapper->map($response->json());
    }
}
