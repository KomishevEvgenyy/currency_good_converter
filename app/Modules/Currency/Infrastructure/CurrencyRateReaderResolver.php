<?php

namespace Modules\Currency\Infrastructure;

use Illuminate\Contracts\Foundation\Application;
use InvalidArgumentException;
use Modules\Currency\Domain\Contracts\CurrencyRateReader;

final readonly class CurrencyRateReaderResolver
{
    public function __construct(
        private Application $app,
    ) {}

    public function resolve(string $name): CurrencyRateReader
    {
        /** @var array<string, array{class: string}> $providers */
        $providers = config('currency.providers', []);
        $definition = $providers[$name] ?? null;

        if (! is_array($definition) || ! isset($definition['class'])) {
            throw new InvalidArgumentException(
                "Currency provider [{$name}] is not defined in config/currency.php."
            );
        }

        $class = $definition['class'];

        if (! is_string($class) || ! is_subclass_of($class, CurrencyRateReader::class)) {
            throw new InvalidArgumentException(
                "Currency provider [{$name}] must resolve to a class implementing CurrencyRateReader."
            );
        }

        return $this->app->make($class);
    }
}
