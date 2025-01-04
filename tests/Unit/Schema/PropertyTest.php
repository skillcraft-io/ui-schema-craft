<?php

namespace Skillcraft\UiSchemaCraft\Tests\Unit\Schema;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversClass;
use Skillcraft\UiSchemaCraft\Schema\Property;
use Skillcraft\UiSchemaCraft\Schema\PropertyBuilder;
use Illuminate\Validation\Rule;

#[CoversClass(Property::class)]
class PropertyTest extends TestCase
{
    #[Test]
    public function it_creates_property_with_basic_attributes()
    {
        $property = new Property('test', 'string', 'Test description');
        
        $this->assertEquals('test', $property->getName());
        $this->assertEquals('string', $property->getType());
        $this->assertEquals('Test description', $property->getDescription());
    }

    #[Test]
    public function it_creates_different_property_types()
    {
        $string = Property::string('string_prop');
        $number = Property::number('number_prop');
        $boolean = Property::boolean('bool_prop');
        $object = Property::object('object_prop');
        $array = Property::array('array_prop');
        $timeRange = Property::timeRange('time_prop');
        
        $this->assertEquals('string', $string->getPrimaryType());
        $this->assertEquals('number', $number->getPrimaryType());
        $this->assertEquals('boolean', $boolean->getPrimaryType());
        $this->assertEquals('object', $object->getPrimaryType());
        $this->assertEquals('array', $array->getPrimaryType());
        $this->assertEquals('object', $timeRange->getPrimaryType());
        $this->assertTrue($timeRange->getAttribute('timeRange'));
        
        // Test with custom format
        $customTimeRange = Property::timeRange('custom_time', 'H:i:s');
        $this->assertEquals('H:i:s', $customTimeRange->getFormat());
    }

    #[Test]
    public function it_handles_default_values()
    {
        $property = Property::string('test');
        $this->assertNull($property->getDefault());
        
        $property->setDefault('default value');
        $this->assertEquals('default value', $property->getDefault());
    }

    #[Test]
    public function it_handles_rules()
    {
        $property = Property::string('test');
        
        $property->addRule('required')
                ->addRule(Rule::unique('users', 'email'));
        
        $rules = $property->getRules();
        $this->assertCount(2, $rules);
        $this->assertEquals('required', $rules[0]);
    }

    #[Test]
    public function it_handles_rules_as_string()
    {
        $property = Property::string('test');
        $property->rules('required|email|min:3');
        
        $rules = $property->getRules();
        $this->assertCount(3, $rules);
        $this->assertContains('required', $rules);
        $this->assertContains('email', $rules);
        $this->assertContains('min:3', $rules);

        // Test with array
        $property->rules(['max:255', 'unique:users']);
        $this->assertContains('max:255', $property->getRules());
        $this->assertContains('unique:users', $property->getRules());
    }

    #[Test]
    public function it_handles_numeric_constraints()
    {
        $property = Property::number('age')
            ->min(18)
            ->max(100)
            ->minimum(18)  // alias for min
            ->maximum(100); // alias for max
        
        $array = $property->toArray();
        $this->assertEquals(18, $array['minimum']);
        $this->assertEquals(100, $array['maximum']);
    }

    #[Test]
    public function it_handles_string_constraints()
    {
        $property = Property::string('email')
            ->format('email')
            ->pattern('/^[a-z]+$/')
            ->enum(['a', 'b', 'c']);
        
        $array = $property->toArray();
        $this->assertEquals('email', $array['format']);
        $this->assertEquals('/^[a-z]+$/', $array['pattern']);
        $this->assertEquals(['a', 'b', 'c'], $array['enum']);
    }

    #[Test]
    public function it_handles_required_state()
    {
        $property = Property::string('test')
            ->required();
        
        $array = $property->toArray();
        $this->assertTrue($array['required']);
        $this->assertContains('required', $property->getRules());
        
        // Add multiple required-prefixed rules
        $property->addRule('required_if:other,value')
                ->addRule('required_with:field1')
                ->addRule('required_without:field2');
        
        // Test removing required state
        $property->required(false);
        $array = $property->toArray();
        $this->assertFalse($array['required']);
        
        // Verify all required-prefixed rules are removed
        $rules = $property->getRules();
        foreach ($rules as $rule) {
            $this->assertStringStartsNotWith('required', $rule);
        }
    }

    #[Test]
    public function it_handles_nullable_type()
    {
        $property = Property::string('test')
            ->nullable();
        
        $type = $property->getType();
        $this->assertIsArray($type);
        $this->assertContains('null', $type);
        $this->assertContains('string', $type);
        
        // Test with already array type
        $property = new Property('test', ['string', 'integer']);
        $property->nullable();
        $type = $property->getType();
        $this->assertIsArray($type);
        $this->assertContains('null', $type);
        $this->assertContains('string', $type);
        $this->assertContains('integer', $type);
        
        // Test with already nullable type
        $property->nullable();
        $type = $property->getType();
        $this->assertEquals(1, count(array_filter($type, fn($t) => $t === 'null')));
    }

    #[Test]
    public function it_handles_array_items()
    {
        $itemSchema = Property::string('item');
        $property = Property::array('list')
            ->items($itemSchema);
        
        $array = $property->toArray();
        $this->assertArrayHasKey('items', $array);
        $this->assertEquals('string', $array['items']['type']);
        
        // Test with raw array schema
        $property->items(['type' => 'number']);
        $array = $property->toArray();
        $this->assertEquals('number', $array['items']['type']);
    }

    #[Test]
    public function it_handles_items_with_array()
    {
        $property = Property::array('list');
        
        $property->items(['type' => 'string', 'pattern' => '[a-z]+']);
        
        $array = $property->toArray();
        $this->assertArrayHasKey('items', $array);
        $this->assertEquals('string', $array['items']['type']);
        $this->assertEquals('[a-z]+', $array['items']['pattern']);
    }

    #[Test]
    public function it_handles_object_properties()
    {
        $property = Property::object('user');
        
        // Test with array of properties
        $properties = [
            'name' => Property::string('name')->required(),
            'age' => Property::number('age')
        ];
        
        $property->properties($properties);
        $array = $property->toArray();
        $this->assertArrayHasKey('properties', $array);
        $this->assertTrue($array['properties']['name']['required']);
        
        // Test with PropertyBuilder
        $builder = new PropertyBuilder();
        $builder->string('email')->required();
        
        $property->properties($builder);
        $array = $property->toArray();
        $this->assertArrayHasKey('email', $array['properties']);
        
        // Test with PropertyBuilder containing properties
        $builder = new PropertyBuilder();
        $builder->string('phone')->required();
        $property->properties($builder->toArray());
        $array = $property->toArray();
        $this->assertArrayHasKey('phone', $array['properties']);
        
        // Test invalid type
        $stringProp = Property::string('test');
        $this->expectException(\InvalidArgumentException::class);
        $stringProp->properties([]);
    }

    #[Test]
    public function it_handles_builder_callback()
    {
        $property = Property::object('user')
            ->withBuilder(function(PropertyBuilder $builder) {
                $builder->string('name')->required();
                $builder->number('age');
            });
        
        $array = $property->toArray();
        $this->assertArrayHasKey('properties', $array);
        $this->assertArrayHasKey('name', $array['properties']);
        $this->assertArrayHasKey('age', $array['properties']);
        
        // Test invalid type
        $stringProp = Property::string('test');
        $this->expectException(\InvalidArgumentException::class);
        $stringProp->withBuilder(fn() => null);
    }

