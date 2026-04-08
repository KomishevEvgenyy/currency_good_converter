<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Modules\Currency\Application\Facades\CurrencyExchangeFacade;

final readonly class CatalogService
{
    public function __construct(
        private CurrencyExchangeFacade $currencyExchange,
    ) {}

    public function getProducts(string $currencyCode): Collection
    {
        $response = Http::connectTimeout((int) config('catalog.http.connect_timeout_seconds', 3))
            ->timeout((int) config('catalog.http.timeout_seconds', 3))
            ->get(config('catalog.urls.dummyjson'), [
                'limit' => 5,
            ])
            ->throw();

        return collect($response->json('products', []))->map(function (array $product) use ($currencyCode): object {
            $price = $this->currencyExchange->catalogPriceInCurrency(
                (float) $product['price'],
                $currencyCode,
            );

            return (object) [
                'id' => $product['id'],
                'title' => $product['title'],
                'price' => $price,
                'rating' => $product['rating'],
                'thumbnail' => $product['thumbnail'],
            ];
        });
    }
}
