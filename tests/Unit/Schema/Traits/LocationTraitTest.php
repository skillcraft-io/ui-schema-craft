<?php

namespace Skillcraft\UiSchemaCraft\Tests\Unit\Schema\Traits;

use Skillcraft\UiSchemaCraft\Tests\TestCase;
use Skillcraft\UiSchemaCraft\Schema\Property;
use Skillcraft\UiSchemaCraft\Schema\PropertyBuilder;
use Skillcraft\UiSchemaCraft\Schema\Traits\LocationTrait;

class LocationTraitTest extends TestCase
{
    /**
     * Test class that uses the LocationTrait
     */
    private $traitUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test class that uses the trait
        $this->traitUser = new class {
            use LocationTrait;
            
            public function object(string $name, ?string $description = null): Property
            {
                return new Property($name, 'object', $description);
            }
            
            public function withBuilder(callable $callback): mixed
            {
                $builder = new PropertyBuilder();
                $callback($builder);
                return $this;
            }
        };
    }

    public function testCoordinatesProperty(): void
    {
        $propertyName = 'location';
        $propertyLabel = 'Location Coordinates';
        
        $property = $this->traitUser->coordinates($propertyName, $propertyLabel);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('object', $property->getType());
        $this->assertEquals($propertyLabel, $property->getDescription());
    }
    
    public function testCoordinatesPropertyWithAutoLabel(): void
    {
        $propertyName = 'gps_coordinates';
        
        $property = $this->traitUser->coordinates($propertyName);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('object', $property->getType());
        $this->assertEquals('Gps Coordinates', $property->getDescription());
    }
    
    public function testCoordinatesHasCorrectStructure(): void
    {
        $propertyName = 'position';
        $propertyLabel = 'Position';
        
        // Create a real property object with the proper builder
        $mockTraitUser = new class {
            use LocationTrait;
            
            public function object(string $name, ?string $description = null): Property
            {
                return new Property($name, 'object', $description);
            }
        };
        
        $property = $mockTraitUser->coordinates($propertyName, $propertyLabel);
        $attributes = $property->toArray();
        
        // Check if builder added the value object with coordinates
        $this->assertArrayHasKey('properties', $attributes);
        if (isset($attributes['properties'])) {
            $this->assertArrayHasKey('value', $attributes['properties']);
            $this->assertArrayHasKey('properties', $attributes['properties']['value']);
            
            $valueProperties = $attributes['properties']['value']['properties'];
            
            // Check latitude
            $this->assertArrayHasKey('latitude', $valueProperties);
            $this->assertEquals('number', $valueProperties['latitude']['type']);
            $this->assertEquals(-90, $valueProperties['latitude']['minimum']);
            $this->assertEquals(90, $valueProperties['latitude']['maximum']);
            
            // Check longitude
            $this->assertArrayHasKey('longitude', $valueProperties);
            $this->assertEquals('number', $valueProperties['longitude']['type']);
            $this->assertEquals(-180, $valueProperties['longitude']['minimum']);
            $this->assertEquals(180, $valueProperties['longitude']['maximum']);
            
            // Check altitude
            $this->assertArrayHasKey('altitude', $valueProperties);
            $this->assertEquals('number', $valueProperties['altitude']['type']);
            $this->assertEquals(true, $valueProperties['altitude']['nullable']);
        }
    }

    public function testMapProperty(): void
    {
        $propertyName = 'locationMap';
        $propertyLabel = 'Location Map';
        
        $property = $this->traitUser->map($propertyName, $propertyLabel);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('object', $property->getType());
        $this->assertEquals($propertyLabel, $property->getDescription());
    }
    
    public function testMapPropertyWithAutoLabel(): void
    {
        $propertyName = 'city_map';
        
        $property = $this->traitUser->map($propertyName);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('object', $property->getType());
        $this->assertEquals('City Map', $property->getDescription());
    }
    
    public function testMapHasCorrectStructure(): void
    {
        $propertyName = 'storeMap';
        $propertyLabel = 'Store Map';
        
        // Create a real property object with the proper builder
        $mockTraitUser = new class {
            use LocationTrait;
            
            public function object(string $name, ?string $description = null): Property
            {
                return new Property($name, 'object', $description);
            }
        };
        
        $property = $mockTraitUser->map($propertyName, $propertyLabel);
        $attributes = $property->toArray();
        
        // Check if builder added the value object with map properties
        $this->assertArrayHasKey('properties', $attributes);
        if (isset($attributes['properties'])) {
            $this->assertArrayHasKey('value', $attributes['properties']);
            $this->assertArrayHasKey('properties', $attributes['properties']['value']);
            
            $valueProperties = $attributes['properties']['value']['properties'];
            
            // Check center
            $this->assertArrayHasKey('center', $valueProperties);
            $this->assertEquals('object', $valueProperties['center']['type']);
            $this->assertArrayHasKey('properties', $valueProperties['center']);
            $this->assertArrayHasKey('latitude', $valueProperties['center']['properties']);
            $this->assertArrayHasKey('longitude', $valueProperties['center']['properties']);
            
            // Check zoom
            $this->assertArrayHasKey('zoom', $valueProperties);
            $this->assertEquals('number', $valueProperties['zoom']['type']);
            $this->assertEquals(0, $valueProperties['zoom']['minimum']);
            $this->assertEquals(20, $valueProperties['zoom']['maximum']);
            $this->assertEquals(13, $valueProperties['zoom']['default']);
            
            // Check markers
            $this->assertArrayHasKey('markers', $valueProperties);
            $this->assertEquals('array', $valueProperties['markers']['type']);
            $this->assertArrayHasKey('items', $valueProperties['markers']);
            $this->assertEquals('object', $valueProperties['markers']['items']['type']);
            $this->assertArrayHasKey('properties', $valueProperties['markers']['items']);
            
            // Check marker properties
            $markerProps = $valueProperties['markers']['items']['properties'];
            $expectedMarkerProps = ['latitude', 'longitude', 'title', 'description'];
            foreach ($expectedMarkerProps as $prop) {
                $this->assertArrayHasKey($prop, $markerProps);
            }
        }
    }

    public function testCascaderProperty(): void
    {
        $propertyName = 'regionSelector';
        $propertyLabel = 'Region Selector';
        
        $property = $this->traitUser->cascader($propertyName, $propertyLabel);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('object', $property->getType());
        $this->assertEquals($propertyLabel, $property->getDescription());
    }
    
    public function testCascaderPropertyWithAutoLabel(): void
    {
        $propertyName = 'location_selector';
        
        $property = $this->traitUser->cascader($propertyName);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('object', $property->getType());
        $this->assertEquals('Location Selector', $property->getDescription());
    }
    
    public function testCascaderHasCorrectStructure(): void
    {
        $propertyName = 'addressSelector';
        $propertyLabel = 'Address Selector';
        
        // Create a real property object with the proper builder
        $mockTraitUser = new class {
            use LocationTrait;
            
            public function object(string $name, ?string $description = null): Property
            {
                return new Property($name, 'object', $description);
            }
        };
        
        $property = $mockTraitUser->cascader($propertyName, $propertyLabel);
        $attributes = $property->toArray();
        
        // Check if builder added the required properties
        $this->assertArrayHasKey('properties', $attributes);
        if (isset($attributes['properties'])) {
            // Check value array
            $this->assertArrayHasKey('value', $attributes['properties']);
            $this->assertEquals('array', $attributes['properties']['value']['type']);
            
            // Check items structure in value array
            $this->assertArrayHasKey('items', $attributes['properties']['value']);
            $itemProps = $attributes['properties']['value']['items']['properties'];
            $expectedItemProps = ['value', 'label', 'children'];
            foreach ($expectedItemProps as $prop) {
                $this->assertArrayHasKey($prop, $itemProps);
            }
            
            // Check multiple flag
            $this->assertArrayHasKey('multiple', $attributes['properties']);
            $this->assertEquals('boolean', $attributes['properties']['multiple']['type']);
            $this->assertEquals(false, $attributes['properties']['multiple']['default']);
            
            // Check clearable flag
            $this->assertArrayHasKey('clearable', $attributes['properties']);
            $this->assertEquals('boolean', $attributes['properties']['clearable']['type']);
            $this->assertEquals(true, $attributes['properties']['clearable']['default']);
        }
    }
}
