# illuma-law/healthcheck-typesense

Typesense health check for Spatie's Laravel Health package. Verifies reachability and latency.

## Usage

### Basic Registration
Uses settings from `config/healthcheck-typesense.php`.

```php
use IllumaLaw\HealthCheckTypesense\TypesenseCheck;
use Spatie\Health\Facades\Health;

Health::checks([
    TypesenseCheck::new(),
]);
```

### Fluent Configuration

```php
Health::checks([
    TypesenseCheck::new()
        ->timeout(3)
        ->clientSettings([
            'api_key' => config('scout.typesense.api_key'),
            'nodes' => [
                ['host' => 'typesense.production.internal', 'port' => '8108', 'protocol' => 'http'],
            ],
        ]),
]);
```

### Injecting Client

```php
use Typesense\Client;

Health::checks([
    TypesenseCheck::new()->useClient(app(Client::class)),
]);
```

## Configuration

Publish config: `php artisan vendor:publish --tag="healthcheck-typesense-config"`

Key options in `config/healthcheck-typesense.php`:
- `timeout_seconds`: Threshold before marking as "slow" (Warning).
- `client_settings`: Array of settings for `\Typesense\Client`.
