# Healthcheck Typesense

[![Tests](https://github.com/illuma-law/healthcheck-typesense/actions/workflows/run-tests.yml/badge.svg)](https://github.com/illuma-law/healthcheck-typesense/actions)
[![Packagist License](https://img.shields.io/badge/Licence-MIT-blue)](http://choosealicense.com/licenses/mit/)
[![Latest Stable Version](https://img.shields.io/packagist/v/illuma-law/healthcheck-typesense?label=Version)](https://packagist.org/packages/illuma-law/healthcheck-typesense)

**A Typesense health check for Spatie's Laravel Health package**

This package provides a health check that verifies whether your Typesense cluster is reachable and healthy.

- [Installation](#installation)
- [Usage](#usage)
  - [Basic Registration](#basic-registration)
  - [Fluent Configuration](#fluent-configuration)
  - [Result States](#result-states)
- [Testing](#testing)
- [Credits](#credits)
- [License](#license)

## Installation

You can install the package via composer:

```bash
composer require illuma-law/healthcheck-typesense
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="healthcheck-typesense-config"
```

## Usage

### TL;DR

```php
use IllumaLaw\HealthCheckTypesense\TypesenseCheck;
use Spatie\Health\Facades\Health;

Health::checks([
    TypesenseCheck::new(),
]);
```

### Basic Registration

Register the check inside your application's health service provider or wherever you configure [Spatie Laravel Health](https://github.com/spatie/laravel-health):

```php
use IllumaLaw\HealthCheckTypesense\TypesenseCheck;
use Spatie\Health\Facades\Health;

Health::checks([
    TypesenseCheck::new(),
]);
```

### Fluent Configuration

Override default settings using the fluent API:

```php
use IllumaLaw\HealthCheckTypesense\TypesenseCheck;

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
| `timeout(int $seconds)` | Set the slow-response warning threshold (default: 5s). |
| `useClient(Client $client)` | Inject a pre-configured `Typesense\Client` instance. |

### Result States

| Status | Condition |
|---|---|
| **OK** | Typesense responded within the timeout threshold. |
| **Warning** | Typesense responded, but slower than the threshold. |
| **Failed** | Typesense was unreachable or threw an error. |

## Testing

The package includes a comprehensive test suite using Pest.

```bash
composer test
```

## Credits

- [illuma-law](https://github.com/illuma-law)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
