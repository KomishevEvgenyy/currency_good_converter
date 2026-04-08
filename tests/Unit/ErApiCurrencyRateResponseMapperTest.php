<?php

namespace Tests\Unit;

use Modules\Currency\Infrastructure\ErApiCurrencyRateResponseMapper;
use PHPUnit\Framework\TestCase;

class ErApiCurrencyRateResponseMapperTest extends TestCase
{
    public function test_it_maps_valid_payload(): void
    {
        $mapper = new ErApiCurrencyRateResponseMapper();

        $rates = $mapper->map([
            'rates' => [
                'USD' => 0.025,
                'EUR' => 0.02,
                'UAH' => 1,
            ],
            'time_last_update_utc' => '2026-03-02T00:00:01+0000',
        ]);

        $this->assertCount(3, $rates);
        $this->assertSame('USD', $rates[0]->currencyCode);
        $this->assertSame(40.0, $rates[0]->rate);
        $this->assertSame('2026-03-02T00:00:01+0000', $rates[0]->exchangeDate);
        $this->assertSame('UAH', $rates[2]->currencyCode);
        $this->assertSame(1.0, $rates[2]->rate);
    }

    public function test_it_throws_on_non_array_payload(): void
    {
        $mapper = new ErApiCurrencyRateResponseMapper();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('ER API returned invalid payload: expected top-level object array');

        $mapper->map('invalid');
    }

    public function test_it_throws_when_exchange_date_is_missing(): void
    {
        $mapper = new ErApiCurrencyRateResponseMapper();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('ER API returned invalid payload: [time_last_update_utc] must be a non-empty string');

        $mapper->map([
            'rates' => [
                'USD' => 0.025,
            ],
        ]);
    }

    public function test_it_throws_when_rate_row_is_invalid(): void
    {
        $mapper = new ErApiCurrencyRateResponseMapper();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('ER API returned invalid rate row for currency [USD]: rate must be numeric and greater than zero');

        $mapper->map([
            'rates' => [
                'USD' => 0,
            ],
            'time_last_update_utc' => '2026-03-02T00:00:01+0000',
        ]);
    }
}
