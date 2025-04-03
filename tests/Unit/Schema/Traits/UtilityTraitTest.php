<?php

namespace Skillcraft\UiSchemaCraft\Tests\Unit\Schema\Traits;

use Skillcraft\UiSchemaCraft\Tests\TestCase;
use Skillcraft\UiSchemaCraft\Schema\Property;
use Skillcraft\UiSchemaCraft\Schema\PropertyBuilder;
use Skillcraft\UiSchemaCraft\Schema\Traits\UtilityTrait;

class UtilityTraitTest extends TestCase
{
    /**
     * Test class that uses the UtilityTrait
     */
    private $traitUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test class that uses the trait
        $this->traitUser = new class {
            use UtilityTrait;
        };
    }

    public function testGroupProperty(): void
    {
        $propertyName = 'personalInfo';
        $description = 'Personal Information';
        
        $property = $this->traitUser->group($propertyName, function(PropertyBuilder $builder) {
            $builder->string('firstName')->required();
            $builder->string('lastName')->required();
            $builder->string('email')->format('email');
        }, $description);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('object', $property->getType());
        $this->assertEquals($description, $property->getDescription());
    }
    
    public function testGroupPropertyWithAutoDescription(): void
    {
        $propertyName = 'contact_details';
        
        $property = $this->traitUser->group($propertyName, function(PropertyBuilder $builder) {
            $builder->string('phone')->required();
            $builder->string('address')->required();
        });
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('object', $property->getType());
        $this->assertEquals('Contact Details', $property->getDescription());
    }
    
    public function testGroupHasCorrectStructure(): void
    {
        $propertyName = 'address';
        $description = 'Address Information';
        
        $property = $this->traitUser->group($propertyName, function(PropertyBuilder $builder) {
            $builder->string('street')->required();
            $builder->string('city')->required();
            $builder->string('state')->required();
            $builder->string('zip')->required();
        }, $description);
        
        $attributes = $property->toArray();
        
        // Check if properties is defined
        $this->assertArrayHasKey('properties', $attributes);
        $properties = $attributes['properties'];
        
        // Check required properties
        $this->assertArrayHasKey('street', $properties);
        $this->assertArrayHasKey('city', $properties);
        $this->assertArrayHasKey('state', $properties);
        $this->assertArrayHasKey('zip', $properties);
        
        // Check types
        $this->assertEquals('string', $properties['street']['type']);
        $this->assertEquals('string', $properties['city']['type']);
        $this->assertEquals('string', $properties['state']['type']);
        $this->assertEquals('string', $properties['zip']['type']);
    }
    
    public function testListProperty(): void
    {
        $propertyName = 'todoItems';
        $description = 'Todo List Items';
        
        $property = $this->traitUser->list($propertyName, function(PropertyBuilder $builder) {
            $builder->string('title')->required();
            $builder->string('description');
            $builder->boolean('completed')->default(false);
        }, $description);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('array', $property->getType());
        $this->assertEquals($description, $property->getDescription());
    }
    
    public function testListPropertyWithAutoDescription(): void
    {
        $propertyName = 'cart_items';
        
        $property = $this->traitUser->list($propertyName, function(PropertyBuilder $builder) {
            $builder->string('product')->required();
            $builder->number('quantity')->required()->min(1);
            $builder->number('price')->required();
        });
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('array', $property->getType());
        $this->assertEquals('Cart Items', $property->getDescription());
    }
    
    public function testListHasCorrectStructure(): void
    {
        $propertyName = 'employees';
        $description = 'Employee List';
        
        $property = $this->traitUser->list($propertyName, function(PropertyBuilder $builder) {
            $builder->string('name')->required();
            $builder->string('position')->required();
            $builder->number('salary')->required();
        }, $description);
        
        $attributes = $property->toArray();
        
        // Check if items is defined
        $this->assertArrayHasKey('items', $attributes);
        $items = $attributes['items'];
        
        // Check items structure
        $this->assertEquals('object', $items['type']);
        $this->assertArrayHasKey('properties', $items);
        
        $properties = $items['properties'];
        
        // Check required properties
        $this->assertArrayHasKey('name', $properties);
        $this->assertArrayHasKey('position', $properties);
        $this->assertArrayHasKey('salary', $properties);
        
        // Check types
        $this->assertEquals('string', $properties['name']['type']);
        $this->assertEquals('string', $properties['position']['type']);
        $this->assertEquals('number', $properties['salary']['type']);
    }
    
    public function testConditionalProperty(): void
    {
        $propertyName = 'shippingInfo';
        $description = 'Shipping Information';
        $conditions = [
            'field' => 'needsShipping',
            'operator' => '==',
            'value' => true
        ];
        
        $property = $this->traitUser->conditional($propertyName, $conditions, function(PropertyBuilder $builder) {
            $builder->string('address')->required();
            $builder->string('city')->required();
            $builder->string('state')->required();
            $builder->string('zip')->required();
        }, $description);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('object', $property->getType());
        $this->assertEquals($description, $property->getDescription());
    }
    
    public function testConditionalPropertyWithAutoDescription(): void
    {
        $propertyName = 'payment_details';
        $conditions = [
            'field' => 'paymentMethod',
            'operator' => '==',
            'value' => 'credit_card'
        ];
        
        $property = $this->traitUser->conditional($propertyName, $conditions, function(PropertyBuilder $builder) {
            $builder->string('cardNumber')->required();
            $builder->string('expiryDate')->required();
            $builder->string('cvv')->required();
        });
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('object', $property->getType());
        $this->assertEquals('Payment Details', $property->getDescription());
    }
    
    public function testConditionalHasCorrectStructure(): void
    {
        $propertyName = 'additionalInfo';
        $description = 'Additional Information';
        $conditions = [
            'field' => 'hasMore',
            'operator' => '==',
            'value' => true
        ];
        
        $property = $this->traitUser->conditional($propertyName, $conditions, function(PropertyBuilder $builder) {
            $builder->string('details')->required();
            $builder->string('notes');
        }, $description);
        
        $attributes = $property->toArray();
        
        // Check if properties is defined
        $this->assertArrayHasKey('properties', $attributes);
        $properties = $attributes['properties'];
        
        // Check required properties
        $this->assertArrayHasKey('details', $properties);
        $this->assertArrayHasKey('notes', $properties);
        
        // Check types
        $this->assertEquals('string', $properties['details']['type']);
        $this->assertEquals('string', $properties['notes']['type']);
        
        // Check conditions
        $this->assertArrayHasKey('conditions', $attributes);
        $this->assertEquals($conditions, $attributes['conditions']);
    }
}
