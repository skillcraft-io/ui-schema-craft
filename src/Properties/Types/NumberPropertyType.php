<?php

namespace Skillcraft\UiSchemaCraft\Properties\Types;

use Skillcraft\UiSchemaCraft\Properties\AbstractPropertyType;

class NumberPropertyType extends AbstractPropertyType
{
    public function validate($value): bool
    {
        if (!is_numeric($value)) {
            return false;
        }

        $min = $this->getOption('min');
        if ($min !== null && $value < $min) {
            return false;
        }

        $max = $this->getOption('max');
        if ($max !== null && $value > $max) {
            return false;
        }

        if ($this->getOption('integer', false) && !is_int($value) && !ctype_digit((string)$value)) {
            return false;
        }

        return true;
    }

    public function cast($value): float|int
    {
        $number = $this->getOption('integer', false) ? (int)$value : (float)$value;
        
        $min = $this->getOption('min');
        if ($min !== null) {
            $number = max($min, $number);
        }

        $max = $this->getOption('max');
        if ($max !== null) {
            $number = min($max, $number);
        }

        return $number;
    }

    public function getJsType(): string
    {
        return $this->getOption('integer', false) ? 'integer' : 'number';
    }

    public function getDefaultValue(): float|int
    {
        return $this->getOption('default', 0);
    }

    protected function getDefaultOptions(): array
    {
        return [
            'min' => null,
            'max' => null,
            'integer' => false,
            'default' => 0,
        ];
    }
}
