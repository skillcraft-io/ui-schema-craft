<?php

namespace Skillcraft\UiSchemaCraft\Tests\Unit\Properties\Types;

use Skillcraft\UiSchemaCraft\Tests\TestCase;
use Skillcraft\UiSchemaCraft\Properties\Types\BooleanPropertyType;

class BooleanPropertyTypeTest extends TestCase
{
    private BooleanPropertyType $booleanType;

    protected function setUp(): void
    {
        parent::setUp();
        $this->booleanType = new BooleanPropertyType();
    }

    /**
     * @dataProvider validBooleanValuesProvider
     */
    public function test_validate_returns_true_for_valid_values($value): void
    {
        $this->assertTrue($this->booleanType->validate($value));
    }

    public static function validBooleanValuesProvider(): array
    {
        return [
            'true' => [true],
            'false' => [false],
            'string true' => ['true'],
            'string false' => ['false'],
            'string 1' => ['1'],
            'string 0' => ['0'],
            'integer 1' => [1],
            'integer 0' => [0],
        ];
    }

    /**
     * @dataProvider invalidBooleanValuesProvider
     */
    public function test_validate_returns_false_for_invalid_values($value): void
    {
        $this->assertFalse($this->booleanType->validate($value));
    }

    public static function invalidBooleanValuesProvider(): array
    {
        return [
            'null' => [null],
            'empty string' => [''],
            'non-boolean string' => ['not-a-boolean'],
            'integer 2' => [2],
            'array' => [[]],
            'object' => [new \stdClass()],
        ];
    }

    /**
     * @dataProvider castTrueValuesProvider
     */
    public function test_cast_returns_true_for_truthy_values($value): void
    {
        $this->assertTrue($this->booleanType->cast($value));
    }

    public static function castTrueValuesProvider(): array
    {
        return [
            'boolean true' => [true],
            'string true' => ['true'],
            'string TRUE' => ['TRUE'],
            'string 1' => ['1'],
            'integer 1' => [1],
            'non-empty array' => [[1]],
            'non-zero float' => [1.5],
        ];
    }

    /**
     * @dataProvider castFalseValuesProvider
     */
    public function test_cast_returns_false_for_falsy_values($value): void
    {
        $this->assertFalse($this->booleanType->cast($value));
    }

    public static function castFalseValuesProvider(): array
    {
        return [
            'boolean false' => [false],
            'string false' => ['false'],
            'string FALSE' => ['FALSE'],
            'string 0' => ['0'],
            'integer 0' => [0],
            'empty array' => [[]],
            'null' => [null],
            'empty string' => [''],
            'zero float' => [0.0],
        ];
    }

    public function test_get_js_type_returns_boolean(): void
    {
        $this->assertEquals('boolean', $this->booleanType->getJsType());
    }

    public function test_get_default_value_returns_false_by_default(): void
    {
        $this->assertFalse($this->booleanType->getDefaultValue());
    }

    public function test_get_default_value_returns_configured_default(): void
    {
        $this->booleanType->setOption('default', true);
        $this->assertTrue($this->booleanType->getDefaultValue());
    }

    public function test_get_default_options_returns_expected_defaults(): void
    {
        $expected = [
            'default' => false,
        ];

        $this->assertEquals($expected, $this->booleanType->getOptions());
    }
}
