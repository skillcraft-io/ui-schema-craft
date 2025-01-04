<?php

namespace Skillcraft\UiSchemaCraft\Tests\Unit\Services;

use Mockery;
use Mockery\MockInterface;
use Skillcraft\UiSchemaCraft\Tests\TestCase;
use Skillcraft\UiSchemaCraft\Services\UiSchemaCraftService;
use Skillcraft\UiSchemaCraft\Registry\ComponentRegistryInterface;
use Skillcraft\UiSchemaCraft\Factory\ComponentFactoryInterface;
use Skillcraft\UiSchemaCraft\State\StateManagerInterface;
use Skillcraft\UiSchemaCraft\Abstracts\UIComponentSchema;
use Skillcraft\UiSchemaCraft\Validation\ValidationResult;
use Skillcraft\UiSchemaCraft\Exceptions\ComponentTypeNotFoundException;

class UiSchemaCraftServiceTest extends TestCase
{
    private UiSchemaCraftService $service;
    private MockInterface $registry;
    private MockInterface $factory;
    private MockInterface $stateManager;
    private MockInterface $component;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->registry = Mockery::mock(ComponentRegistryInterface::class);
        $this->factory = Mockery::mock(ComponentFactoryInterface::class);
        $this->stateManager = Mockery::mock(StateManagerInterface::class);
        $this->component = Mockery::mock(UIComponentSchema::class);
        
