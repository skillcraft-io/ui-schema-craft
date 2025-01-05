<?php

namespace Skillcraft\UiSchemaCraft\Tests\Unit\Schema\Traits;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversClass;
use Skillcraft\UiSchemaCraft\Tests\TestCase;
use Skillcraft\UiSchemaCraft\Schema\PropertyBuilder;
use Skillcraft\UiSchemaCraft\Schema\Traits\NumericTrait;

#[CoversClass(NumericTrait::class)]
class NumericTraitTest extends TestCase
{
    protected PropertyBuilder $builder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->builder = new PropertyBuilder();
    }

    #[Test]
    public function it_creates_range_property()
    {
        $property = $this->builder->range('quantity', 'Product Quantity');
        $schema = $property->toArray();

        $this->assertEquals('quantity', $schema['name']);
        $this->assertEquals('Product Quantity', $schema['description']);
        $this->assertEquals('object', $schema['type']);
        
        // Check value property
        $this->assertArrayHasKey('value', $schema['properties']);
        $value = $schema['properties']['value'];
        $this->assertEquals('number', $value['type']);
        $this->assertEquals(0, $value['default']);
        $this->assertEquals('Current value', $value['description']);
        
        // Check range constraints
        $this->assertArrayHasKey('min', $schema['properties']);
        $min = $schema['properties']['min'];
        $this->assertEquals('number', $min['type']);
        $this->assertEquals(0, $min['default']);
        $this->assertEquals('Minimum value', $min['description']);
        
        $this->assertArrayHasKey('max', $schema['properties']);
        $max = $schema['properties']['max'];
        $this->assertEquals('number', $max['type']);
        $this->assertEquals(100, $max['default']);
        $this->assertEquals('Maximum value', $max['description']);
        
        // Check step and display options
        $this->assertArrayHasKey('step', $schema['properties']);
        $step = $schema['properties']['step'];
        $this->assertEquals('number', $step['type']);
        $this->assertEquals(1, $step['default']);
        $this->assertEquals('Step increment', $step['description']);
        
        $this->assertArrayHasKey('showTicks', $schema['properties']);
        $showTicks = $schema['properties']['showTicks'];
        $this->assertEquals('boolean', $showTicks['type']);
        $this->assertFalse($showTicks['default']);
        $this->assertEquals('Show tick marks', $showTicks['description']);
        
        $this->assertArrayHasKey('showValue', $schema['properties']);
        $showValue = $schema['properties']['showValue'];
        $this->assertEquals('boolean', $showValue['type']);
        $this->assertTrue($showValue['default']);
        $this->assertEquals('Show current value', $showValue['description']);
    }

    #[Test]
    public function it_creates_range_with_default_label()
    {
        $property = $this->builder->range('temperature_control');
        $schema = $property->toArray();

        $this->assertEquals('temperature_control', $schema['name']);
        $this->assertEquals('Temperature Control', $schema['description']);
        $this->assertEquals('object', $schema['type']);
    }

    #[Test]
    public function it_creates_rating_property()
    {
        $property = $this->builder->rating('product_rating', 'Product Rating');
        $schema = $property->toArray();

        $this->assertEquals('product_rating', $schema['name']);
        $this->assertEquals('Product Rating', $schema['description']);
        $this->assertEquals('object', $schema['type']);
        
        // Check value property
        $this->assertArrayHasKey('value', $schema['properties']);
        $value = $schema['properties']['value'];
        $this->assertEquals('number', $value['type']);
        $this->assertEquals(0, $value['default']);
        $this->assertEquals('Current rating', $value['description']);
        
        // Check rating constraints
        $this->assertArrayHasKey('max', $schema['properties']);
        $max = $schema['properties']['max'];
        $this->assertEquals('number', $max['type']);
        $this->assertEquals(5, $max['default']);
        $this->assertEquals('Maximum rating', $max['description']);
        
        // Check rating options
        $this->assertArrayHasKey('allowHalf', $schema['properties']);
        $allowHalf = $schema['properties']['allowHalf'];
        $this->assertEquals('boolean', $allowHalf['type']);
        $this->assertFalse($allowHalf['default']);
        $this->assertEquals('Allow half ratings', $allowHalf['description']);
        
        $this->assertArrayHasKey('readonly', $schema['properties']);
        $readonly = $schema['properties']['readonly'];
        $this->assertEquals('boolean', $readonly['type']);
        $this->assertFalse($readonly['default']);
        $this->assertEquals('Read-only mode', $readonly['description']);
        
        // Check icon configuration
        $this->assertArrayHasKey('icon', $schema['properties']);
        $icon = $schema['properties']['icon'];
        $this->assertEquals('object', $icon['type']);
        
        $iconProps = $icon['properties'];
        $this->assertArrayHasKey('filled', $iconProps);
        $this->assertEquals('string', $iconProps['filled']['type']);
        $this->assertEquals('fas fa-star', $iconProps['filled']['default']);
        $this->assertEquals('Filled star icon', $iconProps['filled']['description']);
        
        $this->assertArrayHasKey('empty', $iconProps);
        $this->assertEquals('string', $iconProps['empty']['type']);
        $this->assertEquals('far fa-star', $iconProps['empty']['default']);
        $this->assertEquals('Empty star icon', $iconProps['empty']['description']);
        
        $this->assertArrayHasKey('color', $iconProps);
        $this->assertEquals('string', $iconProps['color']['type']);
        $this->assertEquals('text-yellow-400', $iconProps['color']['default']);
        $this->assertEquals('Star color', $iconProps['color']['description']);
    }

    #[Test]
    public function it_creates_rating_with_default_label()
    {
        $property = $this->builder->rating('customer_satisfaction');
        $schema = $property->toArray();

        $this->assertEquals('customer_satisfaction', $schema['name']);
        $this->assertEquals('Customer Satisfaction', $schema['description']);
        $this->assertEquals('object', $schema['type']);
    }

    #[Test]
    public function it_creates_currency_property()
    {
        $property = $this->builder->currency('price', 'Product Price');
        $schema = $property->toArray();

        $this->assertEquals('price', $schema['name']);
        $this->assertEquals('Product Price', $schema['description']);
        $this->assertEquals('object', $schema['type']);
        
        // Check value property
        $this->assertArrayHasKey('value', $schema['properties']);
        $value = $schema['properties']['value'];
        $this->assertEquals('number', $value['type']);
        $this->assertEquals('Amount value', $value['description']);
        
        // Check currency options
        $this->assertArrayHasKey('currency', $schema['properties']);
        $currency = $schema['properties']['currency'];
        $this->assertEquals('string', $currency['type']);
        $this->assertEquals('USD', $currency['default']);
        $this->assertEquals('Currency code', $currency['description']);
        
        $this->assertArrayHasKey('locale', $schema['properties']);
        $locale = $schema['properties']['locale'];
        $this->assertEquals('string', $locale['type']);
        $this->assertEquals('en-US', $locale['default']);
        $this->assertEquals('Locale for formatting', $locale['description']);

        $this->assertArrayHasKey('showSymbol', $schema['properties']);
        $showSymbol = $schema['properties']['showSymbol'];
        $this->assertEquals('boolean', $showSymbol['type']);
        $this->assertTrue($showSymbol['default']);
        $this->assertEquals('Show currency symbol', $showSymbol['description']);
    }

    #[Test]
    public function it_creates_currency_with_default_label()
    {
        $property = $this->builder->currency('total_amount');
        $schema = $property->toArray();

        $this->assertEquals('total_amount', $schema['name']);
        $this->assertEquals('Total Amount', $schema['description']);
        $this->assertEquals('object', $schema['type']);
    }

    #[Test]
    public function it_creates_percentage_property()
    {
        $property = $this->builder->percentage('completion_rate', 'Completion Rate');
        $schema = $property->toArray();

        $this->assertEquals('completion_rate', $schema['name']);
        $this->assertEquals('Completion Rate', $schema['description']);
        $this->assertEquals('object', $schema['type']);
        
        // Check value property
        $this->assertArrayHasKey('value', $schema['properties']);
        $value = $schema['properties']['value'];
        $this->assertEquals('number', $value['type']);
        $this->assertEquals('Percentage value', $value['description']);
        $this->assertEquals(0, $value['minimum']);
        $this->assertEquals(100, $value['maximum']);
        
        // Check display options
        $this->assertArrayHasKey('showSymbol', $schema['properties']);
        $showSymbol = $schema['properties']['showSymbol'];
        $this->assertEquals('boolean', $showSymbol['type']);
        $this->assertTrue($showSymbol['default']);
        $this->assertEquals('Show percentage symbol', $showSymbol['description']);
        
        // Check decimal places
        $this->assertArrayHasKey('decimals', $schema['properties']);
        $decimals = $schema['properties']['decimals'];
        $this->assertEquals('number', $decimals['type']);
        $this->assertEquals(0, $decimals['default']);
        $this->assertEquals(0, $decimals['minimum']);
        $this->assertEquals(20, $decimals['maximum']);
        $this->assertEquals('Number of decimal places', $decimals['description']);
    }

    #[Test]
    public function it_creates_percentage_with_default_label()
    {
        $property = $this->builder->percentage('success_rate');
        $schema = $property->toArray();

        $this->assertEquals('success_rate', $schema['name']);
        $this->assertEquals('Success Rate', $schema['description']);
        $this->assertEquals('object', $schema['type']);
    }
}
