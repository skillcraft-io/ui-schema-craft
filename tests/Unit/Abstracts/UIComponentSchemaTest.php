<?php

namespace Skillcraft\UiSchemaCraft\Tests\Unit\Abstracts;

use Skillcraft\UiSchemaCraft\Tests\TestCase;
use Skillcraft\UiSchemaCraft\Abstracts\UIComponentSchema;
use Skillcraft\UiSchemaCraft\Schema\Property;
use Skillcraft\UiSchemaCraft\Validation\ValidationResult;
use Mockery;
use ReflectionClass;

class TestUIComponent extends UIComponentSchema
{
    protected string $type = 'test-component';
    protected string $component = 'test';
    protected string $version = '2.0.0';

    public function properties(): array
    {
        $property = new Property('name', 'string');
        $property->addRule('required');
        return [$property];
    }

    protected function getExampleData(): array
    {
        return [
            'name' => 'Test Component'
        ];
    }
}

class EmptyUIComponent extends UIComponentSchema
{
    protected string $type = 'empty-component';
    protected string $component = 'empty';
    protected string $version = '1.0.0';

    public function properties(): array
    {
        return [];
    }

    protected function getExampleData(): array
    {
        return [];
    }
}

class DefaultPropertiesUIComponent extends UIComponentSchema
{
    protected string $type = 'default-properties-component';
    protected string $component = 'default';
    protected string $version = '1.0.0';

    public function properties(): array
    {
        return [];
    }

    protected function getExampleData(): array
    {
        return [];
    }
}

class UIComponentSchemaTest extends TestCase
{
    private TestUIComponent $component;
    private EmptyUIComponent $emptyComponent;
    private DefaultPropertiesUIComponent $defaultPropertiesComponent;

    protected function setUp(): void
    {
        parent::setUp();
        $this->component = new TestUIComponent();
        $this->emptyComponent = new EmptyUIComponent();
        $this->defaultPropertiesComponent = new DefaultPropertiesUIComponent();
    }

    public function test_to_array_returns_component_schema(): void
    {
        $properties = $this->component->properties();
        $this->assertCount(1, $properties);
        $this->assertInstanceOf(Property::class, $properties[0]);
        $this->assertEquals('name', $properties[0]->getName());

        $array = $this->component->toArray();
        $this->assertEquals([
            'type' => 'test-component',
            'component' => 'test',
            'version' => '2.0.0',
            'properties' => $properties,
            'children' => [],
        ], $array);
    }

    public function test_to_array(): void
    {
        // Add a child component
        $childComponent = new TestUIComponent();
        $this->component->addChild('child', $childComponent);

        $array = $this->component->toArray();

        // Verify basic component properties
        $this->assertSame('test-component', $array['type']);
        $this->assertSame('test', $array['component']);
        $this->assertSame('2.0.0', $array['version']);
        
        // Verify properties array
        $this->assertIsArray($array['properties']);
        $this->assertCount(1, $array['properties']);
        $firstProperty = reset($array['properties']);
        $this->assertSame('name', $firstProperty->getName());
        $this->assertSame('string', $firstProperty->getType());
        
        // Verify children schema
        $this->assertIsArray($array['children']);
        $this->assertArrayHasKey('child', $array['children']);
        $childSchema = $array['children']['child'];
        $this->assertSame('test-component', $childSchema['type']);
        $this->assertSame('test', $childSchema['component']);
        $this->assertSame('2.0.0', $childSchema['version']);
    }

    public function test_validate_with_missing_required_property(): void
    {
        $result = $this->component->validate([]);
        $this->assertTrue($result->hasErrors());
        $this->assertEquals(
            ['name' => ['The name field is required.']],
            $result->toArray()['errors']
        );
    }

    public function test_validate_with_children(): void
    {
        // First add a valid child component
        $childComponent = Mockery::mock(UIComponentSchema::class);
        $childValidationResult = new ValidationResult();
        $childValidationResult->addError('child_field', 'Child field is required');

        $childComponent->shouldReceive('getIdentifier')
            ->andReturn('child-component');
        $childComponent->shouldReceive('validate')
            ->andReturn($childValidationResult);

        $this->component->addChild('child', $childComponent);

        $result = $this->component->validate([
            'name' => 'Test',
            'children' => [
                'child' => ['child_data' => 'value']
            ]
        ]);

        $this->assertTrue($result->hasErrors());
        $this->assertEquals(
            ['child_field' => ['Child field is required']],
            $result->toArray()['errors']
        );
    }

    public function test_validate_with_valid_data(): void
    {
        $result = $this->component->validate(['name' => 'Test']);
        $this->assertFalse($result->hasErrors());
        $this->assertEmpty($result->toArray()['errors']);
    }

    public function test_validate_with_nullable_property(): void
    {
        $property = new Property('optional_field', 'string');
        $property->nullable();
        
        $component = new class extends UIComponentSchema {
            protected string $type = 'test';
            protected string $component = 'test';
            private Property $property;
            
            public function setProperty(Property $property): void
            {
                $this->property = $property;
            }
            
            public function properties(): array
            {
                return [$this->property];
            }
            
            protected function getExampleData(): array
            {
                return [];
            }
        };
        
        $component->setProperty($property);
        
        $result = $component->validate(['optional_field' => null]);
        $this->assertFalse($result->hasErrors());
        $this->assertEmpty($result->toArray()['errors']);
    }