        $this->service = new UiSchemaCraftService(
            $this->registry,
            $this->factory,
            $this->stateManager
        );
    }

    public function test_get_component_without_state(): void
    {
        $type = 'test-component';
        $schema = ['type' => $type];
        
        $this->factory->expects('create')
            ->once()
            ->with($type, [])
            ->andReturn($this->component);
            
        $this->component->expects('toArray')
            ->once()
            ->andReturn($schema);

        $result = $this->service->getComponent($type);
        
        $this->assertEquals([
            'schema' => $schema,
            'state' => null,
        ], $result);
    }

    public function test_get_component_with_state(): void
    {
        $type = 'test-component';
        $stateId = 'test-state';
        $schema = ['type' => $type];
        $state = ['value' => 'test'];
        
        $this->factory->expects('create')
            ->once()
            ->with($type, [])
            ->andReturn($this->component);
            
        $this->component->expects('toArray')
            ->once()
            ->andReturn($schema);
            
        $this->stateManager->expects('load')
            ->once()
            ->with($stateId)
            ->andReturn($state);

        $result = $this->service->getComponent($type, $stateId);
        
        $this->assertEquals([
            'schema' => $schema,
            'state' => $state,
        ], $result);
    }

    public function test_get_component_throws_exception_for_unknown_type(): void
    {
        $type = 'unknown-component';
        
        $this->factory->expects('create')
            ->once()
            ->with($type, [])
            ->andThrow(new ComponentTypeNotFoundException($type));

        $this->expectException(ComponentTypeNotFoundException::class);
        $this->service->getComponent($type);
    }

    public function test_save_state_with_validation_errors(): void
    {
        $type = 'test-component';
        $stateId = 'test-state';
        $state = ['value' => 'test'];
        $validationResult = Mockery::mock(ValidationResult::class);
        $validationErrors = ['field' => ['error']];
        
        $this->factory->expects('create')
            ->once()
            ->with($type, [])
            ->andReturn($this->component);
            
        $this->component->expects('validate')
            ->once()
            ->with($state)
            ->andReturn($validationResult);
            
        $validationResult->expects('hasErrors')
            ->once()
            ->andReturn(true);
            
        $validationResult->expects('toArray')
            ->once()
            ->andReturn($validationErrors);

        $result = $this->service->saveState($type, $state, $stateId);
        
        $this->assertEquals([
            'stateId' => $stateId,
            'state' => $state,
            'validation' => $validationErrors,
        ], $result);
    }

    public function test_save_state_successful(): void
    {
        $type = 'test-component';
        $stateId = 'test-state';
        $state = ['value' => 'test'];
        $validationResult = Mockery::mock(ValidationResult::class);
        
        $this->factory->expects('create')
            ->once()
            ->with($type, [])
            ->andReturn($this->component);
            
        $this->component->expects('validate')
            ->once()
            ->with($state)
            ->andReturn($validationResult);
            
        $validationResult->expects('hasErrors')
            ->once()
            ->andReturn(false);
            
        $this->stateManager->expects('save')
            ->once()
            ->with($stateId, $this->component, $state);

        $result = $this->service->saveState($type, $state, $stateId);
        
        $this->assertEquals([
            'stateId' => $stateId,
            'state' => $state,
            'validation' => null,
        ], $result);
    }

    public function test_save_state_generates_uuid_when_no_id_provided(): void
    {
        $type = 'test-component';
        $state = ['value' => 'test'];
        $validationResult = Mockery::mock(ValidationResult::class);
        
        $this->factory->expects('create')
            ->once()
            ->with($type, [])
            ->andReturn($this->component);
            
        $this->component->expects('validate')
            ->once()
            ->with($state)
            ->andReturn($validationResult);
            
        $validationResult->expects('hasErrors')
            ->once()
            ->andReturn(false);
            
        $this->stateManager->expects('save')
            ->once()
            ->withArgs(function ($stateId, $component, $stateData) use ($state) {
                return is_string($stateId) && 
                    strlen($stateId) === 36 && // UUID length
                    $component === $this->component &&
                    $stateData === $state;
            });

        $result = $this->service->saveState($type, $state);
        
        $this->assertArrayHasKey('stateId', $result);
        $this->assertEquals($state, $result['state']);
        $this->assertNull($result['validation']);
        $this->assertEquals(36, strlen($result['stateId'])); // UUID length
    }

    public function test_save_state_throws_exception_for_unknown_type(): void
    {
        $type = 'unknown-component';
        $state = ['value' => 'test'];
        
        $this->factory->expects('create')
            ->once()
            ->with($type, [])
            ->andThrow(new ComponentTypeNotFoundException($type));

        $this->expectException(ComponentTypeNotFoundException::class);
        $this->service->saveState($type, $state);
    }

    public function test_delete_state(): void
    {
        $stateId = 'test-state';
        
        $this->stateManager->expects('delete')
            ->once()
            ->with($stateId);

        $this->service->deleteState($stateId);
        $this->assertTrue(true, 'State was deleted successfully');
    }

    public function test_get_component_states_throws_exception_for_unknown_type(): void
    {
        $type = 'unknown-component';
        
        $this->registry->expects('has')
            ->once()
            ->with($type)
            ->andReturn(false);

        $this->expectException(ComponentTypeNotFoundException::class);
        
        $this->service->getComponentStates($type);
    }

    public function test_get_component_states_returns_states(): void
    {
        $type = 'test-component';
        $states = ['state1' => [], 'state2' => []];
        
        $this->registry->expects('has')
            ->once()
            ->with($type)
            ->andReturn(true);
            
        $this->stateManager->expects('getStatesForComponent')
            ->once()
            ->with($type)
            ->andReturn($states);

        $result = $this->service->getComponentStates($type);
        
        $this->assertEquals($states, $result);
    }

    public function test_create_component(): void
    {
        $type = 'test-component';
        $config = ['option' => 'value'];
        
        $this->factory->expects('create')
            ->once()
            ->with($type, $config)
            ->andReturn($this->component);

        $result = $this->service->createComponent($type, $config);
        
        $this->assertSame($this->component, $result);
    }

    public function test_create_component_throws_exception_for_unknown_type(): void
    {
        $type = 'unknown-component';
        
        $this->factory->expects('create')
            ->once()
            ->with($type, [])
            ->andThrow(new ComponentTypeNotFoundException($type));

        $this->expectException(ComponentTypeNotFoundException::class);
        $this->service->createComponent($type);
    }

    public function test_create_from_schema(): void
    {
        $schema = ['type' => 'test-component'];
        
        $this->factory->expects('createFromSchema')
            ->once()
            ->with($schema)
            ->andReturn($this->component);

        $result = $this->service->createFromSchema($schema);
        
        $this->assertSame($this->component, $result);
    }

    public function test_validate(): void
    {
        $type = 'test-component';
        $data = ['value' => 'test'];
        $validationResult = Mockery::mock(ValidationResult::class);
        
        $this->registry->expects('get')
            ->once()
            ->with($type)
            ->andReturn($this->component);
            
        $this->component->expects('validate')
            ->once()
            ->with($data)
            ->andReturn($validationResult);

        $result = $this->service->validate($type, $data);
        
        $this->assertSame($validationResult, $result);
    }

    public function test_get_components(): void
    {
        $components = ['component1', 'component2'];
        
        $this->registry->expects('all')
            ->once()
            ->andReturn($components);

        $result = $this->service->getComponents();
        
        $this->assertEquals($components, $result);
    }

    public function test_has_component(): void
    {
        $type = 'test-component';
        
        $this->registry->expects('has')
            ->once()
            ->with($type)
            ->andReturn(true);

        $result = $this->service->hasComponent($type);
        
        $this->assertTrue($result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
