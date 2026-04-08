<?php

namespace Modules\Currency\Application\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Currency\Application\Facades\CurrencyExchangeFacade;
use Modules\Currency\Application\Http\Requests\ExchangeRateRequest;
use Modules\Currency\Application\Http\Resources\CurrencyRateResource;

class ExchangeRateController extends Controller
{
    public function __construct(
        private readonly CurrencyExchangeFacade $currencyExchange,
    ) {}

    public function __invoke(ExchangeRateRequest $request): JsonResponse|CurrencyRateResource
    {
        $code = (string) $request->route('currency');
        $rate = $this->currencyExchange->getRateByCode($code);

        return new CurrencyRateResource($rate);
    }
}