    #[Test]
    public function it_handles_references()
    {
        $property = Property::string('test')
            ->reference('#/definitions/Test');
        
        $array = $property->toArray();
        $this->assertEquals('#/definitions/Test', $array['$ref']);
        $this->assertEquals('#/definitions/Test', $property->getReference());
    }

    #[Test]
    public function it_handles_required_rules()
    {
        $property = Property::string('phone');

        // Test requiredWith
        $property->requiredWith(['email', 'name']);
        $array = $property->toArray();
        $this->assertArrayHasKey('conditionalRules', $array);
        $this->assertCount(1, $array['conditionalRules']);
        $this->assertEquals('requiredWith', $array['conditionalRules'][0]['type']);
        $this->assertEquals(['email', 'name'], $array['conditionalRules'][0]['fields']);
        $this->assertEquals('required_with:email,name', $array['rules'][0]);

        // Test requiredWithout
        $property->requiredWithout('email');
        $array = $property->toArray();
        $this->assertEquals('requiredWithout', $array['conditionalRules'][1]['type']);
        $this->assertEquals(['email'], $array['conditionalRules'][1]['fields']);
        $this->assertEquals('required_without:email', $array['rules'][1]);

        // Test requiredIf
        $property->requiredIf('has_phone', true);
        $array = $property->toArray();
        $this->assertEquals('field', $array['conditionalRules'][2]['type']);
        $this->assertEquals('has_phone', $array['conditionalRules'][2]['field']);
        $this->assertEquals(true, $array['conditionalRules'][2]['value']);
        $this->assertEquals('required_if:has_phone,1', $array['rules'][2]);

        // Test prohibitedIf
        $property->prohibitedIf('no_phone', true);
        $array = $property->toArray();
        $this->assertEquals('field', $array['conditionalRules'][3]['type']);
        $this->assertEquals('no_phone', $array['conditionalRules'][3]['field']);
        $this->assertEquals(true, $array['conditionalRules'][3]['value']);
        $this->assertEquals(['prohibited'], $array['conditionalRules'][3]['rules']);
    }

    #[Test]
    public function it_handles_closure_callbacks()
    {
        $property = Property::string('test');
        
        // Test when with callback
        $property->when(function($value) {
            return $value > 10;
        }, ['required', 'string']);
        
        $array = $property->toArray();
        $this->assertArrayHasKey('conditionalRules', $array);
        $this->assertCount(1, $array['conditionalRules']);
        $this->assertEquals('closure', $array['conditionalRules'][0]['type']);
        $this->assertIsCallable($array['conditionalRules'][0]['closure']);
        $this->assertEquals(['required', 'string'], $array['conditionalRules'][0]['rules']);
        
        // Test with string rules
        $property->when(function($value) {
            return $value < 5;
        }, 'required|string');
        
        $array = $property->toArray();
        $this->assertEquals('closure', $array['conditionalRules'][1]['type']);
        $this->assertIsCallable($array['conditionalRules'][1]['closure']);
        $this->assertEquals(['required', 'string'], $array['conditionalRules'][1]['rules']);
    }
    
    #[Test]
    public function it_handles_array_field_callbacks()
    {
        $property = Property::string('field');
        
        $property->when(['status' => 'active', 'type' => 'user'], ['required', 'string']);
        
        $array = $property->toArray();
        $this->assertEquals('array', $array['conditionalRules'][0]['type']);
        $this->assertEquals(['status' => 'active', 'type' => 'user'], $array['conditionalRules'][0]['field']);
        $this->assertEquals(['required', 'string'], $array['conditionalRules'][0]['rules']);
        
        // Test with string rules
        $property->when(['status' => 'active', 'type' => 'user'], 'required|string');
        
        $array = $property->toArray();
        $this->assertEquals('array', $array['conditionalRules'][1]['type']);
        $this->assertEquals(['status' => 'active', 'type' => 'user'], $array['conditionalRules'][1]['field']);
        $this->assertEquals(['required', 'string'], $array['conditionalRules'][1]['rules']);
    }

    #[Test]
    public function it_validates_values()
    {
        // Test string validation
        $stringProp = Property::string('test')->required();
        $this->assertFalse($stringProp->validate(null));
        $this->assertFalse($stringProp->validate(''));
        $this->assertFalse($stringProp->validate(123));
        $this->assertTrue($stringProp->validate('valid string'));
        
        // Test number validation with constraints
        $numberProp = Property::number('age')
            ->min(18)
            ->max(100);
        $this->assertTrue($numberProp->validate(50));
        $this->assertTrue($numberProp->validate(50.5));
        $this->assertFalse($numberProp->validate(10));
        $this->assertFalse($numberProp->validate(150));
        $this->assertFalse($numberProp->validate('not a number'));
        
        // Test number validation without constraints
        $simpleNumberProp = Property::number('simple');
        $this->assertTrue($simpleNumberProp->validate(42));
        $this->assertTrue($simpleNumberProp->validate(3.14));
        $this->assertFalse($simpleNumberProp->validate('string'));
        
        // Test pattern validation
        $patternProp = Property::string('code')
            ->pattern('/^[A-Z]{3}[0-9]{3}$/');
        $this->assertTrue($patternProp->validate('ABC123'));
        $this->assertFalse($patternProp->validate('invalid'));
        
        // Test nullable validation
        $nullableProp = Property::string('optional')->nullable();
        $this->assertTrue($nullableProp->validate(null));
        $this->assertTrue($nullableProp->validate(''));
        $this->assertTrue($nullableProp->validate('valid string'));

        // Test array type validation
        $arrayTypeProp = new Property('test', ['string', 'integer']);
        $this->assertTrue($arrayTypeProp->validate('test'));
        $this->assertTrue($arrayTypeProp->validate(123));
        $this->assertFalse($arrayTypeProp->validate([]));
        
        // Test integer type validation
        $integerProp = new Property('test', 'integer');
        $this->assertTrue($integerProp->validate(42));
        $this->assertFalse($integerProp->validate('string'));
        
        // Test object type validation
        $objectProp = Property::object('test');
        $this->assertTrue($objectProp->validate(new \stdClass()));
        $this->assertTrue($objectProp->validate(['key' => 'value']));
        $this->assertFalse($objectProp->validate('string'));
        
        // Test array type validation
        $arrayProp = Property::array('test');
        $this->assertTrue($arrayProp->validate([]));  // Empty array should be valid
        $this->assertTrue($arrayProp->validate(['item1', 'item2']));
        $this->assertFalse($arrayProp->validate('string'));
        $this->assertFalse($arrayProp->validate(123));
    }

    #[Test]
    public function it_provides_validation_messages()
    {
        $property = Property::string('username')->required();
        $this->assertEquals('The username field is required.', $property->getValidationMessage());
        
        $property = Property::string('email');
        $this->assertEquals('Invalid value for email', $property->getValidationMessage());
    }

    #[Test]
    public function it_handles_name_updates()
    {
        $property = Property::string('old_name');
        $property->setName('new_name');
        
        $this->assertEquals('new_name', $property->getName());
        
        // Test that it's reflected in the array output
        $array = $property->toArray();
        $this->assertEquals('new_name', $array['name']);
    }
    
