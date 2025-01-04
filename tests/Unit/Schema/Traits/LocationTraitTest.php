<?php

namespace Skillcraft\UiSchemaCraft\Tests\Unit\Schema\Traits;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversClass;
use Skillcraft\UiSchemaCraft\Tests\TestCase;
use Skillcraft\UiSchemaCraft\Schema\PropertyBuilder;
use Skillcraft\UiSchemaCraft\Schema\Traits\LocationTrait;

#[CoversClass(LocationTrait::class)]
class LocationTraitTest extends TestCase
{
    protected PropertyBuilder $builder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->builder = new PropertyBuilder();
    }

    #[Test]
    public function it_creates_coordinates_property()
    {
        $property = $this->builder->coordinates('location', 'Location Coordinates');
        $schema = $property->toArray();

        $this->assertEquals('location', $schema['name']);
        $this->assertEquals('Location Coordinates', $schema['description']);
        $this->assertEquals('object', $schema['type']);
        
        // Check value object properties
        $this->assertArrayHasKey('value', $schema['properties']);
        $value = $schema['properties']['value'];
        $this->assertEquals('object', $value['type']);
        
        // Check coordinate properties
        $this->assertArrayHasKey('latitude', $value['properties']);
        $this->assertArrayHasKey('longitude', $value['properties']);
        $this->assertArrayHasKey('altitude', $value['properties']);
        
        // Check property types and constraints
        $latitude = $value['properties']['latitude'];
        $this->assertEquals('number', $latitude['type']);
        $this->assertEquals(-90, $latitude['minimum']);
        $this->assertEquals(90, $latitude['maximum']);
        
        $longitude = $value['properties']['longitude'];
        $this->assertEquals('number', $longitude['type']);
        $this->assertEquals(-180, $longitude['minimum']);
        $this->assertEquals(180, $longitude['maximum']);
        
        $altitude = $value['properties']['altitude'];
        $this->assertEquals('number', $altitude['type']);
        $this->assertTrue($altitude['nullable']);
    }

    #[Test]
    public function it_creates_coordinates_with_default_label()
    {
        $property = $this->builder->coordinates('user_location');
        $schema = $property->toArray();

        $this->assertEquals('user_location', $schema['name']);
        $this->assertEquals('User Location', $schema['description']);
        $this->assertEquals('object', $schema['type']);
    }

    #[Test]
    public function it_creates_map_property()
    {
        $property = $this->builder->map('area_map', 'Area Map');
        $schema = $property->toArray();

        $this->assertEquals('area_map', $schema['name']);
        $this->assertEquals('Area Map', $schema['description']);
        $this->assertEquals('object', $schema['type']);
        
        // Check value object
        $this->assertArrayHasKey('value', $schema['properties']);
        $value = $schema['properties']['value'];
        $this->assertEquals('object', $value['type']);
        
        // Check center properties
        $this->assertArrayHasKey('center', $value['properties']);
        $center = $value['properties']['center'];
        $this->assertEquals('object', $center['type']);
        $this->assertArrayHasKey('latitude', $center['properties']);
        $this->assertArrayHasKey('longitude', $center['properties']);
        $this->assertEquals('number', $center['properties']['latitude']['type']);
        $this->assertEquals('number', $center['properties']['longitude']['type']);
        
        // Check zoom property
        $this->assertArrayHasKey('zoom', $value['properties']);
        $zoom = $value['properties']['zoom'];
        $this->assertEquals('number', $zoom['type']);
        $this->assertEquals(0, $zoom['minimum']);
        $this->assertEquals(20, $zoom['maximum']);
        $this->assertEquals(13, $zoom['default']);
        
        // Check markers property
        $this->assertArrayHasKey('markers', $value['properties']);
        $markers = $value['properties']['markers'];
        $this->assertEquals('array', $markers['type']);
        
        // Check marker item properties
        $markerItem = $markers['items'];
        $this->assertEquals('object', $markerItem['type']);
        $this->assertArrayHasKey('latitude', $markerItem['properties']);
        $this->assertArrayHasKey('longitude', $markerItem['properties']);
        $this->assertArrayHasKey('title', $markerItem['properties']);
        $this->assertArrayHasKey('description', $markerItem['properties']);
        
        // Check marker property types
        $this->assertEquals('number', $markerItem['properties']['latitude']['type']);
        $this->assertEquals('number', $markerItem['properties']['longitude']['type']);
        $this->assertEquals('string', $markerItem['properties']['title']['type']);
        $this->assertEquals('string', $markerItem['properties']['description']['type']);
    }

    #[Test]
    public function it_creates_map_with_default_label()
    {
        $property = $this->builder->map('store_locations');
        $schema = $property->toArray();

        $this->assertEquals('store_locations', $schema['name']);
        $this->assertEquals('Store Locations', $schema['description']);
        $this->assertEquals('object', $schema['type']);
    }

    #[Test]
    public function it_creates_cascader_property()
    {
        $property = $this->builder->cascader('region_selector', 'Region Selection');
        $schema = $property->toArray();

        $this->assertEquals('region_selector', $schema['name']);
        $this->assertEquals('Region Selection', $schema['description']);
        $this->assertEquals('object', $schema['type']);
        
        // Check value array
        $this->assertArrayHasKey('value', $schema['properties']);
        $value = $schema['properties']['value'];
        $this->assertEquals('array', $value['type']);
        
        // Check item properties
        $item = $value['items'];
        $this->assertEquals('object', $item['type']);
        $this->assertArrayHasKey('value', $item['properties']);
        $this->assertArrayHasKey('label', $item['properties']);
        $this->assertArrayHasKey('children', $item['properties']);
        
        // Check property types
        $this->assertEquals('string', $item['properties']['value']['type']);
        $this->assertEquals('string', $item['properties']['label']['type']);
        $this->assertEquals('array', $item['properties']['children']['type']);
        
        // Check configuration options
        $this->assertArrayHasKey('multiple', $schema['properties']);
        $this->assertArrayHasKey('clearable', $schema['properties']);
        $this->assertFalse($schema['properties']['multiple']['default']);
        $this->assertTrue($schema['properties']['clearable']['default']);
    }

    #[Test]
    public function it_creates_cascader_with_default_label()
    {
        $property = $this->builder->cascader('location_hierarchy');
        $schema = $property->toArray();

        $this->assertEquals('location_hierarchy', $schema['name']);
        $this->assertEquals('Location Hierarchy', $schema['description']);
        $this->assertEquals('object', $schema['type']);
    }
}
