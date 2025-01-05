<?php

namespace Skillcraft\UiSchemaCraft\Properties\Types;

use Skillcraft\UiSchemaCraft\Properties\AbstractPropertyType;
use Skillcraft\UiSchemaCraft\Properties\PropertyTypeInterface;

class ArrayPropertyType extends AbstractPropertyType
{
    private ?PropertyTypeInterface $itemType = null;

    public function validate($value): bool
    {
        if (!is_array($value)) {
            return false;
        }

        $minItems = $this->getOption('minItems');
        if ($minItems !== null && count($value) < $minItems) {
            return false;
        }

        $maxItems = $this->getOption('maxItems');
        if ($maxItems !== null && count($value) > $maxItems) {
            return false;
        }

        if ($this->itemType) {
            foreach ($value as $item) {
                if (!$this->itemType->validate($item)) {
                    return false;
                }
            }
        }

        return true;
    }

    public function cast($value): array
    {
        if (!is_array($value)) {
            $value = [$value];
        }

        if ($this->itemType) {
            return array_map(
                fn($item) => $this->itemType->cast($item),
                $value
            );
        }

        return $value;
    }

    public function getJsType(): string
    {
        return 'array';
    }

    public function getDefaultValue(): array
    {
        return $this->getOption('default', []);
    }

    public function setItemType(PropertyTypeInterface $type): self
    {
        $this->itemType = $type;
        return $this;
    }

    public function getItemType(): ?PropertyTypeInterface
    {
        return $this->itemType;
    }

    protected function getDefaultOptions(): array
    {
        return [
            'minItems' => null,
            'maxItems' => null,
            'default' => [],
            'unique' => false,
        ];
    }
}
