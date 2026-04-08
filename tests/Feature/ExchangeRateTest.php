<?php

namespace Tests\Feature;

use Modules\Currency\Enum\CurrencyTypeEnum;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ExchangeRateTest extends TestCase
{
    private function fakeNbuApi(float $usdRate = 43.0996): void
    {
        Http::fake([
            'bank.gov.ua/*' => Http::response([
                ['r030' => 840, 'txt' => 'Долар США', 'rate' => $usdRate, 'cc' => CurrencyTypeEnum::USD->upper(), 'exchangedate' => '02.03.2026'],
                ['r030' => 978, 'txt' => 'Євро', 'rate' => 50.8661, 'cc' => CurrencyTypeEnum::EUR->upper(), 'exchangedate' => '02.03.2026'],
            ]),
        ]);
    }

    public function test_exchange_rate_usd_returns_successful_response(): void
    {
        $this->fakeNbuApi();

        $response = $this->getJson('/api/exchangeRate/usd');

        $response->assertStatus(200);
    }

    public function test_exchange_rate_usd_returns_correct_json_structure(): void
    {
        $this->fakeNbuApi();

        $response = $this->getJson('/api/exchangeRate/usd');

        $response->assertJsonStructure([
            'data' => ['currencyCode', 'rate', 'exchangeDate'],
        ]);
    }

    public function test_exchange_rate_usd_returns_correct_rate_value(): void
    {
        $this->fakeNbuApi(usdRate: 41.5);

        $response = $this->getJson('/api/exchangeRate/usd');

        $response->assertJson([
            'data' => [
                'currencyCode' => CurrencyTypeEnum::USD->upper(),
                'rate' => 41.5,
                'exchangeDate' => '02.03.2026',
            ],
        ]);
    }

    public function test_exchange_rate_eur_returns_uah_per_one_eur_from_nbu(): void
    {
        $this->fakeNbuApi();

        $response = $this->getJson('/api/exchangeRate/eur');

        $response->assertJson([
            'data' => [
                'currencyCode' => CurrencyTypeEnum::EUR->upper(),
                'rate' => 50.8661,
                'exchangeDate' => '02.03.2026',
            ],
        ]);
    }

    public function test_exchange_rate_resolves_currency_code_case_insensitively(): void
    {
        $this->fakeNbuApi();

        $this->getJson('/api/exchangeRate/EUR')
            ->assertOk()
            ->assertJsonPath('data.currencyCode', CurrencyTypeEnum::EUR->upper());
    }

    public function test_exchange_rate_uah_returns_synthetic_rate(): void
    {
        $this->fakeNbuApi();

        $response = $this->getJson('/api/exchangeRate/uah');

        $response->assertJson([
            'data' => [
                'currencyCode' => CurrencyTypeEnum::UAH->upper(),
                'rate' => 1.0,
                'exchangeDate' => '02.03.2026',
            ],
        ]);
    }

    public function test_exchange_rate_invalid_currency_returns_not_found(): void
    {
        $response = $this->getJson('/api/exchangeRate/gbp');

        $response->assertStatus(422);
    }

    public function test_exchange_rate_endpoint_does_not_require_authentication(): void
    {
        $this->fakeNbuApi();

        $response = $this->getJson('/api/exchangeRate/usd');

        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    public function test_exchange_rate_endpoint_rejects_non_get_methods(): void
    {
        $this->postJson('/api/exchangeRate/usd')->assertStatus(405);
        $this->putJson('/api/exchangeRate/usd')->assertStatus(405);
        $this->deleteJson('/api/exchangeRate/usd')->assertStatus(405);
    }
}
