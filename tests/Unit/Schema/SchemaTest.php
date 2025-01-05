<?php

namespace Skillcraft\UiSchemaCraft\Tests\Unit\Schema;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversClass;
use Skillcraft\UiSchemaCraft\Tests\TestCase;
use Skillcraft\UiSchemaCraft\Schema\Schema;
use Skillcraft\UiSchemaCraft\Facades\PropertyBuilder;
use Skillcraft\UiSchemaCraft\Schema\Property;
use Illuminate\Support\Facades\Validator;

#[CoversClass(Schema::class)]
class SchemaTest extends TestCase
{
    #[Test]
    public function it_adds_property()
    {
        $schema = new Schema();
        $property = PropertyBuilder::string('name')->required();
        
        $schema->addProperty($property);
        
        $properties = $schema->getProperties();
        $this->assertArrayHasKey('name', $properties);
        $this->assertEquals('string', $properties['name']['type']);
    }

    #[Test]
    public function it_adds_validation_rules()
    {
        $schema = new Schema();
        $property = PropertyBuilder::string('email')
            ->required()
            ->rules('email');
        
        $schema->addProperty($property);
        
        $rules = $schema->getRules();
        $this->assertArrayHasKey('email', $rules);
        $this->assertContains('required', $rules['email']);
        $this->assertContains('email', $rules['email']);
    }

    #[Test]
    public function it_validates_data()
    {
        $schema = new Schema();
        $property = PropertyBuilder::string('email')
            ->required()
            ->rules('email');
        
        $schema->addProperty($property);
        
        $result = $schema->validate(['email' => 'invalid']);
        $this->assertFalse($result['valid']);
        $this->assertArrayHasKey('email', $result['errors']);

        $result = $schema->validate(['email' => 'test@example.com']);
        $this->assertTrue($result['valid']);
        $this->assertEmpty($result['errors']);
    }

    #[Test]
    public function it_handles_custom_messages()
    {
        $schema = new Schema();
        $property = PropertyBuilder::string('age')
            ->required()
            ->rules('numeric|min:18');
        
        $schema->addProperty($property)
            ->withMessages([
                'age.required' => 'Age is required',
                'age.min' => 'Must be at least 18 years old'
            ]);
        
        $result = $schema->validate(['age' => 16]);
        $this->assertFalse($result['valid']);
        $this->assertEquals('Must be at least 18 years old', $result['errors']['age'][0]);
    }

    #[Test]
    public function it_handles_custom_attributes()
    {
        $schema = new Schema();
        $property = PropertyBuilder::string('dob')
            ->required()
            ->rules('date');
        
        $schema->addProperty($property)
            ->withAttributes([
                'dob' => 'date of birth'
            ]);
        
        $result = $schema->validate(['dob' => 'invalid']);
        $this->assertFalse($result['valid']);
        $this->assertStringContainsString('date of birth', $result['errors']['dob'][0]);
    }

    #[Test]
    public function it_processes_field_conditional_validation()
    {
        $schema = new Schema();
        $property = PropertyBuilder::string('tax_id')
            ->when('is_business', true, ['required', 'digits:9']);
        
        $schema->addProperty($property);
        
        $result = $schema->validate([
            'is_business' => true,
            'tax_id' => ''
        ]);
        $this->assertFalse($result['valid']);

        $result = $schema->validate([
            'is_business' => false,
            'tax_id' => ''
        ]);
        $this->assertTrue($result['valid']);
    }

    #[Test]
    public function it_processes_array_conditional_validation()
    {
        $property = PropertyBuilder::string('email')
            ->required()
            ->when([
                'role' => 'admin',
                'status' => 'active'
            ], ['unique:users,email']);

        $schema = $property->toArray();
        $this->assertTrue($schema['required']);
        $this->assertArrayHasKey('conditionalRules', $schema);
        $this->assertCount(1, $schema['conditionalRules']);

        $rule = $schema['conditionalRules'][0];
        $this->assertEquals('array', $rule['type']);
        $this->assertEquals(['role' => 'admin', 'status' => 'active'], $rule['field']);
        $this->assertEquals(['unique:users,email'], $rule['rules']);
    }

