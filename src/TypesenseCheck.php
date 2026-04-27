<?php

declare(strict_types=1);

namespace IllumaLaw\HealthCheckTypesense;

use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;
use Throwable;
use Typesense\Client;
use Typesense\Exceptions\TypesenseClientError;

final class TypesenseCheck extends Check
{
    /** @var array<string, mixed>|null */
    private ?array $clientSettings = null;

    private ?int $timeoutSeconds = null;

    private ?Client $client = null;

    public function useClient(Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    /** @param array<string, mixed> $settings */
    public function clientSettings(array $settings): self
    {
        $this->clientSettings = $settings;

        return $this;
    }

    public function timeout(int $seconds): self
    {
        $this->timeoutSeconds = $seconds;

        return $this;
    }

    public function run(): Result
    {
        $configTimeout = config('healthcheck-typesense.timeout_seconds');
        $timeoutSeconds = $this->timeoutSeconds ?? (is_int($configTimeout) ? $configTimeout : 5);

        $configSettings = config('healthcheck-typesense.client_settings');
        /** @var array<string, mixed> $clientSettings */
        $clientSettings = $this->clientSettings ?? (is_array($configSettings) ? $configSettings : []);

        $started = microtime(true);

        try {
            $client = $this->resolveClient($clientSettings);

            /** @var array{ok: bool} $health */
            $health = $client->health->retrieve();
        } catch (TypesenseClientError $e) {
            return $this->failedResult($e->getMessage());
        } catch (Throwable $e) {
            return $this->failedResult($e->getMessage());
        }

        $responseTimeMs = (int) round((microtime(true) - $started) * 1000);

        $result = Result::make()
            ->meta([
                'health'           => $health,
                'response_time_ms' => $responseTimeMs,
            ])
            ->shortSummary("{$responseTimeMs}ms");

        if ($responseTimeMs > ($timeoutSeconds * 1000)) {
            $message = __('healthcheck-typesense::messages.slow', ['ms' => $responseTimeMs]);

            return $result->warning(is_string($message) ? $message : "Typesense responded slowly ({$responseTimeMs}ms)");
        }

        $okMessage = __('healthcheck-typesense::messages.ok');

        return $result->ok(is_string($okMessage) ? $okMessage : 'Typesense is healthy');
    }

    private function resolveClient(mixed $clientSettings): Client
    {
        if ($this->client instanceof Client) {
            return $this->client;
        }

        /** @var array<string, mixed> $settings */
        $settings = is_array($clientSettings) ? $clientSettings : [];

        return new Client($settings);
    }

    private function failedResult(string $errorMessage): Result
    {
        $message = __('healthcheck-typesense::messages.unreachable', ['message' => $errorMessage]);

        return Result::make()
            ->failed(is_string($message) ? $message : 'Typesense request failed')
            ->shortSummary('Failed');
    }
}
