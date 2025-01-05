<?php

namespace Skillcraft\UiSchemaCraft\Tests\Unit\Properties;

use Skillcraft\UiSchemaCraft\Tests\TestCase;
use Skillcraft\UiSchemaCraft\Properties\AbstractPropertyType;

class AbstractPropertyTypeTest extends TestCase
{
    private ConcretePropertyType $propertyType;

    protected function setUp(): void
    {
        parent::setUp();
        $this->propertyType = new ConcretePropertyType();
    }

    public function test_constructor_merges_default_options(): void
    {
        $options = ['custom' => 'value'];
        $propertyType = new ConcretePropertyType($options);

        $expected = array_merge(
            ['default' => 'option'],
            $options
        );

        $this->assertEquals($expected, $propertyType->getOptions());
    }

    public function test_get_option_returns_default_when_not_set(): void
    {
        $default = 'default-value';
        $value = $this->propertyType->getOptionPublic('non-existent', $default);

        $this->assertEquals($default, $value);
    }

    public function test_get_option_returns_value_when_set(): void
    {
        $key = 'test-key';
        $value = 'test-value';
        
        $this->propertyType->setOption($key, $value);
        $result = $this->propertyType->getOptionPublic($key);

        $this->assertEquals($value, $result);
    }

    public function test_set_option_returns_self(): void
    {
        $result = $this->propertyType->setOption('key', 'value');

        $this->assertSame($this->propertyType, $result);
    }

    public function test_get_options_returns_all_options(): void
    {
        $options = [
            'key1' => 'value1',
            'key2' => 'value2',
        ];

        foreach ($options as $key => $value) {
            $this->propertyType->setOption($key, $value);
        }

        $expected = array_merge(
            ['default' => 'option'],
            $options
        );

        $this->assertEquals($expected, $this->propertyType->getOptions());
    }
}

/**
 * Concrete implementation for testing AbstractPropertyType
 */
class ConcretePropertyType extends AbstractPropertyType
{
    protected function getDefaultOptions(): array
    {
        return ['default' => 'option'];
    }

    public function getOptionPublic(string $key, mixed $default = null): mixed
    {
        return $this->getOption($key, $default);
    }

    public function validate($value): bool
    {
        return true;
    }

    public function cast($value): mixed
    {
        return $value;
    }

    public function getJsType(): string
    {
        return 'string';
    }

    public function getDefaultValue(): mixed
    {
        return null;
    }
}