    #[Test]
    public function it_processes_pattern_conditional_validation()
    {
        $schema = new Schema();
        $property = PropertyBuilder::string('vat_number')
            ->whenMatches('country_code', '/^EU/', ['required']);
        
        $schema->addProperty($property);
        
        $result = $schema->validate([
            'country_code' => 'EU123',
            'vat_number' => ''
        ]);
        $this->assertFalse($result['valid']);

        $result = $schema->validate([
            'country_code' => 'US123',
            'vat_number' => ''
        ]);
        $this->assertTrue($result['valid']);
    }

    #[Test]
    public function it_processes_comparison_conditional_validation()
    {
        $schema = new Schema();
        $property = PropertyBuilder::string('guardian_consent')
            ->whenCompare('age', '<', 18, ['required']);
        
        $schema->addProperty($property);
        
        $result = $schema->validate([
            'age' => 16,
            'guardian_consent' => ''
        ]);
        $this->assertFalse($result['valid']);

        $result = $schema->validate([
            'age' => 20,
            'guardian_consent' => ''
        ]);
        $this->assertTrue($result['valid']);
    }

    #[Test]
    public function it_processes_closure_conditional_validation()
    {
        $closure = function($value) {
            return $value > 10;
        };

        $property = PropertyBuilder::string('quantity')
            ->required()
            ->when($closure, ['min:10']);

        $schema = $property->toArray();
        $this->assertTrue($schema['required']);
        $this->assertArrayHasKey('conditionalRules', $schema);
        $this->assertCount(1, $schema['conditionalRules']);

        $rule = $schema['conditionalRules'][0];
        $this->assertEquals('closure', $rule['type']);
        $this->assertInstanceOf(\Closure::class, $rule['closure']);
        $this->assertEquals(['min:10'], $rule['rules']);
    }

    #[Test]
    public function it_validates_empty_schema()
    {
        $schema = new Schema();
        $result = $schema->validate([]);
        
        $this->assertTrue($result['valid']);
        $this->assertEmpty($result['errors']);
    }

    #[Test]
    public function it_handles_multiple_properties()
    {
        $schema = new Schema();
        $schema->addProperty(PropertyBuilder::string('name')->required())
               ->addProperty(PropertyBuilder::string('email')->required()->rules('email'))
               ->addProperty(PropertyBuilder::number('age')->rules('min:18'));
        
        $result = $schema->validate([
            'name' => 'John Doe',
            'email' => 'invalid-email',
            'age' => 16
        ]);
        
        $this->assertFalse($result['valid']);
        $this->assertArrayHasKey('email', $result['errors']);
        $this->assertArrayHasKey('age', $result['errors']);
    }

    #[Test]
    public function it_handles_invalid_comparison_operator()
    {
        $schema = new Schema();
        $property = PropertyBuilder::string('field')
            ->whenCompare('value', 'invalid_operator', 10, ['required']);
        
        $schema->addProperty($property);
        
        $result = $schema->validate([
            'value' => 5,
            'field' => ''
        ]);
        
        $this->assertTrue($result['valid']);
    }

    #[Test]
    public function it_allows_method_chaining()
    {
        $schema = new Schema();
        $result = $schema->addProperty(PropertyBuilder::string('name')->required())
                        ->withMessages(['name.required' => 'Name is required'])
                        ->withAttributes(['name' => 'Full Name']);
        
        $this->assertInstanceOf(Schema::class, $result);
    }

    #[Test]
    public function it_formats_error_messages_correctly()
    {
        $schema = new Schema();
        $schema->addProperty(PropertyBuilder::string('email')->required()->rules('email'))
               ->withMessages([
                   'email.required' => 'Email is mandatory',
                   'email.email' => 'Must be a valid email address'
               ]);
        
        $result = $schema->validate(['email' => 'not-an-email']);
        
        $this->assertFalse($result['valid']);
        $this->assertStringContainsString('valid email address', $result['errors']['email'][0]);
    }

