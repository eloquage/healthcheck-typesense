<?php

declare(strict_types=1);

use IllumaLaw\HealthCheckTypesense\HealthCheckTypesenseServiceProvider;
use Illuminate\Support\ServiceProvider;

it('registers the config publish path under the correct tag', function () {
    $provider = app()->getProvider(HealthCheckTypesenseServiceProvider::class);
    if (! ($provider instanceof HealthCheckTypesenseServiceProvider)) {
        throw new RuntimeException('The package service provider must be registered.');
    }

    $source = dirname(__DIR__).'/src/../config/healthcheck-typesense.php';

    expect(ServiceProvider::pathsToPublish($provider::class, 'healthcheck-typesense-config'))
        ->toBe([$source => config_path('healthcheck-typesense.php')]);
});

it('loads config with the correct default', function () {
    expect(config('healthcheck-typesense.timeout_seconds'))->toBeInt();
});
