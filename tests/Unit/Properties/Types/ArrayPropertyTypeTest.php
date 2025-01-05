<?php

namespace Skillcraft\UiSchemaCraft\Tests\Unit\Properties\Types;

use Mockery;
use Mockery\MockInterface;
use Skillcraft\UiSchemaCraft\Tests\TestCase;
use Skillcraft\UiSchemaCraft\Properties\Types\ArrayPropertyType;
use Skillcraft\UiSchemaCraft\Properties\PropertyTypeInterface;

class ArrayPropertyTypeTest extends TestCase
{
    private ArrayPropertyType $arrayType;
    private MockInterface $itemType;

    protected function setUp(): void
    {
        parent::setUp();
        $this->arrayType = new ArrayPropertyType();
        $this->itemType = Mockery::mock(PropertyTypeInterface::class);
    }

    public function test_validate_returns_false_for_non_array(): void
    {
        $this->assertFalse($this->arrayType->validate('not-an-array'));
    }

    public function test_validate_returns_false_when_below_min_items(): void
    {
        $this->arrayType->setOption('minItems', 2);
        
        $this->assertFalse($this->arrayType->validate([1]));
    }

    public function test_validate_returns_false_when_above_max_items(): void
    {
        $this->arrayType->setOption('maxItems', 2);
        
        $this->assertFalse($this->arrayType->validate([1, 2, 3]));
    }

    public function test_validate_returns_true_when_within_min_max_items(): void
    {
        $this->arrayType->setOption('minItems', 2)
            ->setOption('maxItems', 3);
        
        $this->assertTrue($this->arrayType->validate([1, 2]));
        $this->assertTrue($this->arrayType->validate([1, 2, 3]));
    }

    public function test_validate_returns_false_when_item_type_validation_fails(): void
    {
        $value = [1, 2, 3];
        
        $this->itemType->expects('validate')
            ->times(count($value))
            ->andReturn(true, true, false);
            
        $this->arrayType->setItemType($this->itemType);
        
        $this->assertFalse($this->arrayType->validate($value));
    }

    public function test_validate_returns_true_when_item_type_validation_passes(): void
    {
        $value = [1, 2, 3];
        
        $this->itemType->expects('validate')
            ->times(count($value))
            ->andReturn(true);
            
        $this->arrayType->setItemType($this->itemType);
        
        $this->assertTrue($this->arrayType->validate($value));
    }

    public function test_cast_converts_non_array_to_array(): void
    {
        $value = 'single-value';
        $result = $this->arrayType->cast($value);
        
        $this->assertEquals([$value], $result);
    }

    public function test_cast_applies_item_type_casting(): void
    {
        $input = ['value1', 'value2'];
        $expected = ['CAST1', 'CAST2'];
        
        $this->itemType->expects('cast')
            ->times(2)
            ->andReturnUsing(fn($value) => 'CAST' . substr($value, -1));
            
        $this->arrayType->setItemType($this->itemType);
        
        $result = $this->arrayType->cast($input);
        
        $this->assertEquals($expected, $result);
    }

    public function test_cast_returns_array_as_is_without_item_type(): void
    {
        $input = [1, 2, 3];
        $result = $this->arrayType->cast($input);
        
        $this->assertSame($input, $result);
    }

    public function test_get_js_type_returns_array(): void
    {
        $this->assertEquals('array', $this->arrayType->getJsType());
    }

    public function test_get_default_value_returns_empty_array_by_default(): void
    {
        $this->assertEquals([], $this->arrayType->getDefaultValue());
    }

    public function test_get_default_value_returns_configured_default(): void
    {
        $default = [1, 2, 3];
        $this->arrayType->setOption('default', $default);
        
        $this->assertEquals($default, $this->arrayType->getDefaultValue());
    }

    public function test_set_item_type_returns_self(): void
    {
        $result = $this->arrayType->setItemType($this->itemType);
        
        $this->assertSame($this->arrayType, $result);
    }

    public function test_get_item_type_returns_null_by_default(): void
    {
        $this->assertNull($this->arrayType->getItemType());
    }

    public function test_get_item_type_returns_configured_type(): void
    {
        $this->arrayType->setItemType($this->itemType);
        
        $this->assertSame($this->itemType, $this->arrayType->getItemType());
    }

    public function test_get_default_options_returns_expected_defaults(): void
    {
        $expected = [
            'minItems' => null,
            'maxItems' => null,
            'default' => [],
            'unique' => false,
        ];

        $this->assertEquals($expected, $this->arrayType->getOptions());
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
