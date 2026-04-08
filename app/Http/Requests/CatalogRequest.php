<?php

namespace App\Http\Requests;

use Modules\Currency\Enum\CurrencyTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CatalogRequest extends FormRequest
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