    #[Test]
    public function it_converts_to_array_format()
    {
        $schema = new Schema();
        $schema->addProperty(PropertyBuilder::string('name')->required())
               ->addProperty(PropertyBuilder::string('email'));
        
        $array = $schema->toArray();
        
        $this->assertEquals('object', $array['type']);
        $this->assertArrayHasKey('properties', $array);
        $this->assertArrayHasKey('required', $array);
        $this->assertContains('name', $array['required']);
        $this->assertNotContains('email', $array['required']);
    }

    #[Test]
    public function it_gets_and_sets_messages()
    {
        $schema = new Schema();
        $messages = ['field.required' => 'This field is required'];
        
        $schema->setMessages($messages);
        $this->assertEquals($messages, $schema->getMessages());
        
        $additionalMessages = ['field.email' => 'Must be valid email'];
        $schema->withMessages($additionalMessages);
        
        $allMessages = $schema->getMessages();
        $this->assertArrayHasKey('field.required', $allMessages);
        $this->assertArrayHasKey('field.email', $allMessages);
    }

    #[Test]
    public function it_gets_and_sets_attributes()
    {
        $schema = new Schema();
        $attributes = ['email' => 'Email Address'];
        
        $schema->setAttributes($attributes);
        $this->assertEquals($attributes, $schema->getAttributes());
        
        $additionalAttributes = ['name' => 'Full Name'];
        $schema->withAttributes($additionalAttributes);
        
        $allAttributes = $schema->getAttributes();
        $this->assertArrayHasKey('email', $allAttributes);
        $this->assertArrayHasKey('name', $allAttributes);
    }

    #[Test]
    public function it_handles_property_instance()
    {
        $property = $this->getMockBuilder(Property::class)
                        ->disableOriginalConstructor()
                        ->onlyMethods(['getName', 'toArray', 'getRules'])
                        ->getMock();
        
        $property->expects($this->once())
                ->method('getName')
                ->willReturn('test');
                
        $property->expects($this->once())
                ->method('toArray')
                ->willReturn(['type' => 'string']);
                
        $property->expects($this->once())
                ->method('getRules')
                ->willReturn(['required']);
        
        $schema = new Schema();
        $schema->addProperty($property);
        
        $properties = $schema->getProperties();
        $rules = $schema->getRules();
        
        $this->assertArrayHasKey('test', $properties);
        $this->assertEquals(['type' => 'string'], $properties['test']);
        $this->assertArrayHasKey('test', $rules);
        $this->assertEquals(['required'], $rules['test']);
    }

    #[Test]
    public function it_handles_all_comparison_operators()
    {
        $testCases = [
            // When value equals 10, field should be required
            ['operator' => '=', 'value' => 10, 'field' => '', 'shouldBeValid' => false],
            ['operator' => '=', 'value' => 5, 'field' => '', 'shouldBeValid' => true],
            
            // When value not equals 10, field should be required
            ['operator' => '!=', 'value' => 5, 'field' => '', 'shouldBeValid' => false],
            ['operator' => '!=', 'value' => 10, 'field' => '', 'shouldBeValid' => true],
            
            // When value greater than 10, field should be required
            ['operator' => '>', 'value' => 15, 'field' => '', 'shouldBeValid' => false],
            ['operator' => '>', 'value' => 5, 'field' => '', 'shouldBeValid' => true],
            
            // When value greater than or equal to 10, field should be required
            ['operator' => '>=', 'value' => 10, 'field' => '', 'shouldBeValid' => false],
            ['operator' => '>=', 'value' => 5, 'field' => '', 'shouldBeValid' => true],
            
            // When value less than 10, field should be required
            ['operator' => '<', 'value' => 5, 'field' => '', 'shouldBeValid' => false],
            ['operator' => '<', 'value' => 15, 'field' => '', 'shouldBeValid' => true],
            
            // When value less than or equal to 10, field should be required
            ['operator' => '<=', 'value' => 10, 'field' => '', 'shouldBeValid' => false],
            ['operator' => '<=', 'value' => 15, 'field' => '', 'shouldBeValid' => true],
            
            // Invalid operator should not apply the rule
            ['operator' => 'invalid', 'value' => 10, 'field' => '', 'shouldBeValid' => true],
        ];
        
        foreach ($testCases as $case) {
            $schema = new Schema();
            $property = PropertyBuilder::string('field')
                ->whenCompare('value', $case['operator'], 10, ['required']);
            
            $schema->addProperty($property);
            
            $result = $schema->validate([
                'value' => $case['value'],
                'field' => $case['field']
            ]);
            
            $this->assertEquals(
                $case['shouldBeValid'], 
                $result['valid'], 
                "Operator {$case['operator']} with value {$case['value']} should validate as " . 
                ($case['shouldBeValid'] ? 'true' : 'false')
            );
        }
    }

