<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use App\Services\Mappers\DummyJsonProductMapper;
use Modules\Currency\Application\Facades\CurrencyExchangeFacade;

final readonly class CatalogService
{
    public function __construct(
        private CurrencyExchangeFacade $currencyExchange,
        private DummyJsonProductMapper $productMapper,
    ) {}

    /**
     * @return Collection<int, object{id: int, title: string, price: float, rating: float, thumbnail: string}&\stdClass>
     */
    public function getProducts(string $currencyCode): Collection
    {
        $response = Http::connectTimeout((int) config('catalog.http.connect_timeout_seconds', 3))
            ->timeout((int) config('catalog.http.timeout_seconds', 3))
            ->get(config('catalog.urls.dummyjson'), [
                'limit' => 5,
            ])
            ->throw();

        $products = $this->productMapper->mapProducts($response->json());

        return collect($products)->map(function (array $product) use ($currencyCode): object {
            $price = $this->currencyExchange->catalogPriceInCurrency(
                (float) $product['price'],
                $currencyCode,
            );

            return (object) [
                'id' => $product['id'],
                'title' => $product['title'],
                'price' => $price,
                'rating' => (float) $product['rating'],
                'thumbnail' => $product['thumbnail'],
            ];
        });
    }
}
