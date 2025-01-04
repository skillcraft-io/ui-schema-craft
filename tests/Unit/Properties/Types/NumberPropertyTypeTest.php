<?php

namespace Skillcraft\UiSchemaCraft\Tests\Unit\Properties\Types;

use Skillcraft\UiSchemaCraft\Tests\TestCase;
use Skillcraft\UiSchemaCraft\Properties\Types\NumberPropertyType;

class NumberPropertyTypeTest extends TestCase
{
    private NumberPropertyType $numberType;

    protected function setUp(): void
    {
        parent::setUp();
        $this->numberType = new NumberPropertyType();
    }

    /**
     * @dataProvider validNumberValuesProvider
     */
    public function test_validate_returns_true_for_valid_values($value): void
    {
        $this->assertTrue($this->numberType->validate($value));
    }

    public static function validNumberValuesProvider(): array
    {
        return [
            'integer' => [42],
            'negative integer' => [-42],
            'zero' => [0],
            'float' => [3.14],
            'negative float' => [-3.14],
            'string integer' => ['42'],
            'string float' => ['3.14'],
            'scientific notation' => ['1e-10'],
        ];
    }

    /**
     * @dataProvider invalidNumberValuesProvider
     */
    public function test_validate_returns_false_for_invalid_values($value): void
    {
        $this->assertFalse($this->numberType->validate($value));
    }

    public static function invalidNumberValuesProvider(): array
    {
        return [
            'null' => [null],
            'empty string' => [''],
            'non-numeric string' => ['not-a-number'],
            'array' => [[]],
            'object' => [new \stdClass()],
            'boolean' => [true],
        ];
    }

    public function test_validate_returns_false_when_below_min(): void
    {
        $this->numberType->setOption('min', 0);
        $this->assertFalse($this->numberType->validate(-1));
    }

    public function test_validate_returns_false_when_above_max(): void
    {
        $this->numberType->setOption('max', 100);
        $this->assertFalse($this->numberType->validate(101));
    }

    public function test_validate_returns_true_when_within_min_max(): void
    {
        $this->numberType->setOption('min', 0)
            ->setOption('max', 100);
        
        $this->assertTrue($this->numberType->validate(0));
        $this->assertTrue($this->numberType->validate(50));
        $this->assertTrue($this->numberType->validate(100));
    }

    public function test_validate_enforces_integer_constraint(): void
    {
        $this->numberType->setOption('integer', true);
        
        $this->assertTrue($this->numberType->validate(42));
        $this->assertTrue($this->numberType->validate('42'));
        $this->assertFalse($this->numberType->validate(3.14));
        $this->assertFalse($this->numberType->validate('3.14'));
    }

    /**
     * @dataProvider castValuesProvider
     */
    public function test_cast_converts_to_number($value, $expected, $isInteger = false): void
    {
        if ($isInteger) {
            $this->numberType->setOption('integer', true);
        }
        $this->assertEquals($expected, $this->numberType->cast($value));
    }

    public static function castValuesProvider(): array
    {
        return [
            'integer' => [42, 42.0],
            'float' => [3.14, 3.14],
            'string integer' => ['42', 42.0],
            'string float' => ['3.14', 3.14],
            'boolean true' => [true, 1.0],
            'boolean false' => [false, 0.0],
            'null' => [null, 0.0],
            'integer with integer option' => [42.7, 42, true],
            'negative integer with integer option' => [-42.7, -42, true],
        ];
    }

    public function test_cast_enforces_min_value(): void
    {
        $this->numberType->setOption('min', 0);
        $this->assertEquals(0, $this->numberType->cast(-1));
    }

    public function test_cast_enforces_max_value(): void
    {
        $this->numberType->setOption('max', 100);
        $this->assertEquals(100, $this->numberType->cast(101));
    }

    public function test_cast_enforces_integer_constraint(): void
    {
        $this->numberType->setOption('integer', true);
        
        $this->assertEquals(42, $this->numberType->cast(42.7));
        $this->assertEquals(-42, $this->numberType->cast(-42.7));
        $this->assertEquals(0, $this->numberType->cast(null));
    }

    public function test_get_js_type_returns_number_by_default(): void
    {
        $this->assertEquals('number', $this->numberType->getJsType());
    }

    public function test_get_js_type_returns_integer_when_configured(): void
    {
        $this->numberType->setOption('integer', true);
        $this->assertEquals('integer', $this->numberType->getJsType());
    }

    public function test_get_default_value_returns_zero_by_default(): void
    {
        $this->assertEquals(0, $this->numberType->getDefaultValue());
    }

    public function test_get_default_value_returns_configured_default(): void
    {
        $default = 42;
        $this->numberType->setOption('default', $default);
        
        $this->assertEquals($default, $this->numberType->getDefaultValue());
    }

    public function test_get_default_value_respects_integer_constraint(): void
    {
        $this->numberType->setOption('integer', true)
            ->setOption('default', 42.7);
        
        $this->assertEquals(42, $this->numberType->getDefaultValue());
    }

    public function test_get_default_options_returns_expected_defaults(): void
    {
        $expected = [
            'min' => null,
            'max' => null,
            'integer' => false,
            'default' => 0,
        ];

        $this->assertEquals($expected, $this->numberType->getOptions());
    }
}
