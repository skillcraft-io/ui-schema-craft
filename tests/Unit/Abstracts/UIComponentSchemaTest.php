<?php

namespace Skillcraft\UiSchemaCraft\Tests\Unit\Abstracts;

use Skillcraft\UiSchemaCraft\Tests\TestCase;
use Skillcraft\UiSchemaCraft\Abstracts\UIComponentSchema;
use Skillcraft\SchemaValidation\Contracts\ValidatorInterface;
use Illuminate\Support\MessageBag;

class MockValidator implements ValidatorInterface
{
    public function validate($data, array $rules): bool
    {
        return true;
    }
}

class TestUIComponent extends UIComponentSchema
{
    protected string $type = 'test-component';
    protected string $version = '2.0.0';

    public function properties(): array
    {
        return [
            'name' => [
                'type' => 'string',
                'required' => true
            ]
        ];
    }

    protected function getValidationSchema(): ?array
    {
        return [
            'name' => ['type' => 'string', 'required' => true]
        ];
    }
}

class EmptyUIComponent extends UIComponentSchema
{
    protected string $type = 'empty-component';
    protected string $version = '1.0.0';

    public function properties(): array
    {
        return [];
    }

    protected function getValidationSchema(): ?array
    {
        return null;
    }
}

class DefaultPropertiesUIComponent extends UIComponentSchema
{
    protected string $type = 'default-properties';
    protected string $version = '1.0.0';

    public function properties(): array
    {
        return [
            'name' => [
                'type' => 'string',
                'default' => 'Default Name'
            ]
        ];
    }

    protected function getValidationSchema(): ?array
    {
        return [
            'name' => ['type' => 'string', 'default' => 'Default Name']
        ];
    }
}

class UIComponentSchemaTest extends TestCase
{
    private ValidatorInterface $validator;
    private TestUIComponent $component;
    private EmptyUIComponent $emptyComponent;
    private DefaultPropertiesUIComponent $defaultComponent;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->validator = new MockValidator();
        $this->component = new TestUIComponent($this->validator);
        $this->emptyComponent = new EmptyUIComponent($this->validator);
        $this->defaultComponent = new DefaultPropertiesUIComponent($this->validator);
    }

    public function testToArrayReturnsSchema(): void
    {
        // With hierarchical serialization, properties structure is maintained with type info
        $expected = [
            'type' => 'test-component',
            'version' => '2.0.0',
            'component' => '',
            'properties' => [
                'name' => [
                    'type' => 'string',
                    'required' => true
                ]
            ]
        ];

        $actual = $this->component->toArray();
        $this->assertEquals($expected['type'], $actual['type']);
        $this->assertEquals($expected['version'], $actual['version']);
        $this->assertEquals($expected['component'], $actual['component']);
        $this->assertEquals($expected['properties']['name']['type'], $actual['properties']['name']['type']);
        $this->assertEquals($expected['properties']['name']['required'], $actual['properties']['name']['required']);
    }

    public function testValidateWithMissingRequiredProperty(): void
    {
        $errors = new MessageBag(['validation' => ['Validation failed']]);
        $data = [];
        
        // Create a failing validator
        $failingValidator = new class implements ValidatorInterface {
            public function validate($data, array $rules): bool
            {
                return false;
            }
        };
            
        $component = new TestUIComponent($failingValidator);
        $result = $component->validate($data);
        
        $this->assertFalse($result['valid']);
        $this->assertEquals($errors->toArray(), $result['errors']->toArray());
    }

    public function testValidateWithValidData(): void
    {
        $data = ['name' => 'Test Component'];
        $result = $this->component->validate($data);
        $this->assertTrue($result['valid']);
        $this->assertNull($result['errors']);
    }

    public function testEmptyComponentProperties(): void
    {
        $this->assertEmpty($this->emptyComponent->properties());
        
        $expected = [
            'type' => 'empty-component',
            'version' => '1.0.0',
            'component' => '',
            'properties' => []
        ];
        
        $actual = $this->emptyComponent->toArray();
        $this->assertEquals($expected['type'], $actual['type']);
        $this->assertEquals($expected['version'], $actual['version']);
        $this->assertEquals($expected['component'], $actual['component']);
        $this->assertEquals([], $actual['properties']);

        // Test validation with no schema
        $result = $this->emptyComponent->validate([]);
        $this->assertTrue($result['valid']);
        $this->assertNull($result['errors']);
    }

    public function testDefaultPropertiesComponent(): void
    {
        $expected = [
            'type' => 'default-properties',
            'version' => '1.0.0',
            'component' => '',
            'properties' => [
                'name' => [
                    'type' => 'string',
                    'default' => 'Default Name'
                ]
            ]
        ];
        
        $actual = $this->defaultComponent->toArray();
        $this->assertEquals($expected['type'], $actual['type']);
        $this->assertEquals($expected['version'], $actual['version']);
        $this->assertEquals($expected['component'], $actual['component']);
        $this->assertEquals($expected['properties']['name']['type'], $actual['properties']['name']['type']);
        $this->assertEquals($expected['properties']['name']['default'], $actual['properties']['name']['default']);

        $data = ['name' => 'Default Name'];
        $result = $this->defaultComponent->validate([]);
        $this->assertTrue($result['valid']);
        $this->assertNull($result['errors']);
    }

    public function testGetType(): void
    {
        $this->assertEquals('test-component', $this->component->getType());
    }

    public function testGetVersion(): void
    {
        $this->assertEquals('2.0.0', $this->component->getVersion());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
