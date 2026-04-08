# Backend Developer — Technical Assignment

Smartphone Catalog API (Laravel 12, modular/DDD architecture).

## What to do

1. **Add EUR support** — `GET /api/catalog/EUR` returns products with EUR prices, `GET /api/exchangeRate/EUR` returns the EUR → UAH rate.
2. **Add a second exchange-rate provider as fallback** — integrate `https://open.er-api.com/v6/latest/UAH` alongside the existing NBU provider. If the main provider fails or times out, the fallback is used.
3. **Add `config/currency.php`** — create the ability to swap exchange-rate providers from config, no code changes needed.
4. **Cache exchange rates** — 10-minute TTL, but always fetch fresh on a new calendar day.
5. **Track catalog currency usage** — on every catalog request, dispatch a queued job that saves the currency and timestamp to the database.
6. **Write tests** — unit + feature tests for new functionality; all existing tests must still pass.
7. **Open a PR** — feature branch, clean commits, PR against `main` with summary and test plan.

## What We Value

- **DDD & modular design** — proper boundaries between modules, meaningful domain abstractions.
- **OOP design patterns** — Strategy, Decorator, Factory, or any pattern that fits naturally (don't force them).
- **SOLID principles** — single responsibility, dependency inversion, open/closed — applied where it matters.
- **Test coverage** — unit and feature tests for key paths, including fallback and edge cases.

You are free to use any AI agents or tools to complete this assignment. What matters is the final result and your ability to explain it.

Show your maximum architecture skills. Good luck!

## Future Plans (Out of Scope)

*Not part of the current assignment. Listed for context on where the project is heading.*

- **More currencies** — support additional currencies beyond USD and EUR.
- **Price history** — track product price changes over time, including historical max/min prices.
- **Filters and sorting** — filtering (by brand, price range) and sorting (by price, name) for the catalog.