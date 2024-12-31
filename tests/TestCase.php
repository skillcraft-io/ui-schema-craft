<?php

namespace Skillcraft\UiSchemaCraft\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Skillcraft\UiSchemaCraft\Providers\UiComponentServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            UiComponentServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'PropertyBuilder' => 'Skillcraft\UiSchemaCraft\Facades\PropertyBuilder',
        ];
    }
}
