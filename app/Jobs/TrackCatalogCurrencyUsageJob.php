<?php

namespace App\Jobs;

use App\Models\CatalogCurrencyUsage;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;

class TrackCatalogCurrencyUsageJob implements ShouldQueue
{
    use Dispatchable;
    use Queueable;

    public function __construct(
        public string $currencyCode,
        public Carbon $requestedAt,
    ) {}

    public function handle(): void
    {
        CatalogCurrencyUsage::query()->create([
            'currency_code' => $this->currencyCode,
            'requested_at' => $this->requestedAt,
        ]);
    }
}
