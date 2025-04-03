<?php

namespace Skillcraft\UiSchemaCraft\Tests\Unit\Schema\Traits;

use Skillcraft\UiSchemaCraft\Tests\TestCase;
use Skillcraft\UiSchemaCraft\Schema\Property;
use Skillcraft\UiSchemaCraft\Schema\Traits\BasicTypesTrait;

class BasicTypesTraitTest extends TestCase
{
    /**
     * Test class that uses the BasicTypesTrait
     */
    private $traitUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test class that uses the trait
        $this->traitUser = new class {
            use BasicTypesTrait;
            
            public array $properties = [];
        };
    }

    public function testStringProperty(): void
    {
        $propertyName = 'testString';
        $propertyLabel = 'Test String';
        
        $property = $this->traitUser->string($propertyName, $propertyLabel);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('string', $property->getType());
        $this->assertEquals($propertyLabel, $property->getDescription());
        $this->assertArrayHasKey($propertyName, $this->traitUser->properties);
        $this->assertSame($property, $this->traitUser->properties[$propertyName]);
    }

    public function testNumberProperty(): void
    {
        $propertyName = 'testNumber';
        $propertyLabel = 'Test Number';
        
        $property = $this->traitUser->number($propertyName, $propertyLabel);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('number', $property->getType());
        $this->assertEquals($propertyLabel, $property->getDescription());
        $this->assertArrayHasKey($propertyName, $this->traitUser->properties);
        $this->assertSame($property, $this->traitUser->properties[$propertyName]);
    }

    public function testBooleanProperty(): void
    {
        $propertyName = 'testBoolean';
        $propertyLabel = 'Test Boolean';
        
        $property = $this->traitUser->boolean($propertyName, $propertyLabel);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('boolean', $property->getType());
        $this->assertEquals($propertyLabel, $property->getDescription());
        $this->assertArrayHasKey($propertyName, $this->traitUser->properties);
        $this->assertSame($property, $this->traitUser->properties[$propertyName]);
    }

    public function testObjectProperty(): void
    {
        $propertyName = 'testObject';
        $propertyLabel = 'Test Object';
        
        $property = $this->traitUser->object($propertyName, $propertyLabel);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('object', $property->getType());
        $this->assertEquals($propertyLabel, $property->getDescription());
        $this->assertArrayHasKey($propertyName, $this->traitUser->properties);
        $this->assertSame($property, $this->traitUser->properties[$propertyName]);
    }

    public function testArrayProperty(): void
    {
        $propertyName = 'testArray';
        $propertyLabel = 'Test Array';
        
        $property = $this->traitUser->array($propertyName, $propertyLabel);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('array', $property->getType());
        $this->assertEquals($propertyLabel, $property->getDescription());
        $this->assertArrayHasKey($propertyName, $this->traitUser->properties);
        $this->assertSame($property, $this->traitUser->properties[$propertyName]);
    }

    public function testPropertyWithoutLabel(): void
    {
        $propertyName = 'noLabel';
        
        $property = $this->traitUser->string($propertyName);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('string', $property->getType());
        $this->assertNull($property->getDescription());
    }
}
