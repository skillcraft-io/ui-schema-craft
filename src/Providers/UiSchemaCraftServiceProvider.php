<?php

namespace Skillcraft\UiSchemaCraft\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Skillcraft\UiSchemaCraft\State\StateManager;
use Skillcraft\UiSchemaCraft\Facades\PropertyBuilder;
use Skillcraft\UiSchemaCraft\Factory\ComponentFactory;
use Skillcraft\UiSchemaCraft\Registry\ComponentRegistry;
use Skillcraft\UiSchemaCraft\State\StateManagerInterface;
use Skillcraft\UiSchemaCraft\Services\UiSchemaCraftService;
use Skillcraft\UiSchemaCraft\Factory\ComponentFactoryInterface;
use Skillcraft\UiSchemaCraft\Registry\ComponentRegistryInterface;
use Skillcraft\UiSchemaCraft\Schema\PropertyBuilder as CorePropertyBuilder;
use Skillcraft\UiSchemaCraft\Validation\CompositeValidator;

class UiSchemaCraftServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/ui-schema-craft.php',
            'ui-schema-craft'
        );

        // Register Core Services
        $this->registerCoreServices();

        // Register Main Service
        $this->app->singleton('ui-schema', function ($app) {
            return new UiSchemaCraftService(
                $app->make(ComponentRegistryInterface::class),
                $app->make(ComponentFactoryInterface::class),
                $app->make(StateManagerInterface::class)
            );
        });

        $this->app->singleton(CorePropertyBuilder::class);
        $this->app->alias(CorePropertyBuilder::class, 'property-builder');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../config/ui-schema-craft.php' => $this->app->configPath('ui-schema-craft.php'),
            ], 'ui-schema-craft-config');
        }

        // Register default components if configured
        if (Config::get('ui-schema-craft.register_default_components', true) === true) {
            $this->registerDefaultComponents();
        }
    }

    protected function registerCoreServices(): void
    {
        // Register Component Registry
        $this->app->singleton(ComponentRegistryInterface::class, function ($app) {
            return new ComponentRegistry($app['events']);
        });

        // Register Composite Validator
        $this->app->singleton(CompositeValidator::class, function ($app) {
            return new CompositeValidator();
        });

        // Register Component Factory
        $this->app->singleton(ComponentFactoryInterface::class, function ($app) {
            return new ComponentFactory(
                $app->make(ComponentRegistryInterface::class),
                $app->make(CompositeValidator::class)
            );
        });

        // Register State Manager
        $this->app->singleton(StateManagerInterface::class, function ($app) {
            return new StateManager($app['cache.store']);
        });
    }

    protected function registerDefaultComponents(): void
    {
        // Removed Text component registration
    }
}
