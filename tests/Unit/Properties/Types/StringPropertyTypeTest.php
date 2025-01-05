<?php

namespace Skillcraft\UiSchemaCraft\Tests\Unit\Properties\Types;

use stdClass;
use Skillcraft\UiSchemaCraft\Tests\TestCase;
use Skillcraft\UiSchemaCraft\Properties\Types\StringPropertyType;

class StringPropertyTypeTest extends TestCase
{
    private StringPropertyType $stringType;

    protected function setUp(): void
    {
        parent::setUp();
        $this->stringType = new StringPropertyType();
    }

    /**
     * @dataProvider validStringValuesProvider
     */
    public function test_validate_returns_true_for_valid_values($value): void
    {
        $this->assertTrue($this->stringType->validate($value));
    }

    public static function validStringValuesProvider(): array
    {
        return [
            'empty string' => [''],
            'simple string' => ['hello'],
            'numeric string' => ['123'],
            'special characters' => ['!@#$%^&*()'],
            'unicode string' => ['こんにちは'],
            'multiline string' => ["line1\nline2"],
        ];
    }

    /**
     * @dataProvider invalidStringValuesProvider
     */
    public function test_validate_returns_false_for_invalid_values($value): void
    {
        $this->assertFalse($this->stringType->validate($value));
    }

    public static function invalidStringValuesProvider(): array
    {
        return [
            'null' => [null],
            'integer' => [123],
            'float' => [123.45],
            'boolean' => [true],
            'array' => [[]],
            'object' => [new stdClass()],
        ];
    }

    public function test_validate_returns_false_when_pattern_does_not_match(): void
    {
        $this->stringType->setOption('pattern', '/^[0-9]+$/');
        
        $this->assertFalse($this->stringType->validate('abc'));
        $this->assertTrue($this->stringType->validate('123'));
    }

    public function test_validate_returns_false_when_below_min_length(): void
    {
        $this->stringType->setOption('minLength', 3);
        
        $this->assertFalse($this->stringType->validate('ab'));
        $this->assertTrue($this->stringType->validate('abc'));
    }

    public function test_validate_returns_false_when_above_max_length(): void
    {
        $this->stringType->setOption('maxLength', 3);
        
        $this->assertTrue($this->stringType->validate('abc'));
        $this->assertFalse($this->stringType->validate('abcd'));
    }

    public function test_validate_returns_true_when_within_length_constraints(): void
    {
        $this->stringType->setOption('minLength', 2)
            ->setOption('maxLength', 4);
        
        $this->assertFalse($this->stringType->validate('a'));
        $this->assertTrue($this->stringType->validate('ab'));
        $this->assertTrue($this->stringType->validate('abc'));
        $this->assertTrue($this->stringType->validate('abcd'));
        $this->assertFalse($this->stringType->validate('abcde'));
    }

    /**
     * @dataProvider castValuesProvider
     */
    public function test_cast_converts_to_string($value, $expected): void
    {
        $this->assertSame($expected, $this->stringType->cast($value));
    }

    public static function castValuesProvider(): array
    {
        $obj = new stdClass();
        $obj->key = 'value';
        
        return [
            'null' => [null, ''],
            'empty string' => ['', ''],
            'string' => ['hello', 'hello'],
            'integer' => [123, '123'],
            'float' => [123.45, '123.45'],
            'boolean true' => [true, '1'],
            'boolean false' => [false, ''],
            'array' => [['a', 'b'], '["a","b"]'],
            'object' => [$obj, '{"key":"value"}'],
        ];
    }

    public function test_get_js_type_returns_string(): void
    {
        $this->assertEquals('string', $this->stringType->getJsType());
    }

    public function test_get_default_value_returns_empty_string_by_default(): void
    {
        $this->assertSame('', $this->stringType->getDefaultValue());
    }

    public function test_get_default_value_returns_configured_default(): void
    {
        $default = 'default value';
        $this->stringType->setOption('default', $default);
        
        $this->assertSame($default, $this->stringType->getDefaultValue());
    }

    public function test_get_default_options_returns_expected_defaults(): void
    {
        $expected = [
            'minLength' => null,
            'maxLength' => null,
            'pattern' => null,
            'default' => '',
        ];

        $this->assertEquals($expected, $this->stringType->getOptions());
    }
}
