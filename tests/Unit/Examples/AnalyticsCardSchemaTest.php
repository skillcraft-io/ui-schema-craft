<?php

namespace Skillcraft\UiSchemaCraft\Tests\Unit\Examples;

use Skillcraft\UiSchemaCraft\Tests\TestCase;
use Skillcraft\UiSchemaCraft\Examples\AnalyticsCardSchema;

class AnalyticsCardSchemaTest extends TestCase
{
    private AnalyticsCardSchema $schema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->schema = new AnalyticsCardSchema();
    }

    /** @test */
    public function it_has_correct_component_type()
    {
        $schema = $this->schema->toArray();
        $this->assertEquals('analytics-card', $this->schema->getIdentifier());
        $this->assertEquals('card-component', $schema['component']);
    }

    /** @test */
    public function it_has_all_required_fields()
    {
        $properties = $this->schema->properties();
        
        $this->assertArrayHasKey('title', $properties);
        $this->assertArrayHasKey('metric', $properties);
        $this->assertArrayHasKey('change', $properties);
        $this->assertArrayHasKey('timeRange', $properties);
        $this->assertArrayHasKey('visualization', $properties);
        $this->assertArrayHasKey('showComparison', $properties);
        $this->assertArrayHasKey('currency', $properties);
        $this->assertArrayHasKey('dataPoints', $properties);
    }

    /** @test */
    public function it_has_correct_default_values()
    {
        $properties = $this->schema->properties();
        
        $this->assertEquals('', $properties['title']['default']);
        $this->assertEquals(0, $properties['metric']['default']);
        $this->assertEquals(0, $properties['change']['default']);
        $this->assertEquals('30d', $properties['timeRange']['default']);
        $this->assertEquals('line', $properties['visualization']['default']);
        $this->assertTrue($properties['showComparison']['default']);
        $this->assertEquals('USD', $properties['currency']['default']);
        $this->assertEquals([], $properties['dataPoints']['default']);
    }

    /** @test */
    public function it_validates_time_range_values()
    {
        $properties = $this->schema->properties();
        
        $this->assertContains('in:7d,30d,90d,1y', $properties['timeRange']['rules']);
    }

    /** @test */
    public function it_validates_visualization_types()
    {
        $properties = $this->schema->properties();
        
        $this->assertContains('in:line,bar,area', $properties['visualization']['rules']);
    }

    /** @test */
    public function it_validates_currency_format()
    {
        $properties = $this->schema->properties();
        
        $this->assertContains('size:3', $properties['currency']['rules']);
    }

    /** @test */
    public function it_requires_data_points()
    {
        $properties = $this->schema->properties();
        
        $this->assertContains('required', $properties['dataPoints']['rules']);
        $this->assertContains('array', $properties['dataPoints']['rules']);
        $this->assertContains('min:1', $properties['dataPoints']['rules']);
    }

    /** @test */
    public function it_provides_valid_example_data()
    {
        $exampleData = $this->schema->getExampleData();
        
        $this->assertIsString($exampleData['title']);
        $this->assertIsNumeric($exampleData['metric']);
        $this->assertIsNumeric($exampleData['change']);
        $this->assertContains($exampleData['timeRange'], ['7d', '30d', '90d', '1y']);
        $this->assertContains($exampleData['visualization'], ['line', 'bar', 'area']);
        $this->assertIsBool($exampleData['showComparison']);
        $this->assertEquals(3, strlen($exampleData['currency']));
        $this->assertIsArray($exampleData['dataPoints']);
        $this->assertNotEmpty($exampleData['dataPoints']);
        
        // Check data point structure
        $dataPoint = $exampleData['dataPoints'][0];
        $this->assertArrayHasKey('date', $dataPoint);
        $this->assertArrayHasKey('value', $dataPoint);
        $this->assertIsString($dataPoint['date']);
        $this->assertIsNumeric($dataPoint['value']);
    }
}
