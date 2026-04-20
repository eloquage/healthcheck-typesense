# Healthcheck Typesense

[![Tests](https://github.com/illuma-law/healthcheck-typesense/actions/workflows/run-tests.yml/badge.svg)](https://github.com/illuma-law/healthcheck-typesense/actions/workflows/run-tests.yml)
[![PHPStan](https://github.com/illuma-law/healthcheck-typesense/actions/workflows/phpstan.yml/badge.svg)](https://github.com/illuma-law/healthcheck-typesense/actions/workflows/phpstan.yml)
[![Latest Stable Version](https://img.shields.io/packagist/v/illuma-law/healthcheck-typesense)](https://packagist.org/packages/illuma-law/healthcheck-typesense)
[![License](https://img.shields.io/packagist/l/illuma-law/healthcheck-typesense)](LICENSE.md)

A [Typesense](https://typesense.org) health check for [Spatie's Laravel Health](https://spatie.be/docs/laravel-health) package.

The check pings your Typesense cluster's `/health` endpoint. If the request succeeds within the configured timeout, the check passes. If it exceeds the threshold, it warns. If the cluster is unreachable, it fails.

## Requirements

- PHP 8.3+
- Laravel 11, 12, or 13
- [spatie/laravel-health](https://github.com/spatie/laravel-health) ^1.39
- [typesense/typesense-php](https://github.com/typesense/typesense-php) ^6.0

## Installation

```bash
composer require illuma-law/healthcheck-typesense
```

Optionally publish the configuration file:

```bash
php artisan vendor:publish --tag="healthcheck-typesense-config"
```

## Configuration

The published config file (`config/healthcheck-typesense.php`) contains two sections:

```php
return [
    // Connection settings passed to the Typesense PHP client.
    'client_settings' => [
        'api_key' => env('TYPESENSE_API_KEY', ''),
        'nodes' => [
            [
                'host'     => env('TYPESENSE_HOST', 'localhost'),
                'port'     => env('TYPESENSE_PORT', '8108'),
                'protocol' => env('TYPESENSE_PROTOCOL', 'http'),
            ],
        ],
        'connection_timeout_seconds' => (int) env('TYPESENSE_CONNECTION_TIMEOUT_SECONDS', 2),
    ],

    // Slow-response threshold in seconds.
    'timeout_seconds' => 5,
];
```

All settings can be overridden at runtime via the fluent API.

## Usage

### Basic Registration

Register the check wherever you configure Spatie Health (typically in a service provider):

```php
use IllumaLaw\HealthCheckTypesense\TypesenseCheck;
use Spatie\Health\Facades\Health;

Health::checks([
    TypesenseCheck::new(),
]);
```

### Fluent API

Override config values on a per-check basis:

```php
TypesenseCheck::new()
    ->clientSettings([
        'api_key' => 'my-key',
        'nodes'   => [['host' => 'typesense.example.com', 'port' => '443', 'protocol' => 'https']],
    ])
    ->timeout(3);
```

| Method | Description |
|---|---|
| `clientSettings(array $settings)` | Override the Typesense client connection settings. |
| `timeout(int $seconds)` | Set the slow-response warning threshold. |
| `useClient(Client $client)` | Inject a pre-configured `Typesense\Client` instance (useful for testing). |

### Result Metadata

A successful check includes the following metadata:

| Key | Type | Description |
|---|---|---|
| `health` | `array` | Raw health payload from the Typesense `/health` endpoint. |
| `response_time_ms` | `int` | Round-trip time to the health endpoint in milliseconds. |

### Result Statuses

| Status | Condition |
|---|---|
| **OK** | Typesense responded within the timeout threshold. |
| **Warning** | Typesense responded, but slower than the threshold. |
| **Failed** | Typesense was unreachable or threw an error. |

## Testing

The package ships with a Pest test suite at 100% code coverage.

```bash
composer test
```

Run with coverage:

```bash
composer test-coverage
```

Run static analysis (PHPStan level 9):

```bash
composer analyse
```

Fix code style:

```bash
composer format
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Security

If you discover a security vulnerability, please send an e-mail to [support@illuma.law](mailto:support@illuma.law). All security vulnerabilities will be promptly addressed.

## Credits

- [illuma-law](https://github.com/illuma-law)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