    #[Test]
    public function it_handles_attributes()
    {
        $property = Property::string('test');
        
        // Test adding and retrieving attributes
        $property->addAttribute('custom', 'value');
        $this->assertEquals('value', $property->getAttribute('custom'));
        
        // Test default value for non-existent attribute
        $this->assertEquals('default', $property->getAttribute('nonexistent', 'default'));
        $this->assertNull($property->getAttribute('nonexistent'));
        
        // Test attributes in array output
        $array = $property->toArray();
        $this->assertEquals('value', $array['custom']);
        
        // Test getAttributes method
        $this->assertEquals(['custom' => 'value'], $property->getAttributes());
    }

    #[Test]
    public function it_handles_when_with_callback()
    {
        $property = Property::string('test');
        
        // Test when with callback
        $property->when(function($value) {
            return $value > 10;
        }, ['required', 'string']);
        
        $array = $property->toArray();
        $this->assertArrayHasKey('conditionalRules', $array);
        $this->assertCount(1, $array['conditionalRules']);
        $this->assertEquals('closure', $array['conditionalRules'][0]['type']);
        $this->assertIsCallable($array['conditionalRules'][0]['closure']);
        $this->assertEquals(['required', 'string'], $array['conditionalRules'][0]['rules']);
    }

    #[Test]
    public function it_validates_edge_cases()
    {
        $property = Property::string('field')->required();
        
        // Test empty string
        $this->assertFalse($property->validate(''));
        
        // Test null
        $this->assertFalse($property->validate(null));
        
        // Test non-required property
        $property->required(false);
        $this->assertTrue($property->validate(''));
        $this->assertTrue($property->validate(null));
        
        // Test invalid type
        $this->assertFalse($property->validate(123));
        
        // Test number property with integer and float
        $numberProperty = Property::number('number');
        $this->assertTrue($numberProperty->validate(123));
        $this->assertTrue($numberProperty->validate(123.45));
        
        // Test object property with array and object
        $objectProperty = Property::object('object');
        $this->assertTrue($objectProperty->validate(['key' => 'value']));
        $this->assertTrue($objectProperty->validate((object)['key' => 'value']));
    }
    
    #[Test]
    public function it_sets_name()
    {
        $property = Property::string('old_name');
        $property->setName('new_name');
        
        $this->assertEquals('new_name', $property->getName());
        
        // Test that it's reflected in the array output
        $array = $property->toArray();
        $this->assertEquals('new_name', $array['name']);
    }
    
    #[Test]
    public function it_handles_string_callbacks()
    {
        $property = Property::string('field');
        
        // Test when with string callback
        $property->when('status', 'active', 'required|string|min:3');
        $array = $property->toArray();
        $this->assertEquals('field', $array['conditionalRules'][0]['type']);
        $this->assertEquals('status', $array['conditionalRules'][0]['field']);
        $this->assertEquals('active', $array['conditionalRules'][0]['value']);
        $this->assertEquals(['required', 'string', 'min:3'], $array['conditionalRules'][0]['rules']);
        
        // Test whenMatches with string callback
        $property->whenMatches('code', '/^[A-Z]+$/', 'required|alpha');
        $array = $property->toArray();
        $this->assertEquals('pattern', $array['conditionalRules'][1]['type']);
        $this->assertEquals('/^[A-Z]+$/', $array['conditionalRules'][1]['value']['pattern']);
        $this->assertEquals(['required', 'alpha'], $array['conditionalRules'][1]['rules']);
        
        // Test whenCompare with string callback
        $property->whenCompare('age', '>', 18, 'required|numeric|min:18');
        $array = $property->toArray();
        $this->assertEquals('comparison', $array['conditionalRules'][2]['type']);
        $this->assertEquals('>', $array['conditionalRules'][2]['value']['operator']);
        $this->assertEquals(18, $array['conditionalRules'][2]['value']['value']);
        $this->assertEquals(['required', 'numeric', 'min:18'], $array['conditionalRules'][2]['rules']);
        
        // Test with null callback
        $property->when('status', 'active');
        $array = $property->toArray();
        $this->assertEquals([], $array['conditionalRules'][3]['rules']);
        
        // Test whenMatches with null callback
        $property->whenMatches('code', '/^[A-Z]+$/');
        $array = $property->toArray();
        $this->assertEquals([], $array['conditionalRules'][4]['rules']);
        
        // Test whenCompare with null callback
        $property->whenCompare('age', '>', 18);
        $array = $property->toArray();
        $this->assertEquals([], $array['conditionalRules'][5]['rules']);
    }

    #[Test]
    public function it_validates_mixed_type_arrays()
    {
        // Test array type with multiple allowed types
        $mixedProp = new Property('test', ['string', 'integer', 'boolean']);
        $this->assertTrue($mixedProp->validate('test'));
        $this->assertTrue($mixedProp->validate(123));
        $this->assertTrue($mixedProp->validate(true));
        $this->assertFalse($mixedProp->validate([]));
        $this->assertFalse($mixedProp->validate(new \stdClass()));

        // Test nullable mixed types
        $nullableMixedProp = new Property('test', ['string', 'integer']);
        $nullableMixedProp->nullable();
        $this->assertTrue($nullableMixedProp->validate(null));
        $this->assertTrue($nullableMixedProp->validate('test'));
        $this->assertTrue($nullableMixedProp->validate(123));
        $this->assertFalse($nullableMixedProp->validate([]));
    }

    #[Test]
    public function it_validates_pattern_with_non_strings()
    {
        $patternProp = Property::string('test')->pattern('/^[0-9]+$/');
        
        // Test with non-string values - type validation should fail first
        $this->assertFalse($patternProp->validate(123));  // Numbers should fail type validation
        $this->assertFalse($patternProp->validate(true));
        $this->assertFalse($patternProp->validate([]));
        $this->assertFalse($patternProp->validate(new \stdClass()));
        $this->assertTrue($patternProp->validate(null));  // Null is allowed for non-required fields

        // Test with string values
        $this->assertTrue($patternProp->validate('123'));
        $this->assertFalse($patternProp->validate('abc'));
    }

    #[Test]
    public function it_validates_boolean_values()
    {
        $boolProp = Property::boolean('test');
        
        // Test with boolean values
        $this->assertTrue($boolProp->validate(true));
        $this->assertTrue($boolProp->validate(false));
        
        // Test with non-boolean values
        $this->assertFalse($boolProp->validate('true'));
        $this->assertFalse($boolProp->validate('false'));
        $this->assertFalse($boolProp->validate(1));
        $this->assertFalse($boolProp->validate(0));
        $this->assertFalse($boolProp->validate([]));
        $this->assertTrue($boolProp->validate(null));  // Null is allowed for non-required fields
        
        // Test with null when required
        $requiredBoolProp = Property::boolean('test')->required();
        $this->assertFalse($requiredBoolProp->validate(null));
    }

