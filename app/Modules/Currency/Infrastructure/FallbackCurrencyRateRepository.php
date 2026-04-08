<?php

namespace Modules\Currency\Infrastructure;

use Modules\Currency\Domain\Contracts\CurrencyRateReader;
use Psr\Log\LoggerInterface;

readonly class FallbackCurrencyRateRepository implements CurrencyRateReader
{
    public function __construct(
        private CurrencyRateReader $primary,
        private CurrencyRateReader $fallback,
        private LoggerInterface    $logger,
    ) {}

    public function getAll(): array
    {
        try {
            return $this->primary->getAll();
        } catch (\Throwable $exception) {
            $this->logger->warning('Primary currency provider failed, using fallback provider.', [
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
            ]);

            return $this->fallback->getAll();
        }
    }
}
