<?php

namespace Tests\Unit;

use Modules\Currency\Enum\CurrencyTypeEnum;
use Modules\Currency\Domain\Contracts\CurrencyRateReader;
use Modules\Currency\Domain\CurrencyRate;
use Modules\Currency\Infrastructure\FallbackCurrencyRateRepository;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class FallbackCurrencyRateRepositoryTest extends TestCase
{
    public function test_returns_primary_rates_when_primary_succeeds(): void
    {
        $primary = new class implements CurrencyRateReader {
            public function getAll(): array
            {
                return [new CurrencyRate('P', 1.0, CurrencyTypeEnum::USD->upper(), 'd')];
            }
        };
        $fallback = new class implements CurrencyRateReader {
            public function getAll(): array
            {
                return [new CurrencyRate('F', 2.0, CurrencyTypeEnum::EUR->upper(), 'd')];
            }
        };

        $repo = new FallbackCurrencyRateRepository($primary, $fallback, new NullLogger());

        $rates = $repo->getAll();

        $this->assertCount(1, $rates);
        $this->assertSame(CurrencyTypeEnum::USD->upper(), $rates[0]->currencyCode);
    }

    public function test_returns_fallback_rates_when_primary_throws(): void
    {
        $primary = new class implements CurrencyRateReader {
            public function getAll(): array
            {
                throw new \RuntimeException('primary down');
            }
        };
        $fallback = new class implements CurrencyRateReader {
            public function getAll(): array
            {
                return [new CurrencyRate('F', 2.0, CurrencyTypeEnum::EUR->upper(), 'd')];
            }
        };

        $repo = new FallbackCurrencyRateRepository($primary, $fallback, new \Psr\Log\NullLogger());

        $rates = $repo->getAll();

        $this->assertCount(1, $rates);
        $this->assertSame(CurrencyTypeEnum::EUR->upper(), $rates[0]->currencyCode);
    }
}
