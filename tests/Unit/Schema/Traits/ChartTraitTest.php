<?php

namespace Skillcraft\UiSchemaCraft\Tests\Unit\Schema\Traits;

use Skillcraft\UiSchemaCraft\Tests\TestCase;
use Skillcraft\UiSchemaCraft\Schema\Property;
use Skillcraft\UiSchemaCraft\Schema\Traits\ChartTrait;

class ChartTraitTest extends TestCase
{
    /**
     * Test class that uses the ChartTrait
     */
    private $traitUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test class that uses the trait
        $this->traitUser = new class {
            use ChartTrait;
        };
    }

    public function testChartProperty(): void
    {
        $propertyName = 'testChart';
        $propertyLabel = 'Test Chart';
        
        $property = $this->traitUser->chart($propertyName, $propertyLabel);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('object', $property->getType());
        $this->assertEquals($propertyLabel, $property->getDescription());
        
        // Check that the expected attributes are set
        $attributes = $property->toArray();
        $this->assertArrayHasKey('properties', $attributes);
        
        // Verify chart structure
        $properties = $attributes['properties'];
        $this->assertArrayHasKey('type', $properties);
        $this->assertArrayHasKey('data', $properties);
        $this->assertArrayHasKey('options', $properties);
        
        // Verify type property has expected enum values
        $this->assertEquals('string', $properties['type']['type']);
        $this->assertArrayHasKey('enum', $properties['type']);
        $this->assertEquals(['line', 'bar', 'pie', 'scatter'], $properties['type']['enum']);
    }

    public function testLineChartProperty(): void
    {
        $propertyName = 'testLineChart';
        $propertyLabel = 'Test Line Chart';
        
        $property = $this->traitUser->lineChart($propertyName, $propertyLabel);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('object', $property->getType());
        $this->assertEquals($propertyLabel, $property->getDescription());
        
        // Check chart type is line
        $attributes = $property->toArray();
        $this->assertArrayHasKey('type', $attributes);
        $this->assertEquals('line', $attributes['type']);
        
        // Verify base chart properties are also present
        $this->assertArrayHasKey('properties', $attributes);
        $this->assertArrayHasKey('type', $attributes['properties']);
        $this->assertArrayHasKey('data', $attributes['properties']);
        $this->assertArrayHasKey('options', $attributes['properties']);
    }

    public function testBarChartProperty(): void
    {
        $propertyName = 'testBarChart';
        $propertyLabel = 'Test Bar Chart';
        
        $property = $this->traitUser->barChart($propertyName, $propertyLabel);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('object', $property->getType());
        $this->assertEquals($propertyLabel, $property->getDescription());
        
        // Check chart type is bar
        $attributes = $property->toArray();
        $this->assertArrayHasKey('type', $attributes);
        $this->assertEquals('bar', $attributes['type']);
        
        // Verify base chart properties are also present
        $this->assertArrayHasKey('properties', $attributes);
        $this->assertArrayHasKey('type', $attributes['properties']);
        $this->assertArrayHasKey('data', $attributes['properties']);
        $this->assertArrayHasKey('options', $attributes['properties']);
    }

    public function testPieChartProperty(): void
    {
        $propertyName = 'testPieChart';
        $propertyLabel = 'Test Pie Chart';
        
        $property = $this->traitUser->pieChart($propertyName, $propertyLabel);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('object', $property->getType());
        $this->assertEquals($propertyLabel, $property->getDescription());
        
        // Check chart type is pie
        $attributes = $property->toArray();
        $this->assertArrayHasKey('type', $attributes);
        $this->assertEquals('pie', $attributes['type']);
        
        // Verify base chart properties are also present
        $this->assertArrayHasKey('properties', $attributes);
        $this->assertArrayHasKey('type', $attributes['properties']);
        $this->assertArrayHasKey('data', $attributes['properties']);
        $this->assertArrayHasKey('options', $attributes['properties']);
    }

    public function testScatterPlotProperty(): void
    {
        $propertyName = 'testScatterPlot';
        $propertyLabel = 'Test Scatter Plot';
        
        $property = $this->traitUser->scatterPlot($propertyName, $propertyLabel);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('object', $property->getType());
        $this->assertEquals($propertyLabel, $property->getDescription());
        
        // Check chart type is scatter
        $attributes = $property->toArray();
        $this->assertArrayHasKey('type', $attributes);
        $this->assertEquals('scatter', $attributes['type']);
        
        // Verify base chart properties are also present
        $this->assertArrayHasKey('properties', $attributes);
        $this->assertArrayHasKey('type', $attributes['properties']);
        $this->assertArrayHasKey('data', $attributes['properties']);
        $this->assertArrayHasKey('options', $attributes['properties']);
    }
}
