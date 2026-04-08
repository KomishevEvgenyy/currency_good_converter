<?php

namespace Modules\Currency\Application\Http\Requests;

use Modules\Currency\Enum\CurrencyTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExchangeRateRequest extends FormRequest
{
    /**
     * @return array<string, array<int, \Illuminate\Contracts\Validation\ValidationRule|\Illuminate\Validation\Rules\In|string>>
     */
    public function rules(): array
    {
        return [
            'currency' => ['required', 'string', Rule::in(CurrencyTypeEnum::cases())],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function validationData(): array
    {
        return array_merge(parent::validationData(), [
            'currency' => strtolower($this->route('currency')),
        ]);
    }
}
