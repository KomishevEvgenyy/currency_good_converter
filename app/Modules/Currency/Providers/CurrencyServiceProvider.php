<?php

namespace Modules\Currency\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Currency\Domain\CurrencyExchangeService;
use Modules\Currency\Domain\Contracts\CurrencyRateReader;
use Modules\Currency\Application\Facades\CurrencyExchangeFacade;
use Modules\Currency\Infrastructure\CachedCurrencyRateRepository;
use Modules\Currency\Infrastructure\CurrencyRateReaderResolver;
use Modules\Currency\Infrastructure\FallbackCurrencyRateRepository;
use Psr\Log\LoggerInterface;

class CurrencyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CurrencyRateReaderResolver::class, function ($app) {
            return new CurrencyRateReaderResolver($app);
        });

        $this->app->singleton(CurrencyRateReader::class, function ($app) {
            $resolver = $app->make(CurrencyRateReaderResolver::class);
            $primaryName = (string) config('currency.primary');
            $fallbackName = (string) config('currency.fallback');

            $fallbackReader = new FallbackCurrencyRateRepository(
                primary: $resolver->resolve($primaryName),
                fallback: $resolver->resolve($fallbackName),
                logger: $app->make(LoggerInterface::class),
            );

            return new CachedCurrencyRateRepository(
                inner: $fallbackReader,
                providerSignature: "{$primaryName}|{$fallbackName}",
                ttlSeconds: (int) config('currency.cache.ttl_seconds', 600),
                cacheKeyPrefix: (string) config('currency.cache.key_prefix', 'currency:rates'),
            );
        });

        $this->app->singleton(CurrencyExchangeService::class, function ($app) {
            return new CurrencyExchangeService(
                $app->make(CurrencyRateReader::class),
            );
        });

        $this->app->singleton(CurrencyExchangeFacade::class, function ($app) {
            return new CurrencyExchangeFacade(
                $app->make(CurrencyExchangeService::class),
            );
        });
    }
}
