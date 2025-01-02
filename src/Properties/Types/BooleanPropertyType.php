<?php

namespace Skillcraft\UiSchemaCraft\Properties\Types;

use Skillcraft\UiSchemaCraft\Properties\AbstractPropertyType;

class BooleanPropertyType extends AbstractPropertyType
{
    public function validate($value): bool
    {
        return is_bool($value) || in_array($value, [0, 1, '0', '1', 'true', 'false'], true);
    }

    public function cast($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_string($value)) {
            return strtolower($value) === 'true' || $value === '1';
        }

        return (bool)$value;
    }

    public function getJsType(): string
    {
        return 'boolean';
    }

    public function getDefaultValue(): bool
    {
        return $this->getOption('default', false);
    }

    protected function getDefaultOptions(): array
    {
        return [
            'default' => false,
        ];
    }
}
