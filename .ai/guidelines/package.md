---
description: Typesense cluster health check for Spatie Laravel Health — reachability and latency monitoring
---

# healthcheck-typesense

Typesense search cluster health check for `spatie/laravel-health`. Probes the Typesense `/health` API endpoint.

## Namespace

`IllumaLaw\HealthCheckTypesense`

## Key Check

- `TypesenseCheck` — HTTP check against Typesense `/health`; measures response latency; warns if slow

## Registration

```php
use IllumaLaw\HealthCheckTypesense\TypesenseCheck;
use Spatie\Health\Facades\Health;

Health::checks([
    TypesenseCheck::new(),
    // Or with explicit config:
    TypesenseCheck::new()->connectUsing([
        'nodes' => [['host' => 'typesense', 'port' => 8108, 'protocol' => 'http']],
        'api_key' => env('TYPESENSE_API_KEY'),
    ]),
]);
```

## Config

Publish: `php artisan vendor:publish --tag="healthcheck-typesense-config"`

Reads from `config/healthcheck-typesense.php` by default (uses the same connection settings as Laravel Scout's Typesense driver).

## Notes

- Flexible configuration: use config values, fluent array, or inject a pre-built `Typesense\Client`.
- Returns `warning` if response time exceeds the configured timeout threshold.
- Returns `failed` if the cluster is unreachable.
- This project uses Typesense as the Scout search engine on the `scout` Redis queue.