    #[Test]
    public function it_handles_string_format_rules()
    {
        $schema = new Schema();
        $property = PropertyBuilder::string('email')
            ->when('is_business', true, ['required', 'email']);
        
        $schema->addProperty($property);
        
        $result = $schema->validate([
            'is_business' => true,
            'email' => 'not-an-email'
        ]);
        
        $this->assertFalse($result['valid']);
        $this->assertArrayHasKey('email', $result['errors']);
    }

    #[Test]
    public function it_handles_empty_conditional_rules()
    {
        $schema = new Schema();
        $property = $this->getMockBuilder(Property::class)
                        ->disableOriginalConstructor()
                        ->onlyMethods(['getName', 'toArray', 'getRules'])
                        ->getMock();
        
        $property->expects($this->once())
                ->method('getName')
                ->willReturn('test');
                
        $property->expects($this->once())
                ->method('toArray')
                ->willReturn(['conditionalRules' => []]);
                
        $property->expects($this->once())
                ->method('getRules')
                ->willReturn([]);
        
        $schema->addProperty($property);
        
        $result = $schema->validate(['test' => 'value']);
        $this->assertTrue($result['valid']);
    }

    #[Test]
    public function it_handles_conditional_rules_without_base_rules()
    {
        $schema = new Schema();
        $property = PropertyBuilder::string('field')
            ->when('condition', true, ['required']);
        
        $schema->addProperty($property);
        
        $properties = $schema->getProperties();
        $rules = $schema->getRules();
        
        $this->assertArrayHasKey('field', $properties);
        $this->assertArrayHasKey('field', $rules);
        $this->assertIsArray($rules['field']);
    }

    #[Test]
    public function it_handles_conditional_rules_with_pattern_match()
    {
        $schema = new Schema();
        $property = PropertyBuilder::string('field')
            ->whenMatches('pattern_field', '/^test/', ['required', 'min:3']);
        
        $schema->addProperty($property);
        
        // Test when pattern matches
        $result = $schema->validate([
            'pattern_field' => 'test123',
            'field' => ''
        ]);
        
        $this->assertFalse($result['valid']);
        $this->assertArrayHasKey('field', $result['errors']);
        
        // Test when pattern doesn't match
        $result = $schema->validate([
            'pattern_field' => 'no-match',
            'field' => ''
        ]);
        
        $this->assertTrue($result['valid']);
    }

    #[Test]
    public function it_handles_array_conditional_rules_with_empty_value()
    {
        $schema = new Schema();
        $property = PropertyBuilder::string('field')
            ->when(['status' => null], ['required']);
        
        $schema->addProperty($property);
        
        $result = $schema->validate([
            'status' => null,
            'field' => ''
        ]);
        
        $this->assertFalse($result['valid']);
        $this->assertArrayHasKey('field', $result['errors']);
    }

