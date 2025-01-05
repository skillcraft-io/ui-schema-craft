<?php

namespace Skillcraft\UiSchemaCraft\Tests\Unit\Schema\Traits;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversClass;
use Skillcraft\UiSchemaCraft\Schema\Traits\ValidationTrait;
use Skillcraft\UiSchemaCraft\Tests\TestCase;

#[CoversClass(ValidationTrait::class)]
class ValidationTraitTest extends TestCase
{
    private $traitObject;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test double that uses the trait directly
        $this->traitObject = new class {
            use ValidationTrait;
            
            protected array $attributes = [];
            
            public function getAttributes(): array
            {
                return $this->attributes;
            }
        };
    }

    #[Test]
    public function it_adds_when_validation_with_field()
    {
        $this->traitObject->when('type', 'business', ['required']);

        $attributes = $this->traitObject->getAttributes();
        $this->assertArrayHasKey('conditionalRules', $attributes);
        $rules = $attributes['conditionalRules'];
        $this->assertCount(1, $rules);
        $rule = $rules[0];
        $this->assertEquals('field', $rule['type']);
        $this->assertEquals('type', $rule['field']);
        $this->assertEquals('business', $rule['value']);
        $this->assertEquals(['required'], $rule['rules']);
    }

    #[Test]
    public function it_adds_when_validation_with_field_and_string_rule()
    {
        $this->traitObject->when('type', 'business', 'required');

        $attributes = $this->traitObject->getAttributes();
        $this->assertArrayHasKey('conditionalRules', $attributes);
        $rules = $attributes['conditionalRules'];
        $this->assertCount(1, $rules);
        $rule = $rules[0];
        $this->assertEquals('field', $rule['type']);
        $this->assertEquals('type', $rule['field']);
        $this->assertEquals('business', $rule['value']);
        $this->assertEquals(['required'], $rule['rules']);
    }

    #[Test]
    public function it_adds_when_validation_with_closure()
    {
        $closure = function($field) {
            return $field > 10;
        };

        $this->traitObject->when($closure, ['min:10']);

        $attributes = $this->traitObject->getAttributes();
        $this->assertArrayHasKey('conditionalRules', $attributes);
        $rules = $attributes['conditionalRules'];
        $this->assertCount(1, $rules);
        $rule = $rules[0];
        $this->assertEquals('closure', $rule['type']);
        $this->assertInstanceOf(\Closure::class, $rule['closure']);
        $this->assertEquals(['min:10'], $rule['rules']);
    }

    #[Test]
    public function it_adds_when_validation_with_closure_and_string_rule()
    {
        $closure = function($field) {
            return $field > 10;
        };

        $this->traitObject->when($closure, 'min:10');

        $attributes = $this->traitObject->getAttributes();
        $this->assertArrayHasKey('conditionalRules', $attributes);
        $rules = $attributes['conditionalRules'];
        $this->assertCount(1, $rules);
        $rule = $rules[0];
        $this->assertEquals('closure', $rule['type']);
        $this->assertInstanceOf(\Closure::class, $rule['closure']);
        $this->assertEquals(['min:10'], $rule['rules']);
    }

    #[Test]
    public function it_adds_when_validation_with_array()
    {
        $conditions = ['role' => 'admin', 'status' => 'active'];
        $this->traitObject->when($conditions, ['required']);

        $attributes = $this->traitObject->getAttributes();
        $this->assertArrayHasKey('conditionalRules', $attributes);
        $rules = $attributes['conditionalRules'];
        $this->assertCount(1, $rules);
        $rule = $rules[0];
        $this->assertEquals('array', $rule['type']);
        $this->assertEquals($conditions, $rule['field']);
        $this->assertEquals(['required'], $rule['rules']);
    }

    #[Test]
    public function it_adds_when_validation_with_array_and_string_rule()
    {
        $conditions = ['role' => 'admin', 'status' => 'active'];
        $this->traitObject->when($conditions, 'required');

        $attributes = $this->traitObject->getAttributes();
        $this->assertArrayHasKey('conditionalRules', $attributes);
        $rules = $attributes['conditionalRules'];
        $this->assertCount(1, $rules);
        $rule = $rules[0];
        $this->assertEquals('array', $rule['type']);
        $this->assertEquals($conditions, $rule['field']);
        $this->assertEquals(['required'], $rule['rules']);
    }

    #[Test]
    public function it_adds_when_matches_validation()
    {
        $this->traitObject->whenMatches('country_code', '/^EU/', ['required']);

        $attributes = $this->traitObject->getAttributes();
        $this->assertArrayHasKey('conditionalRules', $attributes);
        $rules = $attributes['conditionalRules'];
        $this->assertCount(1, $rules);
        $rule = $rules[0];
        $this->assertEquals('pattern', $rule['type']);
        $this->assertEquals('country_code', $rule['field']);
        $this->assertEquals(['pattern' => '/^EU/'], $rule['value']);
        $this->assertEquals(['required'], $rule['rules']);
    }

    #[Test]
    public function it_adds_when_matches_validation_with_string_rule()
    {
        $this->traitObject->whenMatches('country_code', '/^EU/', 'required');

        $attributes = $this->traitObject->getAttributes();
        $this->assertArrayHasKey('conditionalRules', $attributes);
        $rules = $attributes['conditionalRules'];
        $this->assertCount(1, $rules);
        $rule = $rules[0];
        $this->assertEquals('pattern', $rule['type']);
        $this->assertEquals('country_code', $rule['field']);
        $this->assertEquals(['pattern' => '/^EU/'], $rule['value']);
        $this->assertEquals(['required'], $rule['rules']);
    }

    #[Test]
    public function it_adds_when_compare_validation()
    {
        $this->traitObject->whenCompare('age', '<', 18, ['required']);

        $attributes = $this->traitObject->getAttributes();
        $this->assertArrayHasKey('conditionalRules', $attributes);
        $rules = $attributes['conditionalRules'];
        $this->assertCount(1, $rules);
        $rule = $rules[0];
        $this->assertEquals('comparison', $rule['type']);
        $this->assertEquals('age', $rule['field']);
        $this->assertEquals(['operator' => '<', 'value' => 18], $rule['value']);
        $this->assertEquals(['required'], $rule['rules']);
    }

    #[Test]
    public function it_adds_when_compare_validation_with_string_rule()
    {
        $this->traitObject->whenCompare('age', '<', 18, 'required');

        $attributes = $this->traitObject->getAttributes();
        $this->assertArrayHasKey('conditionalRules', $attributes);
        $rules = $attributes['conditionalRules'];
        $this->assertCount(1, $rules);
        $rule = $rules[0];
        $this->assertEquals('comparison', $rule['type']);
        $this->assertEquals('age', $rule['field']);
        $this->assertEquals(['operator' => '<', 'value' => 18], $rule['value']);
        $this->assertEquals(['required'], $rule['rules']);
    }

    #[Test]
    public function it_adds_required_with_validation()
    {
        $this->traitObject->requiredWith(['shipping_address', 'payment_method']);

        $attributes = $this->traitObject->getAttributes();
        $this->assertArrayHasKey('conditionalRules', $attributes);
        $rules = $attributes['conditionalRules'];
        $this->assertCount(1, $rules);
        $rule = $rules[0];
        $this->assertEquals('requiredWith', $rule['type']);
        $this->assertEquals(['shipping_address', 'payment_method'], $rule['field']);
        $this->assertEquals(['required'], $rule['rules']);
    }

    #[Test]
    public function it_adds_required_without_validation()
    {
        $this->traitObject->requiredWithout(['email', 'address']);

        $attributes = $this->traitObject->getAttributes();
        $this->assertArrayHasKey('conditionalRules', $attributes);
        $rules = $attributes['conditionalRules'];
        $this->assertCount(1, $rules);
        $rule = $rules[0];
        $this->assertEquals('requiredWithout', $rule['type']);
        $this->assertEquals(['email', 'address'], $rule['field']);
        $this->assertEquals(['required'], $rule['rules']);
    }

    #[Test]
    public function it_adds_required_if_validation()
    {
        $this->traitObject->requiredIf('is_company', true);

        $attributes = $this->traitObject->getAttributes();
        $this->assertArrayHasKey('conditionalRules', $attributes);
        $rules = $attributes['conditionalRules'];
        $this->assertCount(1, $rules);
        $rule = $rules[0];
        $this->assertEquals('field', $rule['type']);
        $this->assertEquals('is_company', $rule['field']);
        $this->assertTrue($rule['value']);
        $this->assertEquals(['required'], $rule['rules']);
    }

    #[Test]
    public function it_adds_prohibited_if_validation()
    {
        $this->traitObject->prohibitedIf('payment_method', 'bank_transfer');

        $attributes = $this->traitObject->getAttributes();
        $this->assertArrayHasKey('conditionalRules', $attributes);
        $rules = $attributes['conditionalRules'];
        $this->assertCount(1, $rules);
        $rule = $rules[0];
        $this->assertEquals('field', $rule['type']);
        $this->assertEquals('payment_method', $rule['field']);
        $this->assertEquals('bank_transfer', $rule['value']);
        $this->assertEquals(['prohibited'], $rule['rules']);
    }

    #[Test]
    public function it_initializes_conditional_rules_array_if_not_set()
    {
        $attributes = $this->traitObject->getAttributes();
        $this->assertArrayNotHasKey('conditionalRules', $attributes);

        $this->traitObject->when('field', 'value', ['required']);
        
        $attributes = $this->traitObject->getAttributes();
        $this->assertArrayHasKey('conditionalRules', $attributes);
    }

    #[Test]
    public function it_can_chain_multiple_validation_rules()
    {
        $this->traitObject
            ->when('field1', 'value1', ['required'])
            ->whenMatches('field2', '/pattern/', ['min:3'])
            ->whenCompare('field3', '>', 10, ['max:20'])
            ->requiredWith(['field4'])
            ->requiredWithout(['field5'])
            ->requiredIf('field6', true)
            ->prohibitedIf('field7', false);

        $attributes = $this->traitObject->getAttributes();
        $rules = $attributes['conditionalRules'];
        
        $this->assertCount(7, $rules);
        $this->assertEquals('field', $rules[0]['type']);
        $this->assertEquals('pattern', $rules[1]['type']);
        $this->assertEquals('comparison', $rules[2]['type']);
        $this->assertEquals('requiredWith', $rules[3]['type']);
        $this->assertEquals('requiredWithout', $rules[4]['type']);
        $this->assertEquals('field', $rules[5]['type']);
        $this->assertEquals('field', $rules[6]['type']);
    }
}