    #[Test]
    public function it_validates_object_edge_cases()
    {
        $objectProp = Property::object('test');
        
        // Test with various object-like values
        $this->assertTrue($objectProp->validate(new \stdClass()));
        $this->assertTrue($objectProp->validate(['key' => 'value']));
        $this->assertTrue($objectProp->validate((object)['key' => 'value']));
        
        // Test with non-object values
        $this->assertFalse($objectProp->validate('string'));
        $this->assertFalse($objectProp->validate(123));
        $this->assertFalse($objectProp->validate(true));
        
        // Test with null when nullable
        $nullableObjectProp = Property::object('test')->nullable();
        $this->assertTrue($nullableObjectProp->validate(null));
    }

    #[Test]
    public function it_validates_array_edge_cases()
    {
        $arrayProp = Property::array('test');
        
        // Test with various array values
        $this->assertTrue($arrayProp->validate([]));  // Empty array should be valid
        $this->assertTrue($arrayProp->validate(['item1', 'item2']));
        $this->assertTrue($arrayProp->validate(['key' => 'value']));
        
        // Test with non-array values
        $this->assertFalse($arrayProp->validate('string'));
        $this->assertFalse($arrayProp->validate(123));
        $this->assertFalse($arrayProp->validate(true));
        $this->assertFalse($arrayProp->validate(new \stdClass()));
        
        // Test with null when nullable
        $nullableArrayProp = Property::array('test')->nullable();
        $this->assertTrue($nullableArrayProp->validate(null));
    }

    #[Test]
    public function it_handles_when_with_closure_and_array_rules()
    {
        $property = Property::string('test');
        
        // Test when with closure and array rules
        $property->when(function($value) {
            return $value > 10;
        }, ['required', 'min:10']);

        $array = $property->toArray();
        $this->assertEquals('closure', $array['conditionalRules'][0]['type']);
        $this->assertIsCallable($array['conditionalRules'][0]['closure']);
        $this->assertEquals(['required', 'min:10'], $array['conditionalRules'][0]['rules']);

        // Test when with array field and string rules
        $property->when(['status' => 'active'], 'required|min:3');
        $array = $property->toArray();
        $this->assertEquals('array', $array['conditionalRules'][1]['type']);
        $this->assertEquals(['status' => 'active'], $array['conditionalRules'][1]['field']);
        $this->assertEquals(['required', 'min:3'], $array['conditionalRules'][1]['rules']);

        // Test when with field and array callback
        $property->when('status', 'active', ['required', 'min:3']);
        $array = $property->toArray();
        $this->assertEquals('field', $array['conditionalRules'][2]['type']);
        $this->assertEquals('status', $array['conditionalRules'][2]['field']);
        $this->assertEquals('active', $array['conditionalRules'][2]['value']);
        $this->assertEquals(['required', 'min:3'], $array['conditionalRules'][2]['rules']);
    }

    #[Test]
    public function it_handles_property_builder_with_array()
    {
        $property = Property::object('user');
        
        // Test with PropertyBuilder toArray result
        $builder = new PropertyBuilder();
        $builder->string('name')->required();
        $builder->number('age');
        
        $property->properties($builder->toArray());
        $array = $property->toArray();
        $this->assertArrayHasKey('properties', $array);
        $this->assertArrayHasKey('name', $array['properties']);
        $this->assertArrayHasKey('age', $array['properties']);
    }

    #[Test]
    public function it_validates_empty_values_with_required()
    {
        $property = Property::string('test')->required();
        
        // Test with empty string
        $this->assertFalse($property->validate(''));
        
        // Test with null
        $this->assertFalse($property->validate(null));
        
        // Test with non-empty value
        $this->assertTrue($property->validate('value'));
    }

    #[Test]
    public function it_validates_numeric_values()
    {
        $property = Property::number('test')
            ->min(0)
            ->max(100);
        
        // Test with valid numbers
        $this->assertTrue($property->validate(50));
        $this->assertTrue($property->validate(0));
        $this->assertTrue($property->validate(100));
        
        // Test with invalid numbers
        $this->assertFalse($property->validate(-1));
        $this->assertFalse($property->validate(101));
        
        // Test with non-numeric values
        $this->assertFalse($property->validate('string'));
        $this->assertFalse($property->validate([]));
        $this->assertFalse($property->validate(true));
    }

    #[Test]
    public function it_validates_object_values()
    {
        $property = Property::object('test');
        
        // Test with valid objects
        $this->assertTrue($property->validate(new \stdClass()));
        $this->assertTrue($property->validate(['key' => 'value']));
        
        // Test with invalid values
        $this->assertFalse($property->validate('string'));
        $this->assertFalse($property->validate(123));
        $this->assertFalse($property->validate(true));
    }

    #[Test]
    public function it_validates_array_values()
    {
        $property = Property::array('test');
        
        // Test with valid arrays
        $this->assertTrue($property->validate([]));  // Empty array should be valid
        $this->assertTrue($property->validate(['item']));
        $this->assertTrue($property->validate(['key' => 'value']));
        
        // Test with invalid values
        $this->assertFalse($property->validate('string'));
        $this->assertFalse($property->validate(123));
        $this->assertFalse($property->validate(true));
        $this->assertFalse($property->validate(new \stdClass()));
    }

    #[Test]
    public function it_handles_property_builder_with_properties()
    {
        $property = Property::object('user');
        
        // Test with PropertyBuilder containing properties
        $builder = new PropertyBuilder();
        $builder->string('name')->required();
        $builder->number('age')->min(18);
        
        $property->properties($builder);
        
        $properties = $property->getProperties();
        $this->assertCount(2, $properties);
        $this->assertArrayHasKey('name', $properties);
        $this->assertArrayHasKey('age', $properties);
        
        // Test with invalid property type
        $stringProp = Property::string('test');
        $this->expectException(\InvalidArgumentException::class);
        $stringProp->properties(['name' => []]);
        
        // Test with builder that has 'properties' key
        $builder = new PropertyBuilder();
        $builder->string('email');
        $builderArray = $builder->toArray();
        $builderArray['properties'] = [
            'username' => Property::string('username')->required()->toArray()
        ];
        
        $property = Property::object('user');
        $property->properties($builderArray);
        
        $properties = $property->getProperties();
        $this->assertArrayHasKey('username', $properties);
        $this->assertTrue($properties['username']['required']);
    }

    #[Test]
    public function it_handles_items_with_property_instance()
    {
        $property = Property::array('list');
        $itemSchema = Property::string('item')->required();
        
        $property->items($itemSchema);
        $array = $property->toArray();
        $this->assertArrayHasKey('items', $array);
        $this->assertEquals('string', $array['items']['type']);
        $this->assertTrue($array['items']['required']);
    }

    #[Test]
    public function it_handles_add_property_with_property_instance()
    {
        $property = Property::object('user');
        $nameSchema = Property::string('name')->required();
        
        $property->addProperty('name', $nameSchema);
        $array = $property->toArray();
        $this->assertArrayHasKey('properties', $array);
        $this->assertArrayHasKey('name', $array['properties']);
        $this->assertEquals('string', $array['properties']['name']['type']);
        $this->assertTrue($array['properties']['name']['required']);
    }

    #[Test]
    public function it_handles_enum_values()
    {
        $property = Property::string('status');
        $property->enum(['active', 'inactive', 'pending']);
        
        $array = $property->toArray();
        $this->assertArrayHasKey('enum', $array);
        $this->assertEquals(['active', 'inactive', 'pending'], $array['enum']);
    }

