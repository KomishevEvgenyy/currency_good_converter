<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Modules\Currency\Application\Facades\CurrencyExchangeFacade;

final readonly class CatalogService
{
    public function __construct(
        private CurrencyExchangeFacade $currencyExchange,
    ) {}

    public function getProducts($lowerCurrency): \Illuminate\Support\Collection
    {
        $response = Http::get(config('catalog.urls.dummyjson'), [
            'limit' => 5,
        ]);

        return collect($response->json('products'))->map(function (array $product) use ($lowerCurrency) {
            $price = $this->currencyExchange->catalogPriceInCurrency(
                (float) $product['price'],
                (string) $lowerCurrency,
            );

            return (object)[
                'id'        => $product['id'],
                'title'     => $product['title'],
                'price'     => $price,
                'rating'    => $product['rating'],
                'thumbnail' => $product['thumbnail'],
            ];
        });
    }
}
