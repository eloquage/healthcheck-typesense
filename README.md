# Healthcheck Typesense for Laravel

![Healthcheck Typesense banner](art/banner.png)

A [Spatie Laravel Health](https://spatie.be/docs/laravel-health) check that
calls Typesense’s `/health` endpoint and reports reachability and response time.

[![Tests](https://github.com/eloquage/healthcheck-typesense/actions/workflows/run-tests.yml/badge.svg)](https://github.com/eloquage/healthcheck-typesense/actions/workflows/run-tests.yml)
[![Latest Version](https://img.shields.io/packagist/v/illuma-law/healthcheck-typesense)](https://packagist.org/packages/illuma-law/healthcheck-typesense)
[![License](https://img.shields.io/packagist/l/illuma-law/healthcheck-typesense)](LICENSE.md)

## Requirements

- PHP 8.3 or later
- Laravel 11, 12, or 13
- Spatie Laravel Health 1.39 or later

## Install and configure

```bash
composer require illuma-law/healthcheck-typesense
php artisan vendor:publish --tag=healthcheck-typesense-config
```

Set the client connection values in `.env`:

```dotenv
TYPESENSE_API_KEY=your-api-key
TYPESENSE_HOST=localhost
TYPESENSE_PORT=8108
TYPESENSE_PROTOCOL=http
TYPESENSE_CONNECTION_TIMEOUT_SECONDS=2
```

The default slow-response warning threshold is five seconds. Change
`timeout_seconds` in `config/healthcheck-typesense.php` to adjust it.

## Register the check

```php
use IllumaLaw\HealthCheckTypesense\TypesenseCheck;
use Spatie\Health\Facades\Health;

Health::checks([
    TypesenseCheck::new(),
]);
```

The check returns OK for a timely health response, Warning when it exceeds the
threshold, and Failed when the request cannot reach Typesense.

## Override settings

Pass client settings when they come from another config source, such as Scout:

```php
TypesenseCheck::new()
    ->timeout(3)
    ->clientSettings([
        'api_key' => config('scout.typesense.api_key'),
        'nodes' => [[
            'host' => config('scout.typesense.host'),
            'port' => '8108',
            'protocol' => 'http',
        ]],
        'connection_timeout_seconds' => 2,
    ]);
```

If your application already has a `Typesense\Client`, pass it with
`TypesenseCheck::new()->useClient($client)`.

## Test

```bash
composer test
composer analyse
vendor/bin/pint --test
```

MIT licensed. See [LICENSE.md](LICENSE.md).
