<?php

namespace Skillcraft\UiSchemaCraft\Properties\Types;

use Skillcraft\UiSchemaCraft\Properties\AbstractPropertyType;

class StringPropertyType extends AbstractPropertyType
{
    public function validate($value): bool
    {
        if (!is_string($value)) {
            return false;
        }

        $pattern = $this->getOption('pattern');
        if ($pattern && !preg_match($pattern, $value)) {
            return false;
        }

        $minLength = $this->getOption('minLength');
        if ($minLength !== null && strlen($value) < $minLength) {
            return false;
        }

        $maxLength = $this->getOption('maxLength');
        if ($maxLength !== null && strlen($value) > $maxLength) {
            return false;
        }

        return true;
    }

    public function cast($value): string
    {
        if (is_array($value) || is_object($value)) {
            return json_encode($value);
        }

        return (string) $value;
    }

    public function getJsType(): string
    {
        return 'string';
    }

    public function getDefaultValue(): string
    {
        return $this->getOption('default', '');
    }

    protected function getDefaultOptions(): array
    {
        return [
            'minLength' => null,
            'maxLength' => null,
            'pattern' => null,
            'default' => '',
        ];
    }
}
