<?php

namespace App\Http\Controllers;

use App\Http\Requests\CatalogRequest;
use App\Http\Resource\CatalogResource;
use App\Jobs\TrackCatalogCurrencyUsageJob;
use App\Services\CatalogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CatalogController extends Controller
{
    public function __construct(
        private readonly CatalogService $catalogService,
    ) {}

    public function __invoke(CatalogRequest $request): AnonymousResourceCollection|JsonResponse
    {
        $currencyCode = strtolower((string) $request->route('currency'));

        TrackCatalogCurrencyUsageJob::dispatch(
            strtoupper($currencyCode),
            now(),
        );

        $products = $this->catalogService->getProducts($currencyCode);

        return CatalogResource::collection($products);
    }
}