    public function test_validate_with_invalid_property_value(): void
    {
        $property = new Property('number_field', 'number');
        $property->addRule('required');
        
        $component = new class extends UIComponentSchema {
            protected string $type = 'test';
            protected string $component = 'test';
            private Property $property;
            
            public function setProperty(Property $property): void
            {
                $this->property = $property;
            }
            
            public function properties(): array
            {
                return [$this->property];
            }
            
            protected function getExampleData(): array
            {
                return [];
            }
        };
        
        $component->setProperty($property);
        
        $result = $component->validate(['number_field' => 'not a number']);
        $this->assertTrue($result->hasErrors());
        $this->assertArrayHasKey('number_field', $result->toArray()['errors']);
    }

    public function test_validate_with_valid_nested_children(): void
    {
        // Create a child component with a valid property
        $childComponent = new class extends UIComponentSchema {
            protected string $type = 'child';
            protected string $component = 'test';
            
            public function properties(): array
            {
                $property = new Property('child_field', 'string');
                return [$property];
            }
            
            protected function getExampleData(): array
            {
                return [];
            }
        };

        $this->component->addChild('child', $childComponent);

        $result = $this->component->validate([
            'name' => 'Parent',
            'children' => [
                'child' => [
                    'child_field' => 'Child Value'
                ]
            ]
        ]);

        $this->assertFalse($result->hasErrors());
        $this->assertEmpty($result->toArray()['errors']);
    }

    public function test_validate_with_non_required_property(): void
    {
        $property = new Property('optional_field', 'string');
        
        $component = new class extends UIComponentSchema {
            protected string $type = 'test';
            protected string $component = 'test';
            private Property $property;
            
            public function setProperty(Property $property): void
            {
                $this->property = $property;
            }
            
            public function properties(): array
            {
                return [$this->property];
            }
            
            protected function getExampleData(): array
            {
                return [];
            }
        };
        
        $component->setProperty($property);
        
        // Test validation without the optional field
        $result = $component->validate([]);
        $this->assertFalse($result->hasErrors());
        $this->assertEmpty($result->toArray()['errors']);
        
        // Test validation with the optional field
        $result = $component->validate(['optional_field' => 'value']);
        $this->assertFalse($result->hasErrors());
        $this->assertEmpty($result->toArray()['errors']);
    }

    public function test_empty_component_properties(): void
    {
        $this->assertEmpty($this->emptyComponent->properties());
    }

    public function test_default_properties_component(): void
    {
        $this->assertEmpty($this->defaultPropertiesComponent->properties());
    }

    public function test_get_identifier_returns_type(): void
    {
        $this->assertEquals('test-component', $this->component->getIdentifier());
    }

    public function test_get_version_returns_version(): void
    {
        $this->assertEquals('2.0.0', $this->component->getVersion());
    }

    public function test_get_and_set_property_values(): void
    {
        // Test initial property values
        $values = $this->component->getPropertyValues();
        $this->assertArrayHasKey('name', $values);
        $this->assertNull($values['name']);

        // Test setting valid property value
        $this->component->setPropertyValue('name', 'Test Value');
        $values = $this->component->getPropertyValues();
        $this->assertEquals('Test Value', $values['name']);

        // Test setting invalid property name (should be ignored)
        $this->component->setPropertyValue('invalid_property', 'Invalid Value');
        $values = $this->component->getPropertyValues();
        $this->assertArrayNotHasKey('invalid_property', $values);
    }

    public function test_clone_component(): void
    {
        // Add a child component
        $childComponent = new EmptyUIComponent();
        $this->component->addChild('child', $childComponent);

        // Clone the component
        $clonedComponent = clone $this->component;

        // Verify children are cloned
        $reflection = new ReflectionClass($clonedComponent);
        $childrenProperty = $reflection->getProperty('children');
        $childrenProperty->setAccessible(true);
        $children = $childrenProperty->getValue($clonedComponent);

        $this->assertCount(1, $children);
        $this->assertInstanceOf(EmptyUIComponent::class, $children['child']);
        $this->assertNotSame($childComponent, $children['child']);
    }

    public function test_clone_component_with_no_children(): void
    {
        // Clone the component without adding any children
        $clonedComponent = clone $this->component;

        // Verify the clone was successful
        $this->assertInstanceOf(TestUIComponent::class, $clonedComponent);
        
        // Verify children array is empty
        $reflection = new ReflectionClass($clonedComponent);
        $childrenProperty = $reflection->getProperty('children');
        $childrenProperty->setAccessible(true);
        $children = $childrenProperty->getValue($clonedComponent);
        
        $this->assertEmpty($children);
    }

    public function test_get_property_names(): void
    {
        $names = $this->component->getPropertyNames();
        $this->assertEquals(['name'], $names);

        $names = $this->emptyComponent->getPropertyNames();
        $this->assertEmpty($names);
    }

