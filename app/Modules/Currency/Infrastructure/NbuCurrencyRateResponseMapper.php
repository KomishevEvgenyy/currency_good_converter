<?php

namespace Modules\Currency\Infrastructure;

use Modules\Currency\Domain\CurrencyRate;

final class NbuCurrencyRateResponseMapper
{
    /**
     * @return CurrencyRate[]
     */
    public function map(mixed $payload): array
    {
        if (! is_array($payload)) {
            throw new \RuntimeException('NBU API returned invalid payload: expected top-level array');
        }

        return array_map(
            function (mixed $item, int|string $index): CurrencyRate {
                if (! is_array($item)) {
                    throw new \RuntimeException("NBU API returned invalid rate row at index [{$index}]: expected object array");
                }

                foreach (['txt', 'rate', 'cc', 'exchangedate'] as $requiredKey) {
                    if (! array_key_exists($requiredKey, $item)) {
                        throw new \RuntimeException(
                            "NBU API returned invalid rate row at index [{$index}]: missing key [{$requiredKey}]"
                        );
                    }
                }

                if (! is_string($item['txt']) || trim($item['txt']) === '') {
                    throw new \RuntimeException(
                        "NBU API returned invalid rate row at index [{$index}]: [txt] must be a non-empty string"
                    );
                }

                if (! is_numeric($item['rate'])) {
                    throw new \RuntimeException(
                        "NBU API returned invalid rate row at index [{$index}]: [rate] must be numeric"
                    );
                }

                if (! is_string($item['cc']) || trim($item['cc']) === '') {
                    throw new \RuntimeException(
                        "NBU API returned invalid rate row at index [{$index}]: [cc] must be a non-empty string"
                    );
                }

                if (! is_string($item['exchangedate']) || trim($item['exchangedate']) === '') {
                    throw new \RuntimeException(
                        "NBU API returned invalid rate row at index [{$index}]: [exchangedate] must be a non-empty string"
                    );
                }

                return new CurrencyRate(
                    name: $item['txt'],
                    rate: (float) $item['rate'],
                    currencyCode: $item['cc'],
                    exchangeDate: $item['exchangedate'],
                );
            },
            $payload,
            array_keys($payload),
        );
    }
}
