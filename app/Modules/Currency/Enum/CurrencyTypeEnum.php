<?php

namespace Modules\Currency\Enum;

enum CurrencyTypeEnum: string
{
    case USD = 'usd';
    case EUR = 'eur';
    case UAH = 'uah';

    public function upper(): string
    {
        return strtoupper($this->value);
    }
}
