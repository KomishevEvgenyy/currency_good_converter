<?php

namespace Tests\Feature;

use App\Jobs\TrackCatalogCurrencyUsageJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class CatalogTest extends TestCase
{
    use RefreshDatabase;

    private function fakeApis(float $usdRate = 43.0996): void
    {
        Http::fake([
            'dummyjson.com/*' => Http::response([
                'products' => [
                    [
                        'id' => 1,
                        'title' => 'iPhone 5s',
                        'description' => 'A classic smartphone.',
                        'category' => 'smartphones',
                        'price' => 199.99,
                        'rating' => 2.83,
                        'brand' => 'Apple',
                        'thumbnail' => 'https://cdn.dummyjson.com/product-images/smartphones/iphone-5s/thumbnail.webp',
                    ],
                    [
                        'id' => 2,
                        'title' => 'iPhone 6',
                        'description' => 'A stylish smartphone.',
                        'category' => 'smartphones',
                        'price' => 299.99,
                        'rating' => 3.41,
                        'brand' => 'Apple',
                        'thumbnail' => 'https://cdn.dummyjson.com/product-images/smartphones/iphone-6/thumbnail.webp',
                    ],
                ],
                'total' => 2,
                'skip' => 0,
                'limit' => 5,
            ]),
            'bank.gov.ua/*' => Http::response([
                ['r030' => 840, 'txt' => 'Долар США', 'rate' => $usdRate, 'cc' => 'USD', 'exchangedate' => '02.03.2026'],
                ['r030' => 978, 'txt' => 'Євро', 'rate' => 50.8661, 'cc' => 'EUR', 'exchangedate' => '02.03.2026'],
            ]),
        ]);
    }

    public function test_catalog_usd_returns_successful_response(): void
    {
        $this->fakeApis();

        $response = $this->getJson('/api/catalog/usd');

        $response->assertStatus(200);
    }

    public function test_catalog_usd_returns_original_usd_price(): void
    {
        $this->fakeApis();

        $response = $this->getJson('/api/catalog/usd');

        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'title', 'price', 'rating', 'thumbnail'],
            ],
        ]);

        $data = $response->json('data');
        $this->assertEquals(199.99, $data[0]['price']);
        $this->assertEquals(299.99, $data[1]['price']);
    }

    public function test_catalog_uah_returns_converted_price_using_full_decimal_precision(): void
    {
        $this->fakeApis(usdRate: 40.0);

        $response = $this->getJson('/api/catalog/uah');

        $data = $response->json('data');
        $this->assertEqualsWithDelta(199.99 * 40.0, $data[0]['price'], 0.01);
        $this->assertEqualsWithDelta(299.99 * 40.0, $data[1]['price'], 0.01);
    }

    public function test_catalog_endpoint_does_not_require_authentication(): void
    {
        $this->fakeApis();

        $response = $this->getJson('/api/catalog/usd');

        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    public function test_catalog_eur_returns_usd_prices_converted_to_eur(): void
    {
        $this->fakeApis(usdRate: 40.0);

        $response = $this->getJson('/api/catalog/eur');

        $response->assertStatus(200);
        $data = $response->json('data');
        $eurRate = 50.8661;
        $this->assertEqualsWithDelta(round(199.99 * (40.0 / $eurRate), 2), $data[0]['price'], 0.01);
        $this->assertEqualsWithDelta(round(299.99 * (40.0 / $eurRate), 2), $data[1]['price'], 0.01);
    }

    public function test_catalog_rejects_unsupported_currency(): void
    {
        $response = $this->getJson('/api/catalog/GBP');

        $response->assertStatus(422);
    }

    public function test_catalog_endpoint_rejects_non_get_methods(): void
    {
        $this->postJson('/api/catalog/usd')->assertStatus(405);
        $this->putJson('/api/catalog/usd')->assertStatus(405);
        $this->deleteJson('/api/catalog/usd')->assertStatus(405);
    }

    public function test_catalog_dispatches_currency_usage_tracking_job(): void
    {
        Queue::fake();
        $this->fakeApis();

        $this->getJson('/api/catalog/usd');

        Queue::assertPushed(TrackCatalogCurrencyUsageJob::class, function (TrackCatalogCurrencyUsageJob $job): bool {
            return $job->currencyCode === 'USD';
        });
    }

    public function test_catalog_usage_job_receives_uppercase_currency_from_route(): void
    {
        Queue::fake();
        $this->fakeApis();

        $this->getJson('/api/catalog/eur');

        Queue::assertPushed(TrackCatalogCurrencyUsageJob::class, function (TrackCatalogCurrencyUsageJob $job): bool {
            return $job->currencyCode === 'EUR';
        });
    }
}
