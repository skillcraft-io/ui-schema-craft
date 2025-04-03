<?php

namespace Skillcraft\UiSchemaCraft\Tests\Unit\Schema\Traits;

use Skillcraft\UiSchemaCraft\Tests\TestCase;
use Skillcraft\UiSchemaCraft\Schema\Traits\ValidationTrait;

class ValidationTraitTest extends TestCase
{
    private $traitUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test class that uses the trait
        $this->traitUser = new class {
            use ValidationTrait;
        };
    }

    public function testRule(): void
    {
        $result = $this->traitUser->rule('email');
        
        $this->assertSame($this->traitUser, $result);
        $this->assertContains('email', $this->traitUser->getRules());
    }
    
    public function testRules(): void
    {
        $rules = ['email', 'min:3', 'max:255'];
        $result = $this->traitUser->rules($rules);
        
        $this->assertSame($this->traitUser, $result);
        foreach ($rules as $rule) {
            $this->assertContains($rule, $this->traitUser->getRules());
        }
    }
    
    public function testMessage(): void
    {
        $rule = 'email';
        $message = 'Must be a valid email address';
        $result = $this->traitUser->message($rule, $message);
        
        $this->assertSame($this->traitUser, $result);
        $this->assertArrayHasKey($rule, $this->traitUser->getMessages());
        $this->assertEquals($message, $this->traitUser->getMessages()[$rule]);
    }
    
    public function testMessages(): void
    {
        $messages = [
            'email' => 'Must be a valid email address',
            'min' => 'Must be at least :min characters'
        ];
        $result = $this->traitUser->messages($messages);
        
        $this->assertSame($this->traitUser, $result);
        foreach ($messages as $rule => $message) {
            $this->assertArrayHasKey($rule, $this->traitUser->getMessages());
            $this->assertEquals($message, $this->traitUser->getMessages()[$rule]);
        }
    }
    
    public function testRequired(): void
    {
        $result = $this->traitUser->required();
        
        $this->assertSame($this->traitUser, $result);
        $this->assertContains('required', $this->traitUser->getRules());
    }
    
    public function testNullable(): void
    {
        $result = $this->traitUser->nullable();
        
        $this->assertSame($this->traitUser, $result);
        $this->assertContains('nullable', $this->traitUser->getRules());
    }
    
    public function testWhenWithFieldAndValue(): void
    {
        $field = 'status';
        $value = 'active';
        $testRules = ['required', 'email'];
        
        $result = $this->traitUser->when($field, $value, $testRules);
        
        $this->assertSame($this->traitUser, $result);
        
        $allRules = $this->traitUser->getRules();
        $ruleEntry = end($allRules);
        $this->assertIsArray($ruleEntry);
        $this->assertArrayHasKey('when', $ruleEntry);
        $this->assertArrayHasKey('rules', $ruleEntry);
        $this->assertEquals([$field => $value], $ruleEntry['when']);
        $this->assertEquals($testRules, $ruleEntry['rules']);
    }
    
    public function testWhenWithCallback(): void
    {
        $callback = function() {
            return true;
        };
        $testRules = ['required', 'email'];
        
        $result = $this->traitUser->when($callback, null, $testRules);
        
        $this->assertSame($this->traitUser, $result);
        
        $allRules = $this->traitUser->getRules();
        $ruleEntry = end($allRules);
        $this->assertIsArray($ruleEntry);
        $this->assertArrayHasKey('when', $ruleEntry);
        $this->assertArrayHasKey('rules', $ruleEntry);
        $this->assertSame($callback, $ruleEntry['when']);
        $this->assertEquals($testRules, $ruleEntry['rules']);
    }
    
    public function testRequiredWith(): void
    {
        $fields = ['first_name', 'last_name'];
        $result = $this->traitUser->requiredWith($fields);
        
        $this->assertSame($this->traitUser, $result);
        $this->assertContains('required_with:first_name,last_name', $this->traitUser->getRules());
    }
    
    public function testRequiredWithout(): void
    {
        $fields = ['first_name', 'last_name'];
        $result = $this->traitUser->requiredWithout($fields);
        
        $this->assertSame($this->traitUser, $result);
        $this->assertContains('required_without:first_name,last_name', $this->traitUser->getRules());
    }
    
    public function testRequiredIf(): void
    {
        $field = 'role';
        $value = 'admin';
        $result = $this->traitUser->requiredIf($field, $value);
        
        $this->assertSame($this->traitUser, $result);
        $this->assertContains("required_if:$field,$value", $this->traitUser->getRules());
    }
    
    public function testProhibitedIf(): void
    {
        $field = 'role';
        $value = 'guest';
        $result = $this->traitUser->prohibitedIf($field, $value);
        
        $this->assertSame($this->traitUser, $result);
        $this->assertContains("prohibited_if:$field,$value", $this->traitUser->getRules());
    }
    
    public function testGetRules(): void
    {
        $rules = ['email', 'min:3', 'max:255'];
        $this->traitUser->rules($rules);
        
        $this->assertEquals($rules, $this->traitUser->getRules());
    }
    
    public function testGetMessages(): void
    {
        $messages = [
            'email' => 'Must be a valid email address',
            'min' => 'Must be at least :min characters'
        ];
        $this->traitUser->messages($messages);
        
        $this->assertEquals($messages, $this->traitUser->getMessages());
    }
}
