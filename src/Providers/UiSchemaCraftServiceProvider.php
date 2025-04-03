<?php

namespace Skillcraft\UiSchemaCraft\Providers;

use Illuminate\Support\ServiceProvider;
use Skillcraft\UiSchemaCraft\Services\UiSchemaCraftService;
use Skillcraft\UiSchemaCraft\ComponentResolver;
use Skillcraft\UiSchemaCraft\Facades\UiSchema;
use Skillcraft\UiSchemaCraft\Schema\PropertyBuilder;
use Skillcraft\SchemaState\Contracts\StateManagerInterface;
use Skillcraft\SchemaValidation\Contracts\ValidatorInterface;

class UiSchemaCraftServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register component resolver
        $this->app->singleton(ComponentResolver::class);

        // Register property builder
        $this->app->singleton('property-builder', function ($app) {
            return new PropertyBuilder();
        });

        // Register property builder facade
        $this->app->alias('property-builder', PropertyBuilder::class);

        // Register main service - handle case where dependencies aren't registered yet
        $this->app->singleton(UiSchemaCraftService::class, function ($app) {
            try {
                // StateManagerInterface and ValidatorInterface are from the split packages
                // and will be available when all packages are installed together
                if ($app->bound(StateManagerInterface::class) && $app->bound(ValidatorInterface::class)) {
                    return new UiSchemaCraftService(
                        $app->make(StateManagerInterface::class),
                        $app->make(ComponentResolver::class),
                        $app->make(ValidatorInterface::class)
                    );
                }
                return null; // For testing when dependencies aren't available
            } catch (\Exception $e) {
                // For testing when dependencies aren't available
                return null;
            }
        });

        $this->app->alias(UiSchemaCraftService::class, 'ui-schema');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $configPath = $this->app->configPath('ui-schema-craft.php');
            $this->publishes([
                __DIR__.'/../config/ui-schema-craft.php' => $configPath,
            ], 'ui-schema-craft-config');
        }

        // Register default components namespace if configured
        if ($this->app->bound('config')) {
            $config = $this->app->make('config');
            $namespace = $config->get('ui-schema-craft.components_namespace');
            
            if ($namespace) {
                $this->app->make(UiSchemaCraftService::class)->registerNamespace($namespace);
            }
        }
    }
}
