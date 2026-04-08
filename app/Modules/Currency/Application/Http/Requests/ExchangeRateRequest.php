<?php

namespace Modules\Currency\Application\Http\Requests;

use Modules\Currency\Enum\CurrencyTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExchangeRateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'currency' => ['required', 'string', Rule::in(CurrencyTypeEnum::cases())],
        ];
    }

    public function validationData(): array
    {
        return array_merge(parent::validationData(), [
            'currency' => strtolower($this->route('currency')),
        ]);
    }
}
