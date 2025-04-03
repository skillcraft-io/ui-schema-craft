<?php

namespace Skillcraft\UiSchemaCraft\Tests\Unit\Facades;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Mockery;
use Skillcraft\UiSchemaCraft\Tests\TestCase;
use Skillcraft\UiSchemaCraft\Tests\Doubles\TestUIComponentSchema;
use Skillcraft\UiSchemaCraft\Facades\UiSchema;
use Skillcraft\UiSchemaCraft\Services\UiSchemaCraftService;
use Skillcraft\SchemaState\Contracts\StateManagerInterface;
use Skillcraft\SchemaValidation\Contracts\ValidatorInterface;
use Skillcraft\UiSchemaCraft\ComponentResolver;
use Skillcraft\UiSchemaCraft\Exceptions\ValidationException;
use Skillcraft\UiSchemaCraft\Exceptions\ComponentTypeNotFoundException;
use Skillcraft\UiSchemaCraft\Contracts\UIComponentSchemaInterface;

class UiSchemaTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_component_returns_schema_and_state(): void
    {
        // Test data
        $state = ['value' => 'test'];
        $stateId = 'test-state-id';
        $expectedOutput = [
            'type' => 'form-field',
            'component' => 'form-field',
            'version' => '1.0.0',
            'label' => 'Test Field',
            'state' => $state
        ];

        // Create a mock service and define behavior
        $mockService = $this->createMock(UiSchemaCraftService::class);
        $mockService->method('getComponent')
            ->with('form-field', $stateId)
            ->willReturn($expectedOutput);

        // Swap the facade
        UiSchema::swap($mockService);

        // Call the facade
        $result = UiSchema::getComponent('form-field', $stateId);

        // Assert results
        $this->assertSame($expectedOutput, $result);
        $this->assertEquals($state, $result['state']);
        $this->assertEquals('Test Field', $result['label']);
    }

    public function test_save_state_with_valid_data(): void
    {
        // Test data
        $stateId = Str::uuid()->toString();
        $state = ['value' => 'test'];

        // Create a mock service and expect saveState to be called once
        $mockService = $this->createMock(UiSchemaCraftService::class);
        $mockService->expects($this->once())
            ->method('saveState')
            ->with($stateId, $state, 'form-field');

        // Swap the facade
        UiSchema::swap($mockService);

        // Call the facade
        UiSchema::saveState($stateId, $state, 'form-field');
    }

    public function test_save_state_with_invalid_data(): void
    {
        // Test data
        $stateId = 'test-state-id';
        $state = ['value' => 'test'];

        // Create a mock service that throws an exception
        $mockService = $this->createMock(UiSchemaCraftService::class);
        $mockService->method('saveState')
            ->with($stateId, $state, 'form-field')
            ->willThrowException(new ValidationException('Invalid data'));

        // Swap the facade
        UiSchema::swap($mockService);

        // Expect exception and call the facade
        $this->expectException(ValidationException::class);
        UiSchema::saveState($stateId, $state, 'form-field');
    }

    public function test_delete_state(): void
    {
        // Test data
        $stateId = 'test-state-id';

        // Create a mock service and expect deleteState to be called once
        $mockService = $this->createMock(UiSchemaCraftService::class);
        $mockService->expects($this->once())
            ->method('deleteState')
            ->with($stateId);

        // Swap the facade
        UiSchema::swap($mockService);

        // Call the facade
        UiSchema::deleteState($stateId);
    }

    public function test_get_component_states(): void
    {
        // Test data
        $states = [
            'state1' => ['value' => 'test1'],
            'state2' => ['value' => 'test2'],
        ];

        // Create a mock service
        $mockService = $this->createMock(UiSchemaCraftService::class);
        $mockService->method('getComponentStates')
            ->with('form-field')
            ->willReturn($states);

        // Swap the facade
        UiSchema::swap($mockService);

        // Call the facade
        $result = UiSchema::getComponentStates('form-field');
        
        // Assert results
        $this->assertSame($states, $result);
    }

    public function test_get_component_states_throws_exception_for_invalid_type(): void
    {
        // Create a mock service that throws an exception
        $mockService = $this->createMock(UiSchemaCraftService::class);
        $mockService->method('getComponentStates')
            ->with('invalid-type')
            ->willThrowException(new ComponentTypeNotFoundException('invalid-type'));

        // Swap the facade
        UiSchema::swap($mockService);

        // Expect exception and call the facade
        $this->expectException(ComponentTypeNotFoundException::class);
        UiSchema::getComponentStates('invalid-type');
    }

    public function test_create_component(): void
    {
        // Test data
        $config = [
            'label' => 'Test Field',
            'validation' => ['required' => true]
        ];
        $validatorMock = $this->createMock(ValidatorInterface::class);
        $componentMock = new TestUIComponentSchema($validatorMock);

        // Create a mock service
        $mockService = $this->createMock(UiSchemaCraftService::class);
        $mockService->method('createComponent')
            ->with('form-field', $config)
            ->willReturn($componentMock);

        // Swap the facade
        UiSchema::swap($mockService);

        // Call the facade
        $result = UiSchema::createComponent('form-field', $config);
        
        // Assert results
        $this->assertSame($componentMock, $result);
        $this->assertEquals('form-field', $result->getType());
    }

    public function test_has_component(): void
    {
        // Create a mock service
        $mockService = $this->createMock(UiSchemaCraftService::class);
        $mockService->method('hasComponent')
            ->with('invalid-type')
            ->willReturn(false);

        // Swap the facade
        UiSchema::swap($mockService);

        // Call the facade
        $result = UiSchema::hasComponent('invalid-type');
        
        // Assert results
        $this->assertFalse($result);
    }

    public function test_has_component_returns_true(): void
    {
        // Create a mock service
        $mockService = $this->createMock(UiSchemaCraftService::class);
        $mockService->method('hasComponent')
            ->with('form-field')
            ->willReturn(true);

        // Swap the facade
        UiSchema::swap($mockService);

        // Call the facade
        $result = UiSchema::hasComponent('form-field');
        
        // Assert results
        $this->assertTrue($result);
    }

    public function test_get_components(): void
    {
        // Test data
        $types = ['form-field', 'layout'];

        // Create a mock service
        $mockService = $this->createMock(UiSchemaCraftService::class);
        $mockService->method('getComponents')
            ->willReturn($types);

        // Swap the facade
        UiSchema::swap($mockService);

        // Call the facade
        $result = UiSchema::getComponents();
        
        // Assert results
        $this->assertSame($types, $result);
    }
}
