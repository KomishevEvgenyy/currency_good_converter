<?php

namespace Modules\Currency\Application\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Currency\Domain\CurrencyRate;

/**
 * @mixin CurrencyRate
 */
class CurrencyRateResource extends JsonResource
{
    /**
     * @return array<string, string|float>
     */
    public function toArray(Request $request): array
    {
        return [
            'currencyCode' => $this->currencyCode,
            'rate' => $this->rate,
            'exchangeDate' => $this->exchangeDate,
        ];
    }
}
