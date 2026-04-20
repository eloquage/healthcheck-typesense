<?php

declare(strict_types=1);

arch('it will not use debugging functions')
    ->expect(['dd', 'dump', 'ray', 'var_dump'])
    ->each->not->toBeUsed();

arch('it will use strict types')
    ->expect('IllumaLaw\HealthCheckTypesense')
    ->toUseStrictTypes();

arch('it will use final classes')
    ->expect('IllumaLaw\HealthCheckTypesense')
    ->toBeFinal();

arch('the check extends spatie check')
    ->expect('IllumaLaw\HealthCheckTypesense\TypesenseCheck')
    ->toExtend('Spatie\Health\Checks\Check');

arch('the service provider extends package service provider')
    ->expect('IllumaLaw\HealthCheckTypesense\HealthCheckTypesenseServiceProvider')
    ->toExtend('Spatie\LaravelPackageTools\PackageServiceProvider');
