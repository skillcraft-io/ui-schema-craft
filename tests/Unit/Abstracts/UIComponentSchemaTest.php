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

        $this->assertEquals($expected, $this->component->toArray());
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
        $this->assertEquals([
            'type' => 'empty-component',
            'version' => '1.0.0',
            'component' => '',
            'properties' => []
        ], $this->emptyComponent->toArray());

        // Test validation with no schema
        $result = $this->emptyComponent->validate([]);
        $this->assertTrue($result['valid']);
        $this->assertNull($result['errors']);
    }

    public function testDefaultPropertiesComponent(): void
    {
        $this->assertEquals([
            'type' => 'default-properties',
            'version' => '1.0.0',
            'component' => '',
            'properties' => [
                'name' => [
                    'type' => 'string',
                    'default' => 'Default Name'
                ]
            ]
        ], $this->defaultComponent->toArray());

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
