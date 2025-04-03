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
        // Load the fix provider to ensure ValidatorInterface is bound
        $this->app->register(FixValidatorBindingServiceProvider::class);
        
        $this->app->singleton(UiSchemaCraftService::class, function ($app) {
            // StateManagerInterface and ValidatorInterface should now be available
            // thanks to our FixValidatorBindingServiceProvider
            // Create service with validation disabled by default
            return new UiSchemaCraftService(
                $app->make(StateManagerInterface::class),
                $app->make(ComponentResolver::class),
                $app->make(ValidatorInterface::class),
                false // Disable validation by default
            );
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
