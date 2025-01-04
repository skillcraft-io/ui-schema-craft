<?php

namespace Tests\Unit\Schema\Traits;

use PHPUnit\Framework\TestCase;
use Skillcraft\UiSchemaCraft\Schema\Property;
use Skillcraft\UiSchemaCraft\Schema\Traits\ChartTrait;

class ChartTraitTest extends TestCase
{
    private $traitObject;

    protected function setUp(): void
    {
        $this->traitObject = new class {
            use ChartTrait;
        };
    }

    public function test_chart_creates_base_chart_property()
    {
        $property = $this->traitObject->chart('testChart', 'Test Chart');

        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals('testChart', $property->getName());
        $this->assertEquals('Test Chart', $property->getDescription());
        $this->assertEquals('object', $property->getType());

        $properties = $property->getAttribute('properties');
        $this->assertIsArray($properties);
        $this->assertArrayHasKey('type', $properties);
        $this->assertArrayHasKey('data', $properties);
        $this->assertArrayHasKey('options', $properties);
        
        $this->assertEquals(['line', 'bar', 'pie', 'scatter'], $properties['type']['enum']);
    }

    public function test_line_chart_creates_property_with_correct_type()
    {
        $property = $this->traitObject->lineChart('lineChart', 'Line Chart');

        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals('lineChart', $property->getName());
        $this->assertEquals('Line Chart', $property->getDescription());
        $this->assertEquals('line', $property->getAttribute('type'));
    }

    public function test_bar_chart_creates_property_with_correct_type()
    {
        $property = $this->traitObject->barChart('barChart', 'Bar Chart');

        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals('barChart', $property->getName());
        $this->assertEquals('Bar Chart', $property->getDescription());
        $this->assertEquals('bar', $property->getAttribute('type'));
    }

    public function test_pie_chart_creates_property_with_correct_type()
    {
        $property = $this->traitObject->pieChart('pieChart', 'Pie Chart');

        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals('pieChart', $property->getName());
        $this->assertEquals('Pie Chart', $property->getDescription());
        $this->assertEquals('pie', $property->getAttribute('type'));
    }

    public function test_scatter_plot_creates_property_with_correct_type()
    {
        $property = $this->traitObject->scatterPlot('scatterPlot', 'Scatter Plot');

        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals('scatterPlot', $property->getName());
        $this->assertEquals('Scatter Plot', $property->getDescription());
        $this->assertEquals('scatter', $property->getAttribute('type'));
    }

    public function test_chart_properties_without_label()
    {
        $property = $this->traitObject->chart('testChart');
        $this->assertNull($property->getDescription());

        $property = $this->traitObject->lineChart('lineChart');
        $this->assertNull($property->getDescription());

        $property = $this->traitObject->barChart('barChart');
        $this->assertNull($property->getDescription());

        $property = $this->traitObject->pieChart('pieChart');
        $this->assertNull($property->getDescription());

        $property = $this->traitObject->scatterPlot('scatterPlot');
        $this->assertNull($property->getDescription());
    }
}
