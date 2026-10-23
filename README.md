# Healthcheck Typesense for Laravel

![Healthcheck Typesense banner](art/banner.png)

[![Tests](https://github.com/eloquage/healthcheck-typesense/actions/workflows/run-tests.yml/badge.svg)](https://github.com/eloquage/healthcheck-typesense/actions)
[![Packagist License](https://img.shields.io/badge/Licence-MIT-blue)](http://choosealicense.com/licenses/mit/)
[![Latest Stable Version](https://img.shields.io/packagist/v/illuma-law/healthcheck-typesense?label=Version)](https://packagist.org/packages/illuma-law/healthcheck-typesense)

A Typesense health check for Spatie's [Laravel Health](https://spatie.be/docs/laravel-health/v1/introduction) package.

This package provides a direct and reliable way to ensure your Typesense search cluster is reachable and responding quickly. It checks the built-in `/health` API of Typesense and alerts you if the service goes down or experiences severe latency.

## Features

- **Reachability Check:** Verifies that your Laravel application can successfully connect to your Typesense cluster.
- **Latency Monitoring:** Measures the exact response time of the Typesense cluster. If the response time exceeds a specified timeout threshold, the check will degrade to a Warning state.
- **Flexible Configuration:** Connect using config values, passing arrays fluently, or even injecting an already instantiated `Typesense\Client`.

## Installation

Require this package with composer:

```shell
composer require illuma-law/healthcheck-typesense
```

## Configuration

You can publish the config file to define the connection settings across your application:

```shell
php artisan vendor:publish --tag="healthcheck-typesense-config"
```

The resulting `config/healthcheck-typesense.php` will look like this:

```php
return [
    // Threshold in seconds before a successful request is considered 'slow' (Warning)
    'timeout_seconds' => 5,

    // Array of settings passed to the \Typesense\Client
    'client_settings' => [
        'api_key' => env('TYPESENSE_API_KEY', ''),
        'nodes' => [
            [
                'host' => env('TYPESENSE_HOST', 'localhost'),
                'port' => env('TYPESENSE_PORT', '8108'),
                'path' => env('TYPESENSE_PATH', ''),
                'protocol' => env('TYPESENSE_PROTOCOL', 'http'),
            ],
        ],
        'connection_timeout_seconds' => 2,
    ],
];
```

## Usage & Integration

Add the check inside your `AppServiceProvider` or your dedicated `HealthServiceProvider`.

### Basic Registration

If you have configured the connection in the published configuration file, you can register the check cleanly without passing any arguments:

```php
use IllumaLaw\HealthCheckTypesense\TypesenseCheck;
use Spatie\Health\Facades\Health;

Health::checks([
    TypesenseCheck::new(),
]);
```

### Fluent Configuration

You can override default settings dynamically. This is useful if you are fetching connection settings from a different configuration file (like Scout) or testing.

```php
use IllumaLaw\HealthCheckTypesense\TypesenseCheck;
use Spatie\Health\Facades\Health;

Health::checks([
    TypesenseCheck::new()
        ->timeout(3) // Issue a warning if the ping takes > 3 seconds
        ->clientSettings([
            'api_key' => config('scout.typesense.api_key'),
            'nodes' => [
                [
                    'host' => 'typesense.production.internal',
                    'port' => '8108',
                    'protocol' => 'http',
                ],
            ],
            'connection_timeout_seconds' => 2,
        ]),
]);
```

### Injecting the Client

If you already bind the `Typesense\Client` in your Laravel container, you can pass it directly to avoid re-initializing it:

```php
use IllumaLaw\HealthCheckTypesense\TypesenseCheck;
use Spatie\Health\Facades\Health;
use Typesense\Client;

Health::checks([
    TypesenseCheck::new()
        ->useClient(app(Client::class)),
]);
```

### Expected Result States

The check communicates with the Spatie Health dashboard using these states:

- **OK:** Typesense responded successfully within the defined timeout threshold. The short summary shows the response time (e.g., `12ms`).
- **Warning:** Typesense responded successfully, but the response time was higher than the configured `timeout_seconds`.
- **Failed:** Typesense was unreachable, the connection timed out entirely, or an authentication error occurred.

## Testing

Run the test suite:

```shell
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
