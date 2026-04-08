<?php

namespace Tests\Unit;

use Modules\Currency\Enum\CurrencyTypeEnum;
use Illuminate\Support\Facades\Cache;
use Modules\Currency\Domain\Contracts\CurrencyRateReader;
use Modules\Currency\Domain\CurrencyRate;
use Modules\Currency\Infrastructure\CachedCurrencyRateRepository;
use Tests\TestCase;

class CachedCurrencyRateRepositoryTest extends TestCase
{
    public function test_inner_reader_called_once_per_calendar_day(): void
    {
        $this->travelTo('2026-03-01 14:00:00');

        $inner = new class implements CurrencyRateReader {
            public int $calls = 0;

            public function getAll(): array
            {
                $this->calls++;

                return [new CurrencyRate(CurrencyTypeEnum::USD->upper(), 40.0, CurrencyTypeEnum::USD->upper(), '01.03.2026')];
            }
        };

        $cached = new CachedCurrencyRateRepository($inner, 'nbu|er', 600, 'currency:rates');

        $cached->getAll();
        $cached->getAll();

        $this->assertSame(1, $inner->calls);

        $this->travelTo('2026-03-02 00:00:01');
        $cached->getAll();

        $this->assertSame(2, $inner->calls);
    }
}
