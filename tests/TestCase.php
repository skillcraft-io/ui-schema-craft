<?php

namespace Skillcraft\UiSchemaCraft\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Skillcraft\UiSchemaCraft\Providers\UiSchemaCraftServiceProvider;
use Skillcraft\SchemaValidation\Providers\SchemaValidationServiceProvider;
use Skillcraft\SchemaState\Providers\SchemaStateServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app): array
    {
        return [
            UiSchemaCraftServiceProvider::class,
            SchemaStateServiceProvider::class,
            SchemaValidationServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'PropertyBuilder' => 'Skillcraft\UiSchemaCraft\Facades\PropertyBuilder',
        ];
    }
}
