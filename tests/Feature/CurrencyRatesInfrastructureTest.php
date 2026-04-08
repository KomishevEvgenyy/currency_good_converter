<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CurrencyRatesInfrastructureTest extends TestCase
{
    private function nbuRatesPayload(float $usdRate = 43.0): array
    {
        return [
            ['r030' => 840, 'txt' => 'Долар США', 'rate' => $usdRate, 'cc' => 'USD', 'exchangedate' => '02.03.2026'],
            ['r030' => 978, 'txt' => 'Євро', 'rate' => 50.8661, 'cc' => 'EUR', 'exchangedate' => '02.03.2026'],
        ];
    }

    private function erRatesPayload(): array
    {
        return [
            'rates' => [
                'USD' => 0.025,
                'EUR' => 0.02,
                'UAH' => 1,
            ],
            'time_last_update_utc' => '2026-03-02T00:00:01+0000',
        ];
    }

    public function test_fallback_uses_er_api_when_nbu_fails(): void
    {
        Http::fake([
            'bank.gov.ua/*' => Http::response([], 500),
            'open.er-api.com/*' => Http::response($this->erRatesPayload()),
        ]);

        $response = $this->getJson('/api/exchangeRate/usd');

        $response->assertOk();
        $response->assertJson([
            'data' => [
                'currencyCode' => 'USD',
                'rate' => 40.0,
            ],
        ]);
    }

    public function test_primary_provider_can_be_switched_to_er_api_via_config(): void
    {
        Config::set('currency.primary', 'er_api');
        Config::set('currency.fallback', 'nbu');

        $nbuCalls = 0;
        $erCalls = 0;

        Http::fake([
            'bank.gov.ua/*' => function () use (&$nbuCalls) {
                $nbuCalls++;

                return Http::response($this->nbuRatesPayload(99.0));
            },
            'open.er-api.com/*' => function () use (&$erCalls) {
                $erCalls++;

                return Http::response($this->erRatesPayload());
            },
        ]);

        $response = $this->getJson('/api/exchangeRate/usd');

        $response->assertOk();
        $this->assertSame(1, $erCalls);
        $this->assertSame(0, $nbuCalls);
        $response->assertJson([
            'data' => [
                'currencyCode' => 'USD',
                'rate' => 40.0,
            ],
        ]);
    }

    public function test_exchange_rate_only_hits_remote_rates_once_per_day_when_called_twice(): void
    {
        $nbuCalls = 0;

        Http::fake([
            'bank.gov.ua/*' => function () use (&$nbuCalls) {
                $nbuCalls++;

                return Http::response($this->nbuRatesPayload(43.0));
            },
        ]);

        $this->travelTo('2026-03-01 12:00:00');

        $this->getJson('/api/exchangeRate/usd');
        $this->getJson('/api/exchangeRate/usd');

        $this->assertSame(1, $nbuCalls);
    }

    public function test_exchange_rate_fetches_again_on_new_calendar_day(): void
    {
        $nbuCalls = 0;

        Http::fake([
            'bank.gov.ua/*' => function () use (&$nbuCalls) {
                $nbuCalls++;

                return Http::response($this->nbuRatesPayload(43.0));
            },
        ]);

        $this->travelTo('2026-03-01 12:00:00');
        $this->getJson('/api/exchangeRate/usd');

        $this->travelTo('2026-03-02 08:00:00');
        $this->getJson('/api/exchangeRate/usd');

        $this->assertSame(2, $nbuCalls);
    }
}
