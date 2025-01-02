<?php

namespace Skillcraft\UiSchemaCraft\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Skillcraft\UiSchemaCraft\Services\UiSchemaCraftService;
use Skillcraft\UiSchemaCraft\Registry\ComponentRegistryInterface;
use Skillcraft\UiSchemaCraft\Registry\ComponentRegistry;
use Skillcraft\UiSchemaCraft\Factory\ComponentFactoryInterface;
use Skillcraft\UiSchemaCraft\Factory\ComponentFactory;
use Skillcraft\UiSchemaCraft\State\StateManagerInterface;
use Skillcraft\UiSchemaCraft\State\StateManager;
use Skillcraft\UiSchemaCraft\Validation\CompositeValidator;
use Skillcraft\UiSchemaCraft\Validation\Rules\RequiredRule;

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
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../config/ui-schema-craft.php' => config_path('ui-schema-craft.php'),
            ], 'ui-schema-craft-config');
        }

        // Register default components if configured
        if (config('ui-schema-craft.register_default_components', true)) {
            $this->registerDefaultComponents();
        }
    }

    protected function registerCoreServices(): void
    {
        // Register Validator
        $this->app->singleton(CompositeValidator::class, function ($app) {
            $validator = new CompositeValidator();
            $validator->addRule(new RequiredRule());
            return $validator;
        });

        // Register Component Registry
        $this->app->singleton(ComponentRegistryInterface::class, function ($app) {
            return new ComponentRegistry(
                $app->make(CompositeValidator::class)
            );
        });

        // Register Component Factory
        $this->app->singleton(ComponentFactoryInterface::class, function ($app) {
            return new ComponentFactory(
                $app->make(ComponentRegistryInterface::class)
            );
        });

        // Register State Manager
        $this->app->singleton(StateManagerInterface::class, function ($app) {
            return new StateManager(
                $app->make('cache.store'),
                config('ui-schema-craft.state_ttl', 3600)
            );
        });
    }

    protected function registerDefaultComponents(): void
    {
        $registry = $this->app->make(ComponentRegistryInterface::class);
        
        // Register default components
        $defaultComponents = [
            \Skillcraft\UiSchemaCraft\Components\FormField::class,
            \Skillcraft\UiSchemaCraft\Components\FormGroup::class,
        ];

        foreach ($defaultComponents as $componentClass) {
            $registry->register(new $componentClass());
        }
    }
}