    #[Test]
    public function it_handles_when_with_null_callback()
    {
        $property = Property::string('test');
        
        // Test when with null callback
        $property->when('status', 'active');
        $array = $property->toArray();
        $this->assertEquals('field', $array['conditionalRules'][0]['type']);
        $this->assertEquals('status', $array['conditionalRules'][0]['field']);
        $this->assertEquals('active', $array['conditionalRules'][0]['value']);
        $this->assertEquals([], $array['conditionalRules'][0]['rules']);
    }

    #[Test]
    public function it_handles_when_matches_with_null_callback()
    {
        $property = Property::string('test');
        
        // Test whenMatches with null callback
        $property->whenMatches('code', '/^[A-Z]+$/');
        $array = $property->toArray();
        $this->assertEquals('pattern', $array['conditionalRules'][0]['type']);
        $this->assertEquals('/^[A-Z]+$/', $array['conditionalRules'][0]['value']['pattern']);
        $this->assertEquals([], $array['conditionalRules'][0]['rules']);
    }

    #[Test]
    public function it_handles_when_compare_with_null_callback()
    {
        $property = Property::string('test');
        
        // Test whenCompare with null callback
        $property->whenCompare('age', '>', 18);
        $array = $property->toArray();
        $this->assertEquals('comparison', $array['conditionalRules'][0]['type']);
        $this->assertEquals('>', $array['conditionalRules'][0]['value']['operator']);
        $this->assertEquals(18, $array['conditionalRules'][0]['value']['value']);
        $this->assertEquals([], $array['conditionalRules'][0]['rules']);
    }

    #[Test]
    public function it_handles_required_with_fields()
    {
        $property = Property::string('test');
        
        // Test with string field
        $property->requiredWith(['name']);
        $array = $property->toArray();
        $this->assertEquals('requiredWith', $array['conditionalRules'][0]['type']);
        $this->assertEquals(['name'], $array['conditionalRules'][0]['fields']);
        $this->assertEquals(['required'], $array['conditionalRules'][0]['rules']);
        $this->assertContains('required_with:name', $array['rules']);
        
        // Test with array fields
        $property->requiredWith(['email', 'phone']);
        $array = $property->toArray();
        $this->assertEquals('requiredWith', $array['conditionalRules'][1]['type']);
        $this->assertEquals(['email', 'phone'], $array['conditionalRules'][1]['fields']);
        $this->assertEquals(['required'], $array['conditionalRules'][1]['rules']);
        $this->assertContains('required_with:email,phone', $array['rules']);
    }

    #[Test]
    public function it_handles_required_without_fields()
    {
        $property = Property::string('test');
        
        // Test with string field
        $property->requiredWithout('name');
        $array = $property->toArray();
        $this->assertEquals('requiredWithout', $array['conditionalRules'][0]['type']);
        $this->assertEquals(['name'], $array['conditionalRules'][0]['fields']);
        $this->assertEquals(['required'], $array['conditionalRules'][0]['rules']);
        $this->assertContains('required_without:name', $array['rules']);
        
        // Test with array fields
        $property->requiredWithout(['email', 'phone']);
        $array = $property->toArray();
        $this->assertEquals('requiredWithout', $array['conditionalRules'][1]['type']);
        $this->assertEquals(['email', 'phone'], $array['conditionalRules'][1]['fields']);
        $this->assertEquals(['required'], $array['conditionalRules'][1]['rules']);
        $this->assertContains('required_without:email,phone', $array['rules']);
    }

    #[Test]
    public function it_handles_required_if_field()
    {
        $property = Property::string('test');
        
        $property->requiredIf('status', 'active');
        $array = $property->toArray();
        $this->assertEquals('field', $array['conditionalRules'][0]['type']);
        $this->assertEquals('status', $array['conditionalRules'][0]['field']);
        $this->assertEquals('active', $array['conditionalRules'][0]['value']);
        $this->assertEquals(['required'], $array['conditionalRules'][0]['rules']);
        $this->assertContains('required_if:status,active', $array['rules']);
    }

    #[Test]
    public function it_handles_prohibited_if_field()
    {
        $property = Property::string('test');
        
        $property->prohibitedIf('status', 'inactive');
        $array = $property->toArray();
        $this->assertEquals('field', $array['conditionalRules'][0]['type']);
        $this->assertEquals('status', $array['conditionalRules'][0]['field']);
        $this->assertEquals('inactive', $array['conditionalRules'][0]['value']);
        $this->assertEquals(['prohibited'], $array['conditionalRules'][0]['rules']);
    }

    #[Test]
    public function it_handles_name_update()
    {
        $property = Property::string('test');
        $this->assertEquals('test', $property->getName());
        
        $property->setName('updated');
        $this->assertEquals('updated', $property->getName());
    }

    #[Test]
    public function it_handles_validation_messages()
    {
        $property = Property::string('username')->required();
        $this->assertEquals('The username field is required.', $property->getValidationMessage());
        
        $property = Property::string('email');
        $this->assertEquals('Invalid value for email', $property->getValidationMessage());
    }

    #[Test]
    public function it_handles_description_update()
    {
        $property = Property::string('test');
        $this->assertNull($property->getDescription());

        $property->description('New description');
        $this->assertEquals('New description', $property->getDescription());

        $property->description(null);
        $this->assertNull($property->getDescription());

        $array = $property->toArray();
        $this->assertArrayNotHasKey('description', $array);
    }
    
    #[Test]
    public function it_handles_time_range()
    {
        // Test with default format
        $property = Property::timeRange('date_range');
        $array = $property->toArray();
        $this->assertEquals(['object', 'null'], $array['type']);
        $this->assertTrue($array['timeRange']);
        $this->assertEquals('Y-m-d', $array['format']);
        
        // Test with custom format
        $property = Property::timeRange('time_range', 'H:i:s');
        $array = $property->toArray();
        $this->assertEquals('H:i:s', $array['format']);
    }

    #[Test]
    public function it_handles_with_builder()
    {
        $property = Property::object('user');
        
        $property->withBuilder(function(PropertyBuilder $builder) {
            $builder->string('name')->required();
            $builder->number('age')->min(18);
            $builder->boolean('active');
        });
        
        $array = $property->toArray();
        $this->assertArrayHasKey('properties', $array);
        $this->assertArrayHasKey('name', $array['properties']);
        $this->assertArrayHasKey('age', $array['properties']);
        $this->assertArrayHasKey('active', $array['properties']);
        
        // Test with non-object type
        $stringProp = Property::string('test');
        $this->expectException(\InvalidArgumentException::class);
        $stringProp->withBuilder(fn() => null);
    }

    #[Test]
    public function it_handles_when_matches_with_array_callback()
    {
        $property = Property::string('field');
        
        // Test whenMatches with array callback
        $property->whenMatches('code', '/^[A-Z]+$/', ['required', 'alpha']);
        $array = $property->toArray();
        $this->assertEquals('pattern', $array['conditionalRules'][0]['type']);
        $this->assertEquals('code', $array['conditionalRules'][0]['field']);
        $this->assertEquals('/^[A-Z]+$/', $array['conditionalRules'][0]['value']['pattern']);
        $this->assertEquals(['required', 'alpha'], $array['conditionalRules'][0]['rules']);

        // Test whenMatches with string callback
        $property->whenMatches('code', '/^[0-9]+$/', 'required|numeric');
        $array = $property->toArray();
        $this->assertEquals(['required', 'numeric'], $array['conditionalRules'][1]['rules']);

        // Test whenMatches with null callback
        $property->whenMatches('code', '/^[a-z]+$/');
        $array = $property->toArray();
        $this->assertEquals([], $array['conditionalRules'][2]['rules']);
    }