    #[Test]
    public function it_handles_missing_field_in_conditional_validation()
    {
        $schema = new Schema();
        $property = PropertyBuilder::string('field')
            ->when('missing_field', true, ['required']);
        
        $schema->addProperty($property);
        
        $result = $schema->validate([
            'field' => ''
        ]);
        
        $this->assertTrue($result['valid']);
    }

    #[Test]
    public function it_handles_conditional_rules_with_string_rules()
    {
        $schema = new Schema();
        
        // Test with string rules that need to be exploded
        $property = PropertyBuilder::string('field')
            ->when('condition', true, ['required', 'min:3']);
        
        $schema->addProperty($property);
        
        $result = $schema->validate([
            'condition' => true,
            'field' => 'a'
        ]);
        
        $this->assertFalse($result['valid']);
        $this->assertArrayHasKey('field', $result['errors']);
        
        // Test with direct field value comparison
        $property2 = PropertyBuilder::string('another_field')
            ->when('status', 'active', ['required']);
        
        $schema->addProperty($property2);
        
        $result = $schema->validate([
            'status' => 'active',
            'another_field' => ''
        ]);
        
        $this->assertFalse($result['valid']);
        $this->assertArrayHasKey('another_field', $result['errors']);
    }

    #[Test]
    public function it_handles_string_rules_in_conditional_validation()
    {
        $schema = new Schema();
        $property = PropertyBuilder::string('field')
            ->when('condition', true, 'required');
        
        $schema->addProperty($property);
        
        $result = $schema->validate([
            'condition' => true,
            'field' => ''
        ]);
        
        $this->assertFalse($result['valid']);
        $this->assertArrayHasKey('field', $result['errors']);
    }

    #[Test]
    public function it_handles_property_with_only_conditional_rules()
    {
        $schema = new Schema();
        $property = $this->getMockBuilder(Property::class)
                        ->disableOriginalConstructor()
                        ->onlyMethods(['getName', 'toArray', 'getRules'])
                        ->getMock();
        
        $property->expects($this->once())
                ->method('getName')
                ->willReturn('test');
                
        $property->expects($this->once())
                ->method('toArray')
                ->willReturn([
                    'conditionalRules' => [
                        [
                            'field' => 'condition',
                            'value' => true,
                            'rules' => 'required|min:3'
                        ]
                    ]
                ]);
                
        $property->expects($this->once())
                ->method('getRules')
                ->willReturn([]);
        
        $schema->addProperty($property);
        
        $result = $schema->validate([
            'condition' => true,
            'test' => 'a'
        ]);
        
        $this->assertFalse($result['valid']);
        $this->assertArrayHasKey('test', $result['errors']);
    }

    #[Test]
    public function it_handles_missing_input_field_in_conditional_validation()
    {
        $schema = new Schema();
        $property = PropertyBuilder::string('field')
            ->whenCompare('value', '>', 10, ['required']);
        
        $schema->addProperty($property);
        
        // Test when conditional field is missing
        $result = $schema->validate([
            'field' => ''
        ]);
        
        $this->assertTrue($result['valid']);
        
        // Test when conditional field is null
        $result = $schema->validate([
            'value' => null,
            'field' => ''
        ]);
        
        $this->assertTrue($result['valid']);
    }

    #[Test]
    public function it_handles_closure_conditional_rules()
    {
        $schema = new Schema();
        $property = PropertyBuilder::string('field')
            ->when(
                fn($input) => isset($input['condition']) && $input['condition'] === true,
                ['required', 'email']
            );
        
        $schema->addProperty($property);
        
        // Test when closure returns true
        $result = $schema->validate([
            'condition' => true,
            'field' => 'not-an-email'
        ]);
        
        $this->assertFalse($result['valid']);
        $this->assertArrayHasKey('field', $result['errors']);
        
        // Test when closure returns false
        $result = $schema->validate([
            'condition' => false,
            'field' => ''
        ]);
        
        $this->assertTrue($result['valid']);
    }
}
