<?php

namespace Modules\Currency\Application\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Currency\Application\Http\Resources\CurrencyRateResource;
use Illuminate\Http\JsonResponse;
use Modules\Currency\Application\Facades\CurrencyExchangeFacade;
use Modules\Currency\Application\Http\Requests\ExchangeRateRequest;

class ExchangeRateController extends Controller
{
    public function __invoke(ExchangeRateRequest $request): JsonResponse|CurrencyRateResource
    {
        $code = (string) $request->route('currency');
        $rate = app(CurrencyExchangeFacade::class)->getRateByCode($code);

        return new CurrencyRateResource($rate);
    }
}