    #[Test]
    public function it_handles_when_compare_with_array_callback()
    {
        $property = Property::string('field');
        
        // Test whenCompare with array callback
        $property->whenCompare('age', '>', 18, ['required', 'string']);
        $array = $property->toArray();
        $this->assertEquals('comparison', $array['conditionalRules'][0]['type']);
        $this->assertEquals('age', $array['conditionalRules'][0]['field']);
        $this->assertEquals('>', $array['conditionalRules'][0]['value']['operator']);
        $this->assertEquals(18, $array['conditionalRules'][0]['value']['value']);
        $this->assertEquals(['required', 'string'], $array['conditionalRules'][0]['rules']);

        // Test whenCompare with string callback
        $property->whenCompare('count', '<', 10, 'required|numeric');
        $array = $property->toArray();
        $this->assertEquals(['required', 'numeric'], $array['conditionalRules'][1]['rules']);

        // Test whenCompare with null callback
        $property->whenCompare('value', '=', 'test');
        $array = $property->toArray();
        $this->assertEquals([], $array['conditionalRules'][2]['rules']);
    }

    #[Test]
    public function it_handles_get_properties()
    {
        $property = Property::object('user');
        
        // Test empty properties
        $this->assertEmpty($property->getProperties());
        
        // Add properties and test retrieval
        $property->addProperty('name', Property::string('name')->required());
        $property->addProperty('age', Property::number('age')->min(18));
        
        $properties = $property->getProperties();
        $this->assertCount(2, $properties);
        $this->assertArrayHasKey('name', $properties);
        $this->assertArrayHasKey('age', $properties);
        
        // Verify property details
        $nameProperty = $properties['name'];
        $ageProperty = $properties['age'];
        
        $this->assertInstanceOf(Property::class, $nameProperty);
        $this->assertInstanceOf(Property::class, $ageProperty);
        
        $nameArray = $nameProperty->toArray();
        $ageArray = $ageProperty->toArray();
        
        $this->assertEquals('string', $nameArray['type']);
        $this->assertTrue($nameArray['required']);
        $this->assertEquals('number', $ageArray['type']);
        $this->assertEquals(18, $ageArray['minimum']);
    }

    #[Test]
    public function it_handles_get_items()
    {
        // Test array property with no items
        $property = Property::array('tags');
        $this->assertEmpty($property->getItems());
        
        // Test array property with string items
        $property->items(Property::string('tag')->required());
        $items = $property->getItems();
        $this->assertNotNull($items);
        $this->assertEquals('string', $items['type']);
        $this->assertTrue($items['required']);
        
        // Test array property with object items
        $property = Property::array('users');
        $property->items(
            Property::object('user')
                ->addProperty('name', Property::string('name')->required())
                ->addProperty('age', Property::number('age')->min(18))
        );
        
        $items = $property->getItems();
        $this->assertEquals('object', $items['type']);
        $this->assertArrayHasKey('properties', $items);
        $this->assertArrayHasKey('name', $items['properties']);
        $this->assertArrayHasKey('age', $items['properties']);
        $this->assertTrue($items['properties']['name']['required']);
        $this->assertEquals(18, $items['properties']['age']['minimum']);
    }

    #[Test]
    public function it_handles_pattern()
    {
        $property = Property::string('test');
        
        // Test with no pattern set
        $this->assertNull($property->getPattern());
        
        // Test with email pattern
        $property->pattern('^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$');
        $this->assertEquals('^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$', $property->getPattern());
        
        // Test with phone number pattern
        $property->pattern('^\+?[1-9]\d{1,14}$');
        $this->assertEquals('^\+?[1-9]\d{1,14}$', $property->getPattern());
        
        // Verify pattern in array output
        $array = $property->toArray();
        $this->assertEquals('^\+?[1-9]\d{1,14}$', $array['pattern']);
    }

    #[Test]
    public function it_handles_properties_with_builder()
    {
        $property = Property::object('user');
        
        // Test with PropertyBuilder
        $builder = new PropertyBuilder();
        $builder->string('name')->required();
        $builder->number('age')->min(18);
        
        $property->properties($builder);
        
        $properties = $property->getProperties();
        $this->assertCount(2, $properties);
        $this->assertArrayHasKey('name', $properties);
        $this->assertArrayHasKey('age', $properties);
        
        // Test with non-object type
        $stringProp = Property::string('test');
        $this->expectException(\InvalidArgumentException::class);
        $stringProp->properties(['name' => []]);
        
        // Test with builder that has 'properties' key
        $builder = new PropertyBuilder();
        $builder->string('email');
        $builderArray = $builder->toArray();
        $builderArray['properties'] = [
            'username' => Property::string('username')->required()->toArray()
        ];
        
        $property = Property::object('user');
        $property->properties($builderArray);
        
        $properties = $property->getProperties();
        $this->assertArrayHasKey('username', $properties);
        $this->assertTrue($properties['username']['required']);
    }

    #[Test]
    public function it_handles_properties_method()
    {
        // Test with array of properties
        $property = Property::object('user');
        $property->properties([
            'name' => Property::string('name')->required()->toArray(),
            'age' => Property::number('age')->min(18)->toArray()
        ]);
        
        $properties = $property->getProperties();
        $this->assertCount(2, $properties);
        $this->assertArrayHasKey('name', $properties);
        $this->assertArrayHasKey('age', $properties);
        $this->assertTrue($properties['name']['required']);
        $this->assertEquals(18, $properties['age']['minimum']);
        
        // Test with PropertyBuilder
        $builder = new PropertyBuilder();
        $builder->add(Property::string('email')->required());
        $builder->add(Property::boolean('active')->setDefault(true));
        
        $property = Property::object('user');
        $property->properties($builder);
        
        $properties = $property->getProperties();
        $this->assertCount(2, $properties);
        $this->assertArrayHasKey('email', $properties);
        $this->assertArrayHasKey('active', $properties);
        $this->assertTrue($properties['email']['required']);
        $this->assertTrue($properties['active']['default']);
        
        // Test with non-object type should be last
        $stringProp = Property::string('test');
        $this->expectException(\InvalidArgumentException::class);
        $stringProp->properties(['name' => []]);
    }

    #[Test]
    public function it_validates_pattern_with_non_string_values()
    {
        $property = Property::string('test')
            ->pattern('/^[a-z]+$/');
        
        // Non-string values should not be validated against pattern
        $this->assertFalse($property->validate(123));
        $this->assertFalse($property->validate(true));
        $this->assertFalse($property->validate([]));
        $this->assertFalse($property->validate(new \stdClass()));
    }

