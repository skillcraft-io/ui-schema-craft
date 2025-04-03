<?php

namespace Skillcraft\UiSchemaCraft\Schema;

use Skillcraft\UiSchemaCraft\Validation\Rules\LaravelValidationRule;
use Skillcraft\UiSchemaCraft\Schema\Traits\AuthenticationTrait;
use Skillcraft\UiSchemaCraft\Schema\Traits\BasicTypesTrait;
use Skillcraft\UiSchemaCraft\Schema\Traits\ChartTrait;
use Skillcraft\UiSchemaCraft\Schema\Traits\DataHandlingTrait;
use Skillcraft\UiSchemaCraft\Schema\Traits\EditorTrait;
use Skillcraft\UiSchemaCraft\Schema\Traits\FormFieldsTrait;
use Skillcraft\UiSchemaCraft\Schema\Traits\LayoutTrait;
use Skillcraft\UiSchemaCraft\Schema\Traits\LocationTrait;
use Skillcraft\UiSchemaCraft\Schema\Traits\MediaTrait;
use Skillcraft\UiSchemaCraft\Schema\Traits\NumericTrait;
use Skillcraft\UiSchemaCraft\Schema\Traits\SelectionTrait;
use Skillcraft\UiSchemaCraft\Schema\Traits\TimeTrait;
use Skillcraft\UiSchemaCraft\Schema\Traits\UtilityTrait;
use Skillcraft\UiSchemaCraft\Schema\Traits\ValidationTrait;

class PropertyBuilder
{
    use AuthenticationTrait;
    use BasicTypesTrait;
    use ChartTrait;
    use DataHandlingTrait;
    use EditorTrait;
    use FormFieldsTrait;
    use LayoutTrait;
    use LocationTrait;
    use MediaTrait;
    use NumericTrait;
    use SelectionTrait;
    use TimeTrait;
    use UtilityTrait;
    use ValidationTrait;

    protected array $properties = [];

    public function __construct()
    {
        $this->properties = [];
    }

    public function new(): static
    {
        return new static();
    }

    public function add(Property $property): Property
    {
        $this->properties[$property->getName()] = $property;
        return $property;
    }

    public function toArray(): array
    {
        $properties = [];

        foreach ($this->properties as $property) {
            $data = $property->toArray();
            $name = $data['name'];
            unset($data['name']);

            // Move default to root level if it exists
            if (isset($data['default'])) {
                $properties[$name] = array_merge($data, ['default' => $data['default']]);
                unset($data['default']);
            } else {
                $properties[$name] = $data;
            }
        }

        return $properties;
    }

    public function merge(PropertyBuilder $other): static
    {
        foreach ($other->getProperties() as $property) {
            $this->properties[$property->getName()] = clone $property;
        }

        return $this;
    }

    public function prefix(string $prefix): static
    {
        $properties = [];
        
        foreach ($this->properties as $property) {
            $newProperty = clone $property;
            $newProperty->setName($prefix . $property->getName());
            $properties[$newProperty->getName()] = $newProperty;
        }

        $this->properties = $properties;
        return $this;
    }

    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * Create a new property with validation rules.
     */
    public function validate(string $name, array $rules, string $type = 'string'): Property
    {
        $property = new Property($name, $type);
        
        foreach ($rules as $rule) {
            $property->addRule($rule);
        }
        
        return $property;
    }
}