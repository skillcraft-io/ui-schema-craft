<?php

namespace Skillcraft\UiSchemaCraft\Tests\Unit\Factory;

use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Skillcraft\UiSchemaCraft\Tests\Stubs\TestComponent;
use Skillcraft\UiSchemaCraft\Exceptions\ComponentTypeNotFoundException;
use Skillcraft\UiSchemaCraft\Factory\ComponentFactory;
use Skillcraft\UiSchemaCraft\Registry\ComponentRegistryInterface;
use Skillcraft\UiSchemaCraft\Validation\CompositeValidator;

class ComponentFactoryTest extends TestCase
{
    private ComponentFactory $factory;
    private MockInterface $registry;
    private MockInterface $validator;
    private TestComponent $component;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->registry = Mockery::mock(ComponentRegistryInterface::class);
        $this->validator = Mockery::mock(CompositeValidator::class);
        $this->component = new TestComponent();
        
        $this->factory = new ComponentFactory($this->registry, $this->validator);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_create_throws_exception_when_component_type_not_found(): void
    {
        $this->expectException(ComponentTypeNotFoundException::class);
        
        $this->registry->shouldReceive('has')
            ->once()
            ->with('non_existent_type')
            ->andReturn(false);
            
        $this->factory->create('non_existent_type');
    }

    public function test_create_returns_configured_component(): void
    {
        $type = 'test-component';
        $config = [
            'label' => 'Username',
            'name' => 'username',
            'required' => true
        ];
        
        $this->registry->shouldReceive('has')
            ->once()
            ->with($type)
            ->andReturn(true);
            
        $this->registry->shouldReceive('get')
            ->once()
            ->with($type)
            ->andReturn($this->component);
            
        $result = $this->factory->create($type, $config);
        
        $this->assertInstanceOf(TestComponent::class, $result);
        $this->assertNotSame($this->component, $result);
        $propertyValues = $result->getPropertyValues();
        $this->assertEquals('Username', $propertyValues['label']);
        $this->assertEquals('username', $propertyValues['name']);
        $this->assertTrue($propertyValues['required']);
    }

    public function test_create_from_schema_throws_exception_when_type_missing(): void
    {
        $this->expectException(ComponentTypeNotFoundException::class);
        
        $schema = ['config' => []];
        
        $this->factory->createFromSchema($schema);
    }

    public function test_create_from_schema_creates_component_with_config(): void
    {
        $schema = [
            'type' => 'test-component',
            'config' => [
                'label' => 'Username',
                'name' => 'username'
            ]
        ];
        
        $this->registry->shouldReceive('has')
            ->once()
            ->with('test-component')
            ->andReturn(true);
            
        $this->registry->shouldReceive('get')
            ->once()
            ->with('test-component')
            ->andReturn($this->component);
            
        $result = $this->factory->createFromSchema($schema);
        
        $this->assertInstanceOf(TestComponent::class, $result);
        $this->assertNotSame($this->component, $result);
        $propertyValues = $result->getPropertyValues();
        $this->assertEquals('Username', $propertyValues['label']);
        $this->assertEquals('username', $propertyValues['name']);
    }

    public function test_create_from_schema_handles_missing_config(): void
    {
        $schema = ['type' => 'test-component'];
        
        $this->registry->shouldReceive('has')
            ->once()
            ->with('test-component')
            ->andReturn(true);
            
        $this->registry->shouldReceive('get')
            ->once()
            ->with('test-component')
            ->andReturn($this->component);
            
        $result = $this->factory->createFromSchema($schema);
        
        $this->assertInstanceOf(TestComponent::class, $result);
        $this->assertNotSame($this->component, $result);
    }

    public function test_create_handles_nonexistent_setters(): void
    {
        $type = 'test-component';
        $config = [
            'nonExistentProperty' => 'value',
            'label' => 'Username',
            'name' => 'username'
        ];
        
        $this->registry->shouldReceive('has')
            ->once()
            ->with($type)
            ->andReturn(true);
            
        $this->registry->shouldReceive('get')
            ->once()
            ->with($type)
            ->andReturn($this->component);
            
        $result = $this->factory->create($type, $config);
        
        $this->assertInstanceOf(TestComponent::class, $result);
        $this->assertNotSame($this->component, $result);
        $propertyValues = $result->getPropertyValues();
        $this->assertEquals('Username', $propertyValues['label']);
        $this->assertEquals('username', $propertyValues['name']);
        $this->assertArrayNotHasKey('nonExistentProperty', $propertyValues);
    }
}