    #[Test]
    public function it_validates_numeric_values_with_constraints()
    {
        $property = Property::number('test')
            ->minimum(0)
            ->maximum(100);
        
        // Test with non-numeric values
        $this->assertFalse($property->validate('string'));
        $this->assertFalse($property->validate(true));
        $this->assertFalse($property->validate([]));
        $this->assertFalse($property->validate(new \stdClass()));
        
        // Test with numeric values
        $this->assertTrue($property->validate(50));
        $this->assertTrue($property->validate(0));
        $this->assertTrue($property->validate(100));
        $this->assertFalse($property->validate(-1));
        $this->assertFalse($property->validate(101));
        
        // Test with numeric strings (should fail as we want strict type checking)
        $this->assertFalse($property->validate('50'));
        $this->assertFalse($property->validate('0'));
        $this->assertFalse($property->validate('100'));
    }

    #[Test]
    public function it_checks_nullable_state()
    {
        $property = Property::string('test');
        
        // Initially not nullable
        $this->assertFalse($property->isNullable());
        
        // After making nullable
        $property->nullable();
        $this->assertTrue($property->isNullable());
        
        // Test with array type
        $arrayProperty = new Property('test', ['string', 'integer']);
        $this->assertFalse($arrayProperty->isNullable());
        $arrayProperty->nullable();
        $this->assertTrue($arrayProperty->isNullable());
        
        // Test with already nullable array type
        $arrayProperty->nullable(); // Call nullable again
        $this->assertTrue($arrayProperty->isNullable());
        
        // Test with already nullable string type
        $property->nullable(); // Call nullable again
        $this->assertTrue($property->isNullable());
    }

    #[Test]
    public function it_handles_properties_with_invalid_type()
    {
        $property = Property::string('test');
        
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Properties can only be set on object type');
        
        $property->properties(['name' => Property::string('name')]);
    }

    #[Test]
    public function it_validates_empty_value_with_not_required()
    {
        $property = Property::string('test');
        $property->required(false);
        
        $this->assertTrue($property->validate(''));
        $this->assertTrue($property->validate(null));
    }

    #[Test]
    public function it_handles_required_state_changes()
    {
        $property = Property::string('test');
        
        // Initially not required
        $this->assertFalse($property->toArray()['required']);
        
        // Make required
        $property->required();
        $this->assertTrue($property->toArray()['required']);
        $this->assertContains('required', $property->getRules());
        
        // Make not required
        $property->required(false);
        $this->assertFalse($property->toArray()['required']);
        $this->assertNotContains('required', $property->getRules());
    }

    #[Test]
    public function it_validates_required_with_fields()
    {
        $property = Property::string('test');
        
        // Test with single field
        $property->requiredWith('name');
        
        // Should pass when neither field is present
        $this->assertTrue($property->validate(null));
        
        // Should fail when the required field is present but this field is empty
        $property->addAttribute('_context', ['name' => 'John']);
        $this->assertFalse($property->validate(null));
        $this->assertFalse($property->validate(''));
        
        // Should pass when both fields have values
        $this->assertTrue($property->validate('test value'));
        
        // Test with multiple fields
        $property = Property::string('test');
        $property->requiredWith(['email', 'phone']);
        
        // Should pass when no fields are present
        $this->assertTrue($property->validate(null));
        
        // Should fail when any required field is present but this field is empty
        $property->addAttribute('_context', ['email' => 'test@example.com']);
        $this->assertFalse($property->validate(null));
        
        $property->addAttribute('_context', ['phone' => '1234567890']);
        $this->assertFalse($property->validate(null));
        
        // Should pass when this field has a value
        $this->assertTrue($property->validate('test value'));
        
        // Should pass when required fields are not present
        $property->addAttribute('_context', ['other' => 'value']);
        $this->assertTrue($property->validate(null));
    }

    #[Test]
    public function it_configures_required_with_fields()
    {
        $property = Property::string('test');
        
        // Test with single field as string
        $property->requiredWith('name');
        $array = $property->toArray();
        $this->assertEquals('requiredWith', $array['conditionalRules'][0]['type']);
        $this->assertEquals(['name'], $array['conditionalRules'][0]['fields']);
        $this->assertEquals(['required'], $array['conditionalRules'][0]['rules']);
        $this->assertContains('required_with:name', $property->getRules());
        
        // Test with multiple fields
        $property->requiredWith(['email', 'phone']);
        $array = $property->toArray();
        $this->assertEquals('requiredWith', $array['conditionalRules'][1]['type']);
        $this->assertEquals(['email', 'phone'], $array['conditionalRules'][1]['fields']);
        $this->assertEquals(['required'], $array['conditionalRules'][1]['rules']);
        $this->assertContains('required_with:email,phone', $property->getRules());
    }

    #[Test]
    public function it_converts_property_to_array_with_all_configurations()
    {
        // Create a property with all possible configurations
        $property = Property::string('test')
            ->description('Test description')
            ->default('default value')
            ->format('email')
            ->pattern('/^[a-z]+$/')
            ->reference('#/definitions/test')
            ->rules('required|email')
            ->addAttribute('customKey', 'customValue')
            ->nullable()
            ->required();
            
        // Add a nested property
        $nestedProperty = Property::number('age')
            ->minimum(18)
            ->maximum(100);
        $property->addProperty('nested', $nestedProperty);
        
        // Add array items
        $itemProperty = Property::string('item')
            ->pattern('/^[A-Z]+$/');
        $itemArray = $itemProperty->toArray();
        $property->items($itemArray);
        
        // Add conditional rules
        $property->requiredWith('name')
            ->requiredWithout('email')
            ->requiredIf('type', 'personal')
            ->prohibitedIf('type', 'business');
            
        // Convert to array and verify all fields
        $array = $property->toArray();
        
        // Basic properties
        $this->assertEquals('test', $array['name']);
        $this->assertEquals(['null', 'string'], $array['type']);
        $this->assertEquals('Test description', $array['description']);
        $this->assertEquals('default value', $array['default']);
        $this->assertEquals('email', $array['format']);
        $this->assertEquals('/^[a-z]+$/', $array['pattern']);
        $this->assertEquals('#/definitions/test', $array['$ref']);
        $this->assertCount(6, $array['rules']);
        $this->assertContains('required', $array['rules']);
        $this->assertContains('email', $array['rules']);
        $this->assertContains('nullable', $array['rules']);
        $this->assertContains('required_with:name', $array['rules']);
        $this->assertContains('required_without:email', $array['rules']);
        $this->assertContains('required_if:type,personal', $array['rules']);
        $this->assertEquals('customValue', $array['customKey']);
        $this->assertTrue($array['required']);
        
        // Nested properties
        $this->assertArrayHasKey('properties', $array);
        $this->assertArrayHasKey('nested', $array['properties']);
        $nestedArray = $array['properties']['nested'];
        $this->assertEquals('age', $nestedArray['name']);
        $this->assertEquals('number', $nestedArray['type']);
        $this->assertEquals(18, $nestedArray['minimum']);
        $this->assertEquals(100, $nestedArray['maximum']);
        
        // Array items
        $this->assertArrayHasKey('items', $array);
        $itemsArray = $array['items'];
        $this->assertEquals('item', $itemsArray['name']);
        $this->assertEquals('string', $itemsArray['type']);
        $this->assertEquals('/^[A-Z]+$/', $itemsArray['pattern']);
        
        // Conditional rules
        $this->assertArrayHasKey('conditionalRules', $array);
        $this->assertCount(4, $array['conditionalRules']);
        
        // Required with rule
        $requiredWithRule = $array['conditionalRules'][0];
        $this->assertEquals('requiredWith', $requiredWithRule['type']);
        $this->assertEquals(['name'], $requiredWithRule['fields']);
        
        // Required without rule
        $requiredWithoutRule = $array['conditionalRules'][1];
        $this->assertEquals('requiredWithout', $requiredWithoutRule['type']);
        $this->assertEquals(['email'], $requiredWithoutRule['fields']);
        
        // Required if rule
        $requiredIfRule = $array['conditionalRules'][2];
        $this->assertEquals('field', $requiredIfRule['type']);
        $this->assertEquals('type', $requiredIfRule['field']);
        $this->assertEquals('personal', $requiredIfRule['value']);
        
        // Prohibited if rule
        $prohibitedIfRule = $array['conditionalRules'][3];
        $this->assertEquals('field', $prohibitedIfRule['type']);
        $this->assertEquals('type', $prohibitedIfRule['field']);
        $this->assertEquals('business', $prohibitedIfRule['value']);
    }
    
