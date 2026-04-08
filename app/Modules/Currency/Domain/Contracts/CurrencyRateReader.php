<?php

namespace Modules\Currency\Domain\Contracts;

use Modules\Currency\Domain\CurrencyRate;

interface CurrencyRateReader
{
    /**
     * @return CurrencyRate[]
     */
    public function getAll(): array;
}
