<?php

namespace Skillcraft\UiSchemaCraft\Tests\Unit\Facades;

use Skillcraft\UiSchemaCraft\Tests\TestCase;
use Skillcraft\UiSchemaCraft\Facades\UiSchema;
use Skillcraft\UiSchemaCraft\Services\UiSchemaCraftService;
use Skillcraft\UiSchemaCraft\Registry\ComponentRegistryInterface;
use Skillcraft\UiSchemaCraft\Factory\ComponentFactoryInterface;
use Skillcraft\UiSchemaCraft\State\StateManagerInterface;
use Skillcraft\UiSchemaCraft\Validation\ValidationResult;
use Skillcraft\UiSchemaCraft\Exceptions\ComponentTypeNotFoundException;
use Skillcraft\UiSchemaCraft\Abstracts\UIComponentSchema;
use Illuminate\Support\Str;
use Mockery;

class UiSchemaTest extends TestCase
{
    private ComponentRegistryInterface $registry;
    private ComponentFactoryInterface $factory;
    private StateManagerInterface $stateManager;
    private UiSchemaCraftService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->registry = Mockery::mock(ComponentRegistryInterface::class);
        $this->factory = Mockery::mock(ComponentFactoryInterface::class);
        $this->stateManager = Mockery::mock(StateManagerInterface::class);

        $this->service = new UiSchemaCraftService(
            $this->registry,
            $this->factory,
            $this->stateManager
        );

        $this->app->singleton('ui-schema', function () {
            return $this->service;
        });
    }

    public function test_get_component_returns_schema_and_state(): void
    {
        $component = Mockery::mock(UIComponentSchema::class);
        $schema = ['type' => 'form-field'];
        $state = ['value' => 'test'];
        $stateId = 'test-state-id';

        $this->factory->shouldReceive('create')
            ->with('form-field', [])
            ->andReturn($component);

        $component->shouldReceive('toArray')
            ->andReturn($schema);

        $this->stateManager->shouldReceive('load')
            ->with($stateId)
            ->andReturn($state);

        $result = UiSchema::getComponent('form-field', $stateId);

        $this->assertEquals([
            'schema' => $schema,
            'state' => $state,
        ], $result);
    }

    public function test_save_state_with_valid_data(): void
    {
        $component = Mockery::mock(UIComponentSchema::class);
        $state = ['value' => 'test'];
        $stateId = Str::uuid()->toString();
        $validationResult = new ValidationResult();

        $this->factory->shouldReceive('create')
            ->with('form-field', [])
            ->andReturn($component);

        $component->shouldReceive('validate')
            ->with($state)
            ->andReturn($validationResult);

        $this->stateManager->shouldReceive('save')
            ->with($stateId, $component, $state)
            ->once();

        $result = UiSchema::saveState('form-field', $state, $stateId);

        $this->assertEquals([
            'stateId' => $stateId,
            'state' => $state,
            'validation' => null,
        ], $result);
    }

    public function test_save_state_with_invalid_data(): void
    {
        $component = Mockery::mock(UIComponentSchema::class);
        $state = ['invalid' => 'data'];
        $stateId = Str::uuid()->toString();
        $validationResult = new ValidationResult();
        $validationResult->addError('value', 'Value is required');

        $this->factory->shouldReceive('create')
            ->with('form-field', [])
            ->andReturn($component);

        $component->shouldReceive('validate')
            ->with($state)
            ->andReturn($validationResult);

        $this->stateManager->shouldNotReceive('save');

        $result = UiSchema::saveState('form-field', $state, $stateId);

        $this->assertEquals([
            'stateId' => $stateId,
            'state' => $state,
            'validation' => $validationResult->toArray(),
        ], $result);
    }

    public function test_delete_state(): void
    {
        $stateId = 'test-state-id';

        $this->stateManager->shouldReceive('delete')
            ->with($stateId)
            ->once();

        UiSchema::deleteState($stateId);
    }

    public function test_get_component_states(): void
    {
        $states = [
            'state1' => ['value' => 'test1'],
            'state2' => ['value' => 'test2'],
        ];

        $this->registry->shouldReceive('has')
            ->with('form-field')
            ->andReturn(true);

        $this->stateManager->shouldReceive('getStatesForComponent')
            ->with('form-field')
            ->andReturn($states);

        $result = UiSchema::getComponentStates('form-field');

        $this->assertEquals($states, $result);
    }

    public function test_get_component_states_throws_exception_for_invalid_type(): void
    {
        $this->registry->shouldReceive('has')
            ->with('invalid-type')
            ->andReturn(false);

        $this->expectException(ComponentTypeNotFoundException::class);

        UiSchema::getComponentStates('invalid-type');
    }

    public function test_create_component(): void
    {
        $component = Mockery::mock(UIComponentSchema::class);
        $config = ['label' => 'Test Field'];

        $this->factory->shouldReceive('create')
            ->with('form-field', $config)
            ->andReturn($component);

        $result = UiSchema::createComponent('form-field', $config);

        $this->assertSame($component, $result);
    }

    public function test_create_from_schema(): void
    {
        $component = Mockery::mock(UIComponentSchema::class);
        $schema = [
            'type' => 'form-field',
            'label' => 'Test Field'
        ];

        $this->factory->shouldReceive('createFromSchema')
            ->with($schema)
            ->andReturn($component);

        $result = UiSchema::createFromSchema($schema);

        $this->assertSame($component, $result);
    }

    public function test_has_component(): void
    {
        $this->registry->shouldReceive('has')
            ->with('form-field')
            ->andReturn(true);

        $this->registry->shouldReceive('has')
            ->with('invalid-type')
            ->andReturn(false);

        $this->assertTrue(UiSchema::hasComponent('form-field'));
        $this->assertFalse(UiSchema::hasComponent('invalid-type'));
    }

    public function test_get_components(): void
    {
        $components = [
            'form-field' => UIComponentSchema::class,
        ];

        $this->registry->shouldReceive('all')
            ->andReturn($components);

        $result = UiSchema::getComponents();

        $this->assertEquals($components, $result);
    }
}
