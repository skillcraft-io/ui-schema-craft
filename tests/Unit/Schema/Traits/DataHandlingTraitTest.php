<?php

namespace Skillcraft\UiSchemaCraft\Tests\Unit\Schema\Traits;

use Skillcraft\UiSchemaCraft\Tests\TestCase;
use Skillcraft\UiSchemaCraft\Schema\Property;
use Skillcraft\UiSchemaCraft\Schema\Traits\DataHandlingTrait;

class DataHandlingTraitTest extends TestCase
{
    /**
     * Test class that uses the DataHandlingTrait
     */
    private $traitUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test class that uses the trait
        $this->traitUser = new class {
            use DataHandlingTrait;
            
            // Required method called by some trait methods
            public function string(string $name, ?string $label = null): Property
            {
                $property = new Property($name, 'string', $label);
                return $property;
            }
        };
    }

    public function testIdProperty(): void
    {
        $property = $this->traitUser->id();
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals('id', $property->getName());
        $this->assertEquals('string', $property->getType());
        $this->assertEquals('Unique identifier', $property->getDescription());
        
        $attributes = $property->toArray();
        $this->assertContains('required', $attributes['rules'] ?? []);
    }
    
    public function testIdPropertyWithCustomName(): void
    {
        $customName = 'userId';
        $property = $this->traitUser->id($customName);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($customName, $property->getName());
    }

    public function testForeignKeyProperty(): void
    {
        $propertyName = 'categoryId';
        $references = 'categories';
        
        $property = $this->traitUser->foreignKey($propertyName, $references);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('string', $property->getType());
        $this->assertEquals("Foreign key reference to $references", $property->getDescription());
        
        $attributes = $property->toArray();
        $this->assertContains('required', $attributes['rules'] ?? []);
    }

    public function testTimestampProperty(): void
    {
        $propertyName = 'createdAt';
        
        $property = $this->traitUser->timestamp($propertyName);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('string', $property->getType());
        $this->assertEquals('Timestamp field', $property->getDescription());
        
        $attributes = $property->toArray();
        $this->assertEquals('date-time', $attributes['format'] ?? null);
    }

    public function testSlugProperty(): void
    {
        $property = $this->traitUser->slug();
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals('slug', $property->getName());
        $this->assertEquals('string', $property->getType());
        $this->assertEquals('URL-friendly slug', $property->getDescription());
        
        $attributes = $property->toArray();
        $this->assertEquals('^[a-z0-9]+(?:-[a-z0-9]+)*$', $attributes['pattern'] ?? null);
    }
    
    public function testSlugPropertyWithCustomName(): void
    {
        $customName = 'productSlug';
        $property = $this->traitUser->slug($customName);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($customName, $property->getName());
    }

    public function testJsonProperty(): void
    {
        $propertyName = 'metadata';
        
        $property = $this->traitUser->json($propertyName);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('object', $property->getType());
        $this->assertEquals('JSON data field', $property->getDescription());
        
        $attributes = $property->toArray();
        $this->assertEquals('json', $attributes['format'] ?? null);
    }

    public function testTransferProperty(): void
    {
        $propertyName = 'payment';
        $propertyLabel = 'Payment Transfer';
        
        $property = $this->traitUser->transfer($propertyName, $propertyLabel);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('object', $property->getType());
        $this->assertEquals($propertyLabel, $property->getDescription());
        
        $attributes = $property->toArray();
        $this->assertArrayHasKey('properties', $attributes);
        
        // Verify expected properties exist
        $expectedFields = ['source', 'destination', 'amount', 'currency', 'status', 'timestamp'];
        foreach ($expectedFields as $field) {
            $this->assertArrayHasKey($field, $attributes['properties']);
        }
        
        // Verify specific attribute values
        $this->assertEquals('number', $attributes['properties']['amount']['type']);
        $this->assertEquals(['pending', 'completed', 'failed'], $attributes['properties']['status']['enum']);
        $this->assertEquals('date-time', $attributes['properties']['timestamp']['format']);
    }

    public function testTableProperty(): void
    {
        $propertyName = 'usersTable';
        $propertyLabel = 'Users Table';
        
        $property = $this->traitUser->table($propertyName, $propertyLabel);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('object', $property->getType());
        $this->assertEquals($propertyLabel, $property->getDescription());
        
        $attributes = $property->toArray();
        $this->assertArrayHasKey('properties', $attributes);
        
        // Verify expected properties exist
        $expectedFields = ['columns', 'data', 'pagination'];
        foreach ($expectedFields as $field) {
            $this->assertArrayHasKey($field, $attributes['properties']);
        }
        
        // Verify types
        $this->assertEquals('array', $attributes['properties']['columns']['type']);
        $this->assertEquals('array', $attributes['properties']['data']['type']);
        $this->assertEquals('object', $attributes['properties']['pagination']['type']);
    }

    public function testDynamicFormProperty(): void
    {
        $propertyName = 'dynamicForm';
        $propertyLabel = 'Dynamic Form Builder';
        
        $property = $this->traitUser->dynamicForm($propertyName, $propertyLabel);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('object', $property->getType());
        $this->assertEquals($propertyLabel, $property->getDescription());
        
        $attributes = $property->toArray();
        $this->assertArrayHasKey('properties', $attributes);
        
        // Verify expected properties exist
        $this->assertArrayHasKey('fields', $attributes['properties']);
        $this->assertArrayHasKey('data', $attributes['properties']);
        
        // Verify types
        $this->assertEquals('array', $attributes['properties']['fields']['type']);
        $this->assertEquals('object', $attributes['properties']['data']['type']);
    }

    public function testMatrixProperty(): void
    {
        $propertyName = 'matrix';
        $propertyLabel = 'Data Matrix';
        
        $property = $this->traitUser->matrix($propertyName, $propertyLabel);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('object', $property->getType());
        $this->assertEquals($propertyLabel, $property->getDescription());
        
        $attributes = $property->toArray();
        $this->assertArrayHasKey('properties', $attributes);
        
        // Verify expected properties exist
        $expectedFields = ['rows', 'columns', 'cells'];
        foreach ($expectedFields as $field) {
            $this->assertArrayHasKey($field, $attributes['properties']);
        }
        
        // Verify all are arrays
        $this->assertEquals('array', $attributes['properties']['rows']['type']);
        $this->assertEquals('array', $attributes['properties']['columns']['type']);
        $this->assertEquals('array', $attributes['properties']['cells']['type']);
    }
}
