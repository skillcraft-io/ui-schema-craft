<?php

namespace Skillcraft\UiSchemaCraft\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Skillcraft\UiSchemaCraft\Providers\UiSchemaCraftServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            UiSchemaCraftServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'PropertyBuilder' => 'Skillcraft\UiSchemaCraft\Facades\PropertyBuilder',
        ];
    }
}
