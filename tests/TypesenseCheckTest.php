<?php

declare(strict_types=1);

use IllumaLaw\HealthCheckTypesense\TypesenseCheck;
use Mockery\MockInterface;
use Spatie\Health\Enums\Status;
use Typesense\Client;
use Typesense\Exceptions\TypesenseClientError;
use Typesense\Health;

function mockTypesenseClient(?Closure $healthReturn = null, ?Throwable $healthThrow = null): Client
{
    /** @var Health&MockInterface $health */
    $health = Mockery::mock(Health::class);

    if ($healthThrow) {
        $health->shouldReceive('retrieve')->andThrow($healthThrow);
    } else {
        $health->shouldReceive('retrieve')->andReturnUsing($healthReturn ?? fn (): array => ['ok' => true]);
    }

    /** @var Client&MockInterface $client */
    $client = Mockery::mock(Client::class);
    $client->health = $health;

    return $client;
}

it('returns ok when typesense is healthy', function () {
    $client = mockTypesenseClient();

    $result = TypesenseCheck::new()
        ->useClient($client)
        ->run();

    expect($result->status)->toEqual(Status::ok())
        ->and($result->meta)->toHaveKeys(['health', 'response_time_ms'])
        ->and($result->shortSummary)->toMatch('/^\d+ms$/');
});

it('fails when typesense throws a generic exception', function () {
    $client = mockTypesenseClient(healthThrow: new RuntimeException('Connection refused'));

    $result = TypesenseCheck::new()
        ->useClient($client)
        ->run();

    expect($result->status)->toEqual(Status::failed())
        ->and($result->notificationMessage)->toContain('Connection refused')
        ->and($result->shortSummary)->toBe('Failed');
});

it('fails when typesense throws a TypesenseClientError', function () {
    $client = mockTypesenseClient(healthThrow: new TypesenseClientError('Client error'));

    $result = TypesenseCheck::new()
        ->useClient($client)
        ->run();

    expect($result->status)->toEqual(Status::failed())
        ->and($result->notificationMessage)->toContain('Client error')
        ->and($result->shortSummary)->toBe('Failed');
});

it('warns when response time exceeds timeout threshold', function () {
    $client = mockTypesenseClient(healthReturn: function () {
        usleep(100_000);

        return ['ok' => true];
    });

    $result = TypesenseCheck::new()
        ->useClient($client)
        ->timeout(0)
        ->run();

    expect($result->status)->toEqual(Status::warning())
        ->and($result->notificationMessage)->toContain('slowly');
});

it('uses config timeout when none is set via fluent api', function () {
    config()->set('healthcheck-typesense.timeout_seconds', 60);

    $client = mockTypesenseClient();

    $result = TypesenseCheck::new()
        ->useClient($client)
        ->run();

    expect($result->status)->toEqual(Status::ok());
});

it('falls back to default timeout when config value is not an integer', function () {
    config()->set('healthcheck-typesense.timeout_seconds', 'invalid');

    $client = mockTypesenseClient();

    $result = TypesenseCheck::new()
        ->useClient($client)
        ->run();

    expect($result->status)->toEqual(Status::ok());
});

it('uses config client_settings when none provided via fluent api', function () {
    config()->set('healthcheck-typesense.client_settings', [
        'api_key' => 'test-key',
        'nodes'   => [['host' => 'localhost', 'port' => '8108', 'protocol' => 'http']],
    ]);

    $client = mockTypesenseClient();

    $result = TypesenseCheck::new()
        ->useClient($client)
        ->run();

    expect($result->status)->toEqual(Status::ok());
});

it('falls back to empty array when config client_settings is not an array', function () {
    config()->set('healthcheck-typesense.client_settings', 'invalid');

    $client = mockTypesenseClient();

    $result = TypesenseCheck::new()
        ->useClient($client)
        ->run();

    expect($result->status)->toEqual(Status::ok());
});

it('accepts client settings via fluent method', function () {
    $client = mockTypesenseClient();

    $result = TypesenseCheck::new()
        ->useClient($client)
        ->clientSettings(['api_key' => 'test', 'nodes' => []])
        ->timeout(10)
        ->run();

    expect($result->status)->toEqual(Status::ok());
});

it('creates a client from settings when no client is injected', function () {
    config()->set('healthcheck-typesense.client_settings', [
        'api_key'                    => 'xyz',
        'nodes'                      => [['host' => '127.0.0.1', 'port' => '19999', 'protocol' => 'http']],
        'connection_timeout_seconds' => 1,
    ]);

    $result = TypesenseCheck::new()->run();

    expect($result->status)->toEqual(Status::failed());
});

it('can connect to a real typesense instance', function () {
    $host = env('TYPESENSE_HOST');
    $port = env('TYPESENSE_PORT');
    $protocol = env('TYPESENSE_PROTOCOL', 'http');
    $apiKey = env('TYPESENSE_API_KEY');

    if (! $host || ! $port || ! $apiKey) {
        $this->markTestSkipped('Typesense environment variables not set.');
    }

    $result = TypesenseCheck::new()
        ->clientSettings([
            'api_key' => $apiKey,
            'nodes'   => [
                [
                    'host'     => $host,
                    'port'     => (string) $port,
                    'protocol' => $protocol,
                ],
            ],
            'connection_timeout_seconds' => 2,
        ])
        ->run();

    expect($result->status)->toEqual(Status::ok());
})->group('integration');
