<?php

namespace Skillcraft\UiSchemaCraft\Tests\Unit\HookFlow;

use Mockery;
use Skillcraft\HookFlow\HookDefinition;
use Skillcraft\UiSchemaCraft\HookFlow\AddUiComponentHook;
use Skillcraft\UiSchemaCraft\Services\UiSchemaCraftService;
use Skillcraft\UiSchemaCraft\Tests\TestCase;

class AddUiComponentHookTest extends TestCase
{
    protected AddUiComponentHook $hook;
    protected UiSchemaCraftService $service;

    protected function setUp(): void
    {
        parent::setUp();

        if (!class_exists(HookDefinition::class)) {
            eval('
                namespace Skillcraft\HookFlow;
                abstract class HookDefinition {
                    abstract public function isFilter(): bool;
                    abstract public function getDescription(): string;
                    abstract public function getPlugin(): string;
                    abstract public function getParameters(): array;
                    abstract public function getTriggerPoint(): string;
                }
            ');
        }
        
        $this->service = Mockery::mock(UiSchemaCraftService::class);
        $this->app->instance(UiSchemaCraftService::class, $this->service);
        
        $this->hook = new AddUiComponentHook();
    }

    /** @test */
    public function it_returns_correct_hook_configuration()
    {
        $this->assertTrue($this->hook->isFilter());
        $this->assertEquals('Register Schema for UI Components to the system via hook', $this->hook->getDescription());
        $this->assertEquals('ui-schema-craft', $this->hook->getPlugin());
        $this->assertEquals(['schema' => 'string'], $this->hook->getParameters());
        $this->assertEquals('UiSchemaCraftServiceProvider@boot', $this->hook->getTriggerPoint());
    }

    /** @test */
    public function it_applies_new_schema()
    {
        $existingSchemas = ['existing-schema'];
        $newSchema = ['new-schema'];
        
        config(['ui-schema-craft.schemas' => $existingSchemas]);
        
        $this->service->shouldReceive('addSchemas')
            ->once()
            ->with([$existingSchemas[0], $newSchema])
            ->andReturnSelf();

        $result = $this->hook->apply(null, ['schema' => $newSchema]);

        $this->assertInstanceOf(UiSchemaCraftService::class, $result);
        $this->assertEquals(
            [$existingSchemas[0], $newSchema],
            config('ui-schema-craft.schemas')
        );
    }

    /** @test */
    public function it_handles_empty_existing_schemas()
    {
        config(['ui-schema-craft.schemas' => []]);
        
        $newSchema = ['new-schema'];
        
        $this->service->shouldReceive('addSchemas')
            ->once()
            ->with([$newSchema])
            ->andReturnSelf();

        $result = $this->hook->apply(null, ['schema' => $newSchema]);

        $this->assertInstanceOf(UiSchemaCraftService::class, $result);
        $this->assertEquals([$newSchema], config('ui-schema-craft.schemas'));
    }

    /** @test */
    public function it_handles_missing_schema_argument()
    {
        config(['ui-schema-craft.schemas' => []]);
        
        $this->service->shouldReceive('addSchemas')
            ->once()
            ->with([[]])
            ->andReturnSelf();

        $result = $this->hook->apply(null, []);

        $this->assertInstanceOf(UiSchemaCraftService::class, $result);
        $this->assertEquals([[]], config('ui-schema-craft.schemas'));
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
