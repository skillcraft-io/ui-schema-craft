<?php

namespace Skillcraft\UiSchemaCraft\Tests\Unit\Schema;

use PHPUnit\Framework\Attributes\Test;
use Skillcraft\UiSchemaCraft\Tests\TestCase;
use Skillcraft\UiSchemaCraft\Schema\PropertyBuilder;
use Skillcraft\UiSchemaCraft\Schema\Property;
use Illuminate\Validation\Rule;

class PropertyBuilderTest extends TestCase
{
    protected PropertyBuilder $builder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->builder = new PropertyBuilder();
    }

    #[Test]
    public function it_creates_new_instance()
    {
        $newBuilder = $this->builder->new();
        $this->assertInstanceOf(PropertyBuilder::class, $newBuilder);
    }

    #[Test]
    public function it_adds_property()
    {
        $property = new Property('name', 'string', 'Test Property');
        $addedProperty = $this->builder->add($property);
        
        $this->assertSame($property, $addedProperty);
        $this->assertArrayHasKey('name', $this->builder->getProperties());
    }

    #[Test]
    public function it_converts_to_array()
    {
        $property1 = new Property('name', 'string', 'Name Property');
        $property2 = new Property('age', 'number', 'Age Property');
        
        $this->builder->add($property1);
        $this->builder->add($property2);
        
        $array = $this->builder->toArray();
        
        $this->assertArrayHasKey('name', $array);
        $this->assertArrayHasKey('age', $array);
    }

    #[Test]
    public function it_merges_with_another_builder()
    {
        $otherBuilder = new PropertyBuilder();
        $property = new Property('email', 'string', 'Email Property');
        $otherBuilder->add($property);
        
        $this->builder->merge($otherBuilder);
        
        $this->assertArrayHasKey('email', $this->builder->getProperties());
    }

    #[Test]
    public function it_adds_prefix_to_properties()
    {
        $property1 = new Property('name', 'string', 'Name Property');
        $property2 = new Property('age', 'number', 'Age Property');
        
        $this->builder->add($property1);
        $this->builder->add($property2);
        
        $this->builder->prefix('user_');
        
        $properties = $this->builder->getProperties();
        $this->assertArrayHasKey('user_name', $properties);
        $this->assertArrayHasKey('user_age', $properties);
    }

    #[Test]
    public function it_gets_properties()
    {
        $property1 = new Property('name', 'string', 'Name Property');
        $property2 = new Property('age', 'number', 'Age Property');
        
        $this->builder->add($property1);
        $this->builder->add($property2);
        
        $properties = $this->builder->getProperties();
        
        $this->assertCount(2, $properties);
        $this->assertArrayHasKey('name', $properties);
        $this->assertArrayHasKey('age', $properties);
    }

    #[Test]
    public function it_validates_property()
    {
        $property = $this->builder->validate('age', ['required', 'min:18']);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals('age', $property->getName());
        
        $rules = $property->getRules();
        $this->assertCount(2, $rules);
        
        $this->assertEquals('required', $rules[0]);
        $this->assertEquals('min:18', $rules[1]);
    }

    #[Test]
    public function it_converts_property_with_default_value_to_array()
    {
        $property = new Property('name', 'string', 'Name Property');
        $property->setDefault('John Doe');
        $this->builder->add($property);

        $array = $this->builder->toArray();

        $this->assertArrayHasKey('name', $array);
        $this->assertEquals('string', $array['name']['type']);
        $this->assertEquals('Name Property', $array['name']['description']);
        $this->assertEquals('John Doe', $array['name']['default']);
    }

    #[Test]
    public function it_converts_property_without_default_value_to_array()
    {
        $property = new Property('name', 'string', 'Name Property');
        $this->builder->add($property);

        $array = $this->builder->toArray();

        $this->assertArrayHasKey('name', $array);
        $this->assertEquals('string', $array['name']['type']);
        $this->assertEquals('Name Property', $array['name']['description']);
        $this->assertArrayNotHasKey('default', $array['name']);
    }
}
