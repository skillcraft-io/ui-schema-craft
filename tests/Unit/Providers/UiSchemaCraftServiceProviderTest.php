<?php

namespace Skillcraft\UiSchemaCraft\Tests\Unit\Providers;

use Mockery;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversClass;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Config\Repository as RepositoryContract;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Cache\Repository as CacheContract;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Application;
use Skillcraft\UiSchemaCraft\Providers\UiSchemaCraftServiceProvider;
use Skillcraft\UiSchemaCraft\Registry\ComponentRegistry;
use Skillcraft\UiSchemaCraft\Registry\ComponentRegistryInterface;
use Skillcraft\UiSchemaCraft\Factory\ComponentFactory;
use Skillcraft\UiSchemaCraft\Factory\ComponentFactoryInterface;
use Skillcraft\UiSchemaCraft\State\StateManager;
use Skillcraft\UiSchemaCraft\State\StateManagerInterface;
use Skillcraft\UiSchemaCraft\Services\UiSchemaCraftService;
use Skillcraft\UiSchemaCraft\Schema\PropertyBuilder as CorePropertyBuilder;

#[CoversClass(UiSchemaCraftServiceProvider::class)]
class UiSchemaCraftServiceProviderTest extends TestCase
{
    use \Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    private MockApplication $app;
    private UiSchemaCraftServiceProvider $provider;
    private Repository $config;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a mock application
        $this->app = Mockery::mock(MockApplication::class)->makePartial();
        $this->app->shouldReceive('runningInConsole')->andReturn(false)->byDefault();
        $this->app->shouldReceive('basePath')->andReturn('/path/to/base')->byDefault();
        $this->app->shouldReceive('configPath')->andReturnUsing(function($path = '') {
            return '/path/to/config'.($path ? DIRECTORY_SEPARATOR.$path : $path);
        })->byDefault();
        
        $this->config = new Repository();
        
        // Set up necessary bindings
        $this->app->instance('config', $this->config);
        $this->app->bind(RepositoryContract::class, function() {
            return $this->config;
        });
        $this->app->bind('config', function() {
            return $this->config;
        });

        // Mock Cache for StateManager
        $cache = $this->createMock(CacheContract::class);
        $this->app->instance('cache.store', $cache);

        // Mock Events Dispatcher
        $events = $this->createMock(Dispatcher::class);
        $this->app->instance('events', $events);
        
        // Create the provider
        $this->provider = new UiSchemaCraftServiceProvider($this->app);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    #[Test]
    public function it_registers_core_services()
    {
        // Call register method
        $this->provider->register();

        // Verify bindings
        $this->assertTrue($this->app->bound(ComponentRegistryInterface::class));
        $this->assertTrue($this->app->bound(ComponentFactoryInterface::class));
        $this->assertTrue($this->app->bound(StateManagerInterface::class));
        $this->assertTrue($this->app->bound(CorePropertyBuilder::class));
        $this->assertTrue($this->app->bound('property-builder'));
        $this->assertTrue($this->app->bound('ui-schema'));

        // Test resolved instances
        $this->assertInstanceOf(ComponentRegistry::class, $this->app->make(ComponentRegistryInterface::class));
        $this->assertInstanceOf(ComponentFactory::class, $this->app->make(ComponentFactoryInterface::class));
        $this->assertInstanceOf(StateManager::class, $this->app->make(StateManagerInterface::class));
        $this->assertInstanceOf(CorePropertyBuilder::class, $this->app->make(CorePropertyBuilder::class));
        $this->assertInstanceOf(UiSchemaCraftService::class, $this->app->make('ui-schema'));
    }

    #[Test]
    public function it_boots_and_publishes_config()
    {
        // Register the provider first
        $this->provider->register();

        // Mock running in console
        $this->app->shouldReceive('runningInConsole')
            ->andReturn(true);

        // Create provider with mock application
        $provider = Mockery::mock(UiSchemaCraftServiceProvider::class, [$this->app])
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        // Mock the publishes method
        $provider->shouldReceive('publishes')
            ->once()
            ->withArgs(function ($paths, $group) {
                return $group === 'ui-schema-craft-config' && 
                       is_array($paths) && 
                       count($paths) === 1;
            });

        // Register and boot the provider
        $provider->register();
        $provider->boot();
    }

    #[Test]
    public function it_boots_without_registering_components()
    {
        // Create mock registry that expects register to never be called
        $registry = Mockery::mock(ComponentRegistryInterface::class);
        $registry->shouldNotReceive('register');
        
        $this->app->instance(ComponentRegistryInterface::class, $registry);

        // Boot the provider
        $this->provider->boot();
    }
}
