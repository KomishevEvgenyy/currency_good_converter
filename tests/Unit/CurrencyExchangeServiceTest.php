<?php

namespace Tests\Unit;

use Modules\Currency\Enum\CurrencyTypeEnum;
use Modules\Currency\Domain\Contracts\CurrencyRateReader;
use Modules\Currency\Domain\CurrencyExchangeService;
use Modules\Currency\Domain\CurrencyRate;
use PHPUnit\Framework\TestCase;

class CurrencyExchangeServiceTest extends TestCase
{
    private function serviceWithRates(CurrencyRate ...$rates): CurrencyExchangeService
    {
        $reader = new class ($rates) implements CurrencyRateReader {
            /**
             * @param array<int, CurrencyRate> $rates
             */
            public function __construct(private array $rates) {}

            public function getAll(): array
            {
                return $this->rates;
            }
        };

        return new CurrencyExchangeService($reader);
    }

    public function test_get_rate_by_code_normalizes_whitespace_and_case(): void
    {
        $service = $this->serviceWithRates(
            new CurrencyRate('Euro', 50.8661, CurrencyTypeEnum::EUR->upper(), '02.03.2026'),
        );

        $rate = $service->getRateByCode('  eur ');

        $this->assertSame(CurrencyTypeEnum::EUR->upper(), $rate->currencyCode);
        $this->assertSame(50.8661, $rate->rate);
    }

    public function test_get_rate_by_code_throws_when_missing(): void
    {
        $service = $this->serviceWithRates(
            new CurrencyRate('Dollar', 40.0, CurrencyTypeEnum::USD->upper(), '02.03.2026'),
        );

        $this->expectException(\RuntimeException::class);
        $service->getRateByCode('GBP');
    }

    public function test_uah_rate_is_synthetic_and_reuses_first_row_exchange_date(): void
    {
        $service = $this->serviceWithRates(
            new CurrencyRate('Dollar', 40.0, CurrencyTypeEnum::USD->upper(), '02.03.2026'),
        );

        $rate = $service->getRateByCode(CurrencyTypeEnum::UAH->upper());

        $this->assertSame(CurrencyTypeEnum::UAH->upper(), $rate->currencyCode);
        $this->assertSame(1.0, $rate->rate);
        $this->assertSame('02.03.2026', $rate->exchangeDate);
    }

    public function test_convert_preserves_decimal_amounts_without_truncation(): void
    {
        $service = $this->serviceWithRates(
            new CurrencyRate('Dollar', 40.0, CurrencyTypeEnum::USD->upper(), '02.03.2026'),
            new CurrencyRate('Euro', 50.8661, CurrencyTypeEnum::EUR->upper(), '02.03.2026'),
        );

        $converted = $service->convert(199.99, CurrencyTypeEnum::USD->upper(), CurrencyTypeEnum::UAH->upper());

        $this->assertEqualsWithDelta(199.99, $converted->originalPrice, 0.0001);
        $this->assertEqualsWithDelta(round(199.99 * 40.0, 2), $converted->convertedPrice, 0.001);
    }

    public function test_convert_usd_to_eur_uses_cross_rate_via_uah(): void
    {
        $service = $this->serviceWithRates(
            new CurrencyRate('Dollar', 40.0, CurrencyTypeEnum::USD->upper(), '02.03.2026'),
            new CurrencyRate('Euro', 50.8661, CurrencyTypeEnum::EUR->upper(), '02.03.2026'),
        );

        $converted = $service->convert(199.99, CurrencyTypeEnum::USD->upper(), CurrencyTypeEnum::EUR->upper());

        $expected = round(199.99 * (40.0 / 50.8661), 2);
        $this->assertEqualsWithDelta($expected, $converted->convertedPrice, 0.0001);
    }
}