    public function test_validate_with_children_multiple_errors(): void
    {
        // Create a child component with multiple validation errors
        $childComponent = Mockery::mock(UIComponentSchema::class);
        $childValidationResult = new ValidationResult();
        $childValidationResult->addError('field1', 'Error message 1');
        $childValidationResult->addError('field1', 'Error message 2');
        $childValidationResult->addError('field2', 'Error message 3');

        $childComponent->shouldReceive('getIdentifier')
            ->andReturn('child-component');
        $childComponent->shouldReceive('validate')
            ->andReturn($childValidationResult);

        $this->component->addChild('child', $childComponent);

        $result = $this->component->validate([
            'name' => 'Test',
            'children' => [
                'child' => ['data' => 'value']
            ]
        ]);

        $this->assertTrue($result->hasErrors());
        $errors = $result->toArray()['errors'];
        $this->assertArrayHasKey('field1', $errors);
        $this->assertArrayHasKey('field2', $errors);
        $this->assertCount(2, $errors['field1']);
        $this->assertContains('Error message 1', $errors['field1']);
        $this->assertContains('Error message 2', $errors['field1']);
        $this->assertContains('Error message 3', $errors['field2']);
    }

    public function test_validate_with_nonexistent_child(): void
    {
        // Create a component without any children
        $result = $this->component->validate([
            'name' => 'Test',
            'children' => [
                'nonexistent' => ['data' => 'value']
            ]
        ]);

        // Should not throw an error for nonexistent child
        $this->assertFalse($result->hasErrors());
    }

    public function test_validate_with_null_value_non_nullable_property(): void
    {
        // Create a component with a non-nullable property
        $component = new class extends UIComponentSchema {
            protected string $type = 'test';
            protected string $component = 'test';
            protected string $version = '1.0.0';

            public function properties(): array
            {
                $property = new Property('number', 'number');
                $property->required(); // Make it required to ensure validation runs
                return [$property];
            }

            protected function getExampleData(): array
            {
                return ['number' => 42];
            }
        };

        // Test with null value
        $result = $component->validate(['number' => null]);
        $this->assertTrue($result->hasErrors());
        $errors = $result->toArray()['errors'];
        $this->assertArrayHasKey('number', $errors);
        $this->assertNotEmpty($errors['number']);
        $this->assertStringContainsString('number', $errors['number'][0]);
    }

    public function test_validate_with_null_value_validation_message(): void
    {
        // Create a component with a non-nullable property and custom validation message
        $component = new class extends UIComponentSchema {
            protected string $type = 'test';
            protected string $component = 'test';
            protected string $version = '1.0.0';

            public function properties(): array
            {
                $property = new Property('field', 'string');
                $property->required()
                    ->addAttribute('validationMessage', 'Custom validation message');
                return [$property];
            }

            protected function getExampleData(): array
            {
                return ['field' => 'value'];
            }
        };

        // Test with null value
        $result = $component->validate(['field' => null]);
        $this->assertTrue($result->hasErrors());
        $errors = $result->toArray()['errors'];
        $this->assertArrayHasKey('field', $errors);
        $this->assertEquals(['The field field is required.'], $errors['field']);
    }

    public function test_validate_handles_nullable_property_with_null_value(): void
    {
        // Create a component with a nullable property that has additional validation rules
        $component = new class extends UIComponentSchema {
            protected string $type = 'test';
            protected string $component = 'test';
            protected string $version = '1.0.0';

            public function properties(): array
            {
                $property = new Property('email', ['string', 'null']);  
                $property->required()
                    ->addRule('nullable')  
                    ->addRule('email');

                return [$property];
            }

            protected function getExampleData(): array
            {
                return ['email' => 'test@example.com'];
            }
        };

        // Test validation with null value for nullable property
        $result = $component->validate(['email' => null]);
        
        // Should pass validation since the property is nullable
        $this->assertFalse($result->hasErrors());
        $this->assertEmpty($result->toArray()['errors']);

        // Test validation with invalid email to ensure other validations still work
        $result = $component->validate(['email' => 'invalid-email']);
        
        // Should fail validation since the value is not a valid email
        $this->assertTrue($result->hasErrors());
        $this->assertArrayHasKey('email', $result->toArray()['errors']);
    }

    public function test_validate_skips_optional_properties_when_not_present(): void
    {
        // Create a component with both required and optional properties
        $component = new class extends UIComponentSchema {
            protected string $type = 'test';
            protected string $component = 'test';
            protected string $version = '1.0.0';

            public function properties(): array
            {
                $requiredProp = new Property('required_field', 'string');
                $requiredProp->required();

                $optionalProp = new Property('optional_field', 'string');
                // Not marking as required makes it optional

                return [$requiredProp, $optionalProp];
            }

            protected function getExampleData(): array
            {
                return [
                    'required_field' => 'value',
                    'optional_field' => 'optional value'
                ];
            }
        };

        // Test validation with only required field
        $result = $component->validate([
            'required_field' => 'test value'
        ]);

        $this->assertFalse($result->hasErrors());
        $this->assertEmpty($result->toArray()['errors']);
    }
}
