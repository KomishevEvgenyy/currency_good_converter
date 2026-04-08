# Smartphone Catalog API

A Laravel 12 backend application that serves a smartphone product catalog with real-time currency conversion (USD to UAH) using the National Bank of Ukraine (NBU) exchange rates.

## Features

- Browse smartphone catalog sourced from [DummyJSON](https://dummyjson.com/)
- View prices in USD (original) or UAH (converted via NBU rates)
- Retrieve the current USD/UAH exchange rate from the NBU API
- Modular domain-driven architecture for the Currency module

## Tech Stack

- **PHP** ^8.2
- **Laravel** 12
- **PHPUnit** 11 for testing
- **Docker** via Laravel Sail (PHP 8.5, MySQL 8.4, Redis, Memcached)

## API Endpoints

### `GET /api/catalog/{currency}`

Returns a list of smartphones with prices in the requested currency.

| Parameter  | Type   | Allowed Values | Description                          |
|------------|--------|----------------|--------------------------------------|
| `currency` | string | `usd`, `uah`   | Currency for product prices          |

**Response (200):**

```json
{
  "data": [
    {
      "id": 1,
      "title": "iPhone 5s",
      "price": 199.99,
      "rating": 2.83,
      "thumbnail": "https://cdn.dummyjson.com/..."
    }
  ]
}
```

Unsupported currencies return **404**.

### `GET /api/exchangeRate/USD`

Returns the current USD to UAH exchange rate from the NBU.

**Response (200):**

```json
{
  "data": {
    "currencyCode": "USD",
    "rate": 43.0996,
    "exchangeDate": "02.03.2026"
  }
}
```

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── CatalogController.php       # Catalog API; dispatches usage job; delegates to CatalogService
│   │   └── Controller.php
│   └── Requests/
│       └── CatalogRequest.php          # Validates catalog currency (usd, uah, eur)
├── Jobs/
│   └── TrackCatalogCurrencyUsageJob.php   # Queued persistence of catalog currency usage
├── Models/
│   ├── CatalogCurrencyUsage.php        # Usage tracking row (currency_code, requested_at)
│   └── User.php
├── Modules/
│   └── Currency/
│       ├── Application/
│       │   ├── Facades/
│       │   │   └── CurrencyExchangeFacade.php   # Application entry for rates & catalog pricing
│       │   └── Http/
│       │       ├── Controllers/
│       │       │   └── ExchangeRateController.php
│       │       └── Requests/
│       │           └── ExchangeRateRequest.php
│       ├── Domain/
│       │   ├── Contracts/
│       │   │   └── CurrencyRateReader.php       # Port for loading rate snapshots
│       │   ├── ConvertedPrice.php
│       │   ├── CurrencyExchangeService.php      # Rate lookup & conversion (UAH baseline)
│       │   └── CurrencyRate.php
│       ├── Enum/
│       │   └── CurrencyTypeEnum.php
│       ├── Infrastructure/
│       │   ├── CachedCurrencyRateRepository.php      # Read-through cache (day-scoped key + TTL)
│       │   ├── CurrencyRateReaderResolver.php        # Resolves config provider name → class
│       │   ├── ErApiCurrencyRepository.php         # open.er-api.com (fallback / alternate primary)
│       │   ├── FallbackCurrencyRateRepository.php  # Primary reader with fallback on failure
│       │   └── NbuApiCurrencyRepository.php        # NBU exchange API
│       └── Providers/
│           └── CurrencyServiceProvider.php         # Binds reader chain, service, facade
├── Providers/
│   └── AppServiceProvider.php
└── Services/
    └── CatalogService.php              # DummyJSON fetch + price mapping via CurrencyExchangeFacade

config/
├── catalog.php                         # Catalog external URLs (e.g. DummyJSON)
└── currency.php                        # primary / fallback provider keys, cache TTL, provider registry

database/
└── migrations/                         # users, cache, jobs, catalog_currency_usages, …

routes/
└── api.php                             # GET /api/catalog/{currency}, GET /api/exchangeRate/{currency}

tests/
├── Feature/                            # API tests, cache/fallback/config integration
├── Unit/                               # CurrencyExchangeService, cache & fallback decorators
└── TestCase.php
```

## Getting Started

### Prerequisites

- PHP 8.2+ (or Docker)
- Composer
- Node.js & npm

### Quick Setup

## Requirements

- Docker
- Docker Compose

## Setup

```bash
cp .env.example .env
```
Add credentials to `.env` file.

```bash
make boot
```

This command will:

- start the containers;
- create `.env` if it does not exist;
- install PHP dependencies;
- generate the application key;
- run database migrations;
- install frontend dependencies;
- build frontend assets;
- start the queue worker.

if you get a problem with mysql docker image (docker/mysql/create-testing-database.sh - permission denied), use this command:
```bash
chmod +x ./docker/mysql/create-testing-database.sh
```

## Available commands

### Start containers only
```bash
make up
```

### Stop containers
```bash
make down
```

### Build containers
```bash
make build
```

### Open a shell inside the app container
```bash
make shell
```

### Run Artisan commands
```bash
make artisan migrate
```

### Run tests
```bash
make artisan test
```


## API

### Catalog endpoint
http GET /api/catalog/{currency}

Example:
```bash
curl --location 'http://localhost/api/catalog/eur' \
--header 'Accept: application/json'
```
### Exchange endpoint
http GET /api/exchangeRate/{currency}

Example:
```bash
curl --location 'http://localhost/api/exchangeRate/USD' \
--header 'Accept: application/json'
```
## Notes
- The project uses Docker-based development environment.
- Queue worker is started separately by the `boot` command.

### Using Docker (Laravel Sail)

```bash
cp .env.example .env

# Start the containers
./vendor/bin/sail up -d

# Install dependencies and set up the application
./vendor/bin/sail composer setup
```

The application will be available at `http://localhost`.

### Local Development

```bash
composer dev
```

This starts the Laravel dev server, queue worker, log watcher (Pail), and Vite concurrently.

## Running Tests

```bash
make test
```

Tests use `Http::fake()` to mock external API calls (DummyJSON and NBU), so no network access is needed.

### Test Coverage

- **Catalog endpoint** — USD/UAH responses, price conversion, currency validation, HTTP method restrictions
- **Exchange rate endpoint** — JSON structure, rate values, HTTP method restrictions
- Both endpoints verified to not require authentication

## External APIs

| API | Purpose | URL |
|-----|---------|-----|
| DummyJSON | Smartphone product data | `https://dummyjson.com/products/category/smartphones` |
| NBU | USD/UAH exchange rate | `https://bank.gov.ua/NBUStatService/v1/statdirectory/exchange` |
