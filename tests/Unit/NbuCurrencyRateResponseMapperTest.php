<?php

namespace Tests\Unit;

use Modules\Currency\Infrastructure\NbuCurrencyRateResponseMapper;
use PHPUnit\Framework\TestCase;

class NbuCurrencyRateResponseMapperTest extends TestCase
{
    public function test_it_maps_valid_payload(): void
    {
        $mapper = new NbuCurrencyRateResponseMapper();

        $rates = $mapper->map([
            [
                'txt' => 'US Dollar',
                'rate' => 43.0996,
                'cc' => 'USD',
                'exchangedate' => '02.03.2026',
            ],
        ]);

        $this->assertCount(1, $rates);
        $this->assertSame('US Dollar', $rates[0]->name);
        $this->assertSame(43.0996, $rates[0]->rate);
        $this->assertSame('USD', $rates[0]->currencyCode);
        $this->assertSame('02.03.2026', $rates[0]->exchangeDate);
    }

    public function test_it_throws_on_non_array_payload(): void
    {
        $mapper = new NbuCurrencyRateResponseMapper();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('NBU API returned invalid payload: expected top-level array');

        $mapper->map('invalid');
    }

    public function test_it_throws_on_empty_payload(): void
    {
        $mapper = new NbuCurrencyRateResponseMapper();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('NBU API returned invalid payload: expected at least one rate row');

        $mapper->map([]);
    }

    public function test_it_throws_when_row_is_not_array(): void
    {
        $mapper = new NbuCurrencyRateResponseMapper();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('NBU API returned invalid rate row at index [0]: expected object array');

        $mapper->map(['invalid']);
    }

    public function test_it_throws_when_required_key_is_missing(): void
    {
        $mapper = new NbuCurrencyRateResponseMapper();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('NBU API returned invalid rate row at index [0]: missing key [txt]');

        $mapper->map([
            [
                'rate' => 43.0996,
                'cc' => 'USD',
                'exchangedate' => '02.03.2026',
            ],
        ]);
    }

    public function test_it_throws_when_rate_is_not_numeric(): void
    {
        $mapper = new NbuCurrencyRateResponseMapper();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('NBU API returned invalid rate row at index [0]: [rate] must be numeric');

        $mapper->map([
            [
                'txt' => 'US Dollar',
                'rate' => 'not-a-number',
                'cc' => 'USD',
                'exchangedate' => '02.03.2026',
            ],
        ]);
    }
}
