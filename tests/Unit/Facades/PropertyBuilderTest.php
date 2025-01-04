<?php

namespace Skillcraft\UiSchemaCraft\Tests\Unit\Facades;

use Skillcraft\UiSchemaCraft\Tests\TestCase;
use Skillcraft\UiSchemaCraft\Facades\PropertyBuilder;
use Skillcraft\UiSchemaCraft\Schema\PropertyBuilder as CorePropertyBuilder;
use Skillcraft\UiSchemaCraft\Schema\Property;

class PropertyBuilderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->app->singleton('property-builder', function () {
            return new CorePropertyBuilder();
        });
    }

    public function test_string_property_creation(): void
    {
        $property = PropertyBuilder::string('name', 'Full Name');
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals('name', $property->getName());
        $this->assertEquals('string', $property->getType());
    }

    public function test_number_property_creation(): void
    {
        $property = PropertyBuilder::number('age', 'Age');
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals('age', $property->getName());
        $this->assertEquals('number', $property->getType());
    }

    public function test_boolean_property_creation(): void
    {
        $property = PropertyBuilder::boolean('active', 'Is Active');
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals('active', $property->getName());
        $this->assertEquals('boolean', $property->getType());
    }

    public function test_array_property_creation(): void
    {
        $property = PropertyBuilder::array('tags', 'Tags');
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals('tags', $property->getName());
        $this->assertEquals('array', $property->getType());
    }

    public function test_property_with_description(): void
    {
        $property = PropertyBuilder::string('email')
            ->description('Enter your email address');
        
        $this->assertEquals('Enter your email address', $property->getDescription());
    }

    public function test_property_with_default_value(): void
    {
        $property = PropertyBuilder::string('status')
            ->setDefault('pending');
        
        $array = $property->toArray();
        $this->assertEquals('pending', $array['default']);
    }

    public function test_nullable_property(): void
    {
        $property = PropertyBuilder::string('middle_name')
            ->nullable();
        
        $this->assertEquals(['null', 'string'], $property->getType());
    }

    public function test_required_property(): void
    {
        $property = PropertyBuilder::string('username')
            ->required();
        
        $array = $property->toArray();
        $this->assertTrue($array['required']);
    }

    public function test_property_with_enum_values(): void
    {
        $property = PropertyBuilder::string('status')
            ->enum(['pending', 'active', 'inactive']);
        
        $array = $property->toArray();
        $this->assertEquals(['pending', 'active', 'inactive'], $array['enum']);
    }

    public function test_property_with_builder_callback(): void
    {
        $property = PropertyBuilder::object('user')
            ->withBuilder(function ($builder) {
                $builder->string('name')->required();
                $builder->number('age')->min(18);
            });
        
        $array = $property->toArray();
        $this->assertArrayHasKey('properties', $array);
        $this->assertArrayHasKey('name', $array['properties']);
        $this->assertArrayHasKey('age', $array['properties']);
    }

    public function test_property_to_array_conversion(): void
    {
        $property = PropertyBuilder::string('email')
            ->description('Email address')
            ->required()
            ->setDefault('user@example.com');
        
        $array = $property->toArray();
        
        $this->assertIsArray($array);
        $this->assertEquals('email', $array['name']);
        $this->assertEquals('string', $array['type']);
        $this->assertEquals('Email address', $array['description']);
        $this->assertTrue($array['required']);
        $this->assertEquals('user@example.com', $array['default']);
    }

    public function test_property_builder_merge(): void
    {
        $builder1 = new CorePropertyBuilder();
        $builder1->string('name')->required();

        $builder2 = new CorePropertyBuilder();
        $builder2->number('age')->min(18);

        $builder1->merge($builder2);
        $array = $builder1->toArray();

        $this->assertArrayHasKey('name', $array);
        $this->assertArrayHasKey('age', $array);
    }

    public function test_property_builder_prefix(): void
    {
        $builder = new CorePropertyBuilder();
        $builder->string('name')->required();
        $builder->number('age')->min(18);

        $builder->prefix('user_');
        $array = $builder->toArray();

        $this->assertArrayHasKey('user_name', $array);
        $this->assertArrayHasKey('user_age', $array);
    }

    public function test_property_builder_new_instance(): void
    {
        $builder = new CorePropertyBuilder();
        $newBuilder = $builder->new();

        $this->assertInstanceOf(CorePropertyBuilder::class, $newBuilder);
        $this->assertNotSame($builder, $newBuilder);
    }
}
