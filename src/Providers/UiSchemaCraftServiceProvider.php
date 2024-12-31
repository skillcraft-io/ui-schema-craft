<?php

namespace Skillcraft\UiSchemaCraft\Providers;

use Illuminate\Support\ServiceProvider;
use Skillcraft\HookFlow\Facades\Hook;
use Skillcraft\UiSchemaCraft\Abstracts\UIComponentSchema;
use Skillcraft\UiSchemaCraft\Examples\AnalyticsDashboardSchema;
use Skillcraft\UiSchemaCraft\Examples\BlogPostSchema;
use Skillcraft\UiSchemaCraft\Examples\ProductConfigurationSchema;
use Skillcraft\UiSchemaCraft\Examples\UserProfileSchema;
use Skillcraft\UiSchemaCraft\HookFlow\AddUiComponentHook;
use Skillcraft\UiSchemaCraft\Schema\PropertyBuilder;
use Skillcraft\UiSchemaCraft\Services\UiSchemaCraftService;

class UiSchemaCraftServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Register the PropertyBuilder singleton
        $this->app->singleton('ui-schema-craft.property-builder', function ($app) {
            return new PropertyBuilder;
        });

        // Merge config
        $this->mergeConfigFrom(
            __DIR__.'/../../config/ui-schema-craft.php',
            'ui-schema-craft'
        );
    }

    public function boot(): void
    {
        // Publish config
        $this->publishes([
            __DIR__.'/../../config/ui-schema-craft.php' => config_path('ui-schema-craft.php'),
        ], 'config');

        $this->app->booted(function () {

            /**
             * The Class Filter Hook can be found
             */
            Hook::register(new AddUiComponentHook);

            /**
             * This code here is for example of registration of
             * external schemas (From other packages/plugins).
             *
             * @See Examples for class definitions and properties
             */
            if (app()->environment('local') && config('ui-schema-craft.enable_examples')) {
                $external_schemas = [
                    AnalyticsDashboardSchema::class,
                    ProductConfigurationSchema::class,
                    UserProfileSchema::class,
                    BlogPostSchema::class,
                ];

                foreach ($external_schemas as $schema) {
                    Hook::execute(
                        AddUiComponentHook::class, [
                            'schema' => $schema,
                        ]);
                }

                // Quickly Check whats been registered by enabled dd_examples
                if (config('ui-schema-craft.dd_examples')) {
                    dd(app(UiSchemaCraftService::class)->getAllSchemas());
                }
            }
        });
    }
}
