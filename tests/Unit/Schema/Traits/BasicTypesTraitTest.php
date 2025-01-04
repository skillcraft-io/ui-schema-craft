<?php

namespace Skillcraft\UiSchemaCraft\Tests\Unit\Schema\Traits;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversClass;
use Skillcraft\UiSchemaCraft\Tests\TestCase;
use Skillcraft\UiSchemaCraft\Schema\PropertyBuilder;
use Skillcraft\UiSchemaCraft\Schema\Traits\BasicTypesTrait;

#[CoversClass(BasicTypesTrait::class)]
class BasicTypesTraitTest extends TestCase
{
    protected PropertyBuilder $builder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->builder = new PropertyBuilder();
    }

    #[Test]
    public function it_creates_string_property()
    {
        $property = $this->builder->string('name');
        $schema = $property->toArray();
        
        $this->assertEquals('name', $property->getName());
        $this->assertEquals('string', $schema['type']);
        $this->assertArrayNotHasKey('description', $schema);
    }

    #[Test]
    public function it_creates_string_property_with_label()
    {
        $property = $this->builder->string('name', 'Full Name');
        $schema = $property->toArray();
        
        $this->assertEquals('name', $property->getName());
        $this->assertEquals('string', $schema['type']);
        $this->assertEquals('Full Name', $schema['description']);
    }

    #[Test]
    public function it_creates_number_property()
    {
        $property = $this->builder->number('age');
        $schema = $property->toArray();
        
        $this->assertEquals('age', $property->getName());
        $this->assertEquals('number', $schema['type']);
        $this->assertArrayNotHasKey('description', $schema);
    }

    #[Test]
    public function it_creates_number_property_with_label()
    {
        $property = $this->builder->number('age', 'User Age');
        $schema = $property->toArray();
        
        $this->assertEquals('age', $property->getName());
        $this->assertEquals('number', $schema['type']);
        $this->assertEquals('User Age', $schema['description']);
    }

    #[Test]
    public function it_creates_boolean_property()
    {
        $property = $this->builder->boolean('active');
        $schema = $property->toArray();
        
        $this->assertEquals('active', $property->getName());
        $this->assertEquals('boolean', $schema['type']);
        $this->assertArrayNotHasKey('description', $schema);
    }

    #[Test]
    public function it_creates_boolean_property_with_label()
    {
        $property = $this->builder->boolean('active', 'Is Active');
        $schema = $property->toArray();
        
        $this->assertEquals('active', $property->getName());
        $this->assertEquals('boolean', $schema['type']);
        $this->assertEquals('Is Active', $schema['description']);
    }

    #[Test]
    public function it_creates_object_property()
    {
        $property = $this->builder->object('address');
        $schema = $property->toArray();
        
        $this->assertEquals('address', $property->getName());
        $this->assertEquals('object', $schema['type']);
        $this->assertArrayNotHasKey('description', $schema);
    }

    #[Test]
    public function it_creates_object_property_with_label()
    {
        $property = $this->builder->object('address', 'Mailing Address');
        $schema = $property->toArray();
        
        $this->assertEquals('address', $property->getName());
        $this->assertEquals('object', $schema['type']);
        $this->assertEquals('Mailing Address', $schema['description']);
    }

    #[Test]
    public function it_creates_array_property()
    {
        $property = $this->builder->array('tags');
        $schema = $property->toArray();
        
        $this->assertEquals('tags', $property->getName());
        $this->assertEquals('array', $schema['type']);
        $this->assertArrayNotHasKey('description', $schema);
    }

    #[Test]
    public function it_creates_array_property_with_label()
    {
        $property = $this->builder->array('tags', 'Content Tags');
        $schema = $property->toArray();
        
        $this->assertEquals('tags', $property->getName());
        $this->assertEquals('array', $schema['type']);
        $this->assertEquals('Content Tags', $schema['description']);
    }

    #[Test]
    public function it_adds_nullable_property()
    {
        $property = $this->builder->string('description')->nullable();
        $schema = $property->toArray();
        
        $this->assertEquals(['null', 'string'], $schema['type']);
    }

    #[Test]
    public function it_adds_description()
    {
        $description = 'User full name';
        $property = $this->builder->string('name')->description($description);
        $schema = $property->toArray();
        
        $this->assertEquals($description, $schema['description']);
    }

    #[Test]
    public function it_adds_reference()
    {
        $ref = '#/components/schemas/User';
        $property = $this->builder->string('user')->reference($ref);
        $schema = $property->toArray();
        
        $this->assertEquals($ref, $schema['$ref']);
    }

    #[Test]
    public function it_stores_property_in_builder()
    {
        $property = $this->builder->string('name', 'Full Name');
        $properties = $this->builder->getProperties();
        
        $this->assertArrayHasKey('name', $properties);
        $this->assertSame($property, $properties['name']);
    }
}