    #[Test]
    public function it_validates_conditional_rules()
    {
        // Test requiredWith validation
        $property = Property::string('test')
            ->requiredWith('name');

        // When required field is present
        $property->addAttribute('_context', ['name' => 'John']);
        $this->assertFalse($property->validate(null));
        $this->assertFalse($property->validate(''));
        $this->assertTrue($property->validate('value'));

        // When required field is missing
        $property->addAttribute('_context', ['other' => 'value']);
        $this->assertTrue($property->validate(null));
        $this->assertTrue($property->validate(''));
        $this->assertTrue($property->validate('value'));

        // When required field is null or empty
        $property->addAttribute('_context', ['name' => null]);
        $this->assertTrue($property->validate(null));
        $property->addAttribute('_context', ['name' => '']);
        $this->assertTrue($property->validate(null));

        // Test requiredWithout validation
        $property = Property::string('test')
            ->requiredWithout('email');

        // When field is present
        $property->addAttribute('_context', ['email' => 'test@example.com']);
        $this->assertTrue($property->validate(null));
        $this->assertTrue($property->validate(''));
        $this->assertTrue($property->validate('value'));

        // When field is missing
        $property->addAttribute('_context', ['other' => 'value']);
        $this->assertFalse($property->validate(null));
        $this->assertFalse($property->validate(''));
        $this->assertTrue($property->validate('value'));

        // When field is null or empty
        $property->addAttribute('_context', ['email' => null]);
        $this->assertFalse($property->validate(null));
        $property->addAttribute('_context', ['email' => '']);
        $this->assertFalse($property->validate(null));

        // Test multiple fields
        $property = Property::string('test')
            ->requiredWith(['name', 'age']);

        // When any required field is present
        $property->addAttribute('_context', ['name' => 'John']);
        $this->assertFalse($property->validate(null));
        $property->addAttribute('_context', ['age' => 25]);
        $this->assertFalse($property->validate(null));
        $property->addAttribute('_context', ['name' => 'John', 'age' => 25]);
        $this->assertFalse($property->validate(null));

        // When no required fields are present
        $property->addAttribute('_context', ['other' => 'value']);
        $this->assertTrue($property->validate(null));

        $property = Property::string('test')
            ->requiredWithout(['email', 'phone']);

        // When any field is present
        $property->addAttribute('_context', ['email' => 'test@example.com']);
        $this->assertTrue($property->validate(null));
        $property->addAttribute('_context', ['phone' => '1234567890']);
        $this->assertTrue($property->validate(null));
        $property->addAttribute('_context', ['email' => 'test@example.com', 'phone' => '1234567890']);
        $this->assertTrue($property->validate(null));

        // When all fields are missing
        $property->addAttribute('_context', ['other' => 'value']);
        $this->assertFalse($property->validate(null));
    }

    #[Test]
    public function it_handles_time_properties()
    {
        // Test time range property
        $timeRange = Property::timeRange('meeting_time', 'Meeting Time Range');
        $array = $timeRange->toArray();
        $this->assertEquals(['object', 'null'], $array['type']);
        $this->assertEquals('Meeting Time Range', $array['description']);
        $this->assertArrayHasKey('properties', $array);
        $this->assertEquals(['type' => 'string', 'format' => 'date-time'], $array['properties']['start']);
        $this->assertEquals(['type' => 'string', 'format' => 'date-time'], $array['properties']['end']);

        // Test time property
        $time = Property::time('start_time', 'Start Time');
        $array = $time->toArray();
        $this->assertEquals('string', $array['type']);
        $this->assertEquals('Start Time', $array['description']);
        $this->assertEquals('time', $array['format']);

        // Test time property with default description
        $time = Property::time('start_time');
        $array = $time->toArray();
        $this->assertEquals('Start Time', $array['description']);

        // Test duration property
        $duration = Property::duration('event_duration', 'Event Duration');
        $array = $duration->toArray();
        $this->assertEquals('object', $array['type']);
        $this->assertEquals('Event Duration', $array['description']);
        $this->assertArrayHasKey('properties', $array);
        $this->assertEquals(['type' => 'number'], $array['properties']['value']);
        $this->assertEquals(
            ['type' => 'string', 'enum' => ['seconds', 'minutes', 'hours', 'days', 'weeks', 'months', 'years']], 
            $array['properties']['unit']
        );

        // Test duration property with default description
        $duration = Property::duration('event_duration');
        $array = $duration->toArray();
        $this->assertEquals('Event Duration', $array['description']);

        // Test date range property
        $dateRange = Property::dateRange('booking_period', 'Booking Period');
        $array = $dateRange->toArray();
        $this->assertEquals('object', $array['type']);
        $this->assertEquals('Booking Period', $array['description']);
        
        // Check start and end properties
        $this->assertArrayHasKey('properties', $array);
        $properties = $array['properties'];
        $this->assertEquals('string', $properties['start']['type']);
        $this->assertEquals('date', $properties['start']['format']);
        $this->assertEquals('Start date', $properties['start']['description']);
        $this->assertEquals('string', $properties['end']['type']);
        $this->assertEquals('date', $properties['end']['format']);
        $this->assertEquals('End date', $properties['end']['description']);

        // Check options property
        $this->assertArrayHasKey('options', $properties);
        $options = $properties['options']['properties'];
        $this->assertEquals(['type' => 'string', 'format' => 'date'], $options['minDate']);
        $this->assertEquals(['type' => 'string', 'format' => 'date'], $options['maxDate']);
        $this->assertEquals(['type' => 'array', 'items' => ['type' => 'string', 'format' => 'date']], $options['disabledDates']);
        $this->assertEquals(['type' => 'string', 'default' => 'YYYY-MM-DD'], $options['format']);
        $this->assertEquals(['type' => 'boolean', 'default' => true], $options['shortcuts']);
        $this->assertEquals(['type' => 'boolean', 'default' => false], $options['weekNumbers']);
        $this->assertEquals(['type' => 'boolean', 'default' => true], $options['monthSelector']);
        $this->assertEquals(['type' => 'boolean', 'default' => true], $options['yearSelector']);

        // Test date range property with default description
        $dateRange = Property::dateRange('booking_period');
        $array = $dateRange->toArray();
        $this->assertEquals('Booking Period', $array['description']);
    }
}
