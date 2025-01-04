<?php

namespace Skillcraft\UiSchemaCraft\Schema\Traits;

use Skillcraft\UiSchemaCraft\Schema\Property;

trait BasicTypesTrait
{
    /**
     * Create a string property.
     */
    public function string(string $name, ?string $label = null): Property
    {
        $property = new Property($name, 'string', $label);
        $this->properties[$name] = $property;
        return $property;
    }

    /**
     * Create a number property.
     */
    public function number(string $name, ?string $label = null): Property
    {
        $property = new Property($name, 'number', $label);
        $this->properties[$name] = $property;
        return $property;
    }

    /**
     * Create a boolean property.
     */
    public function boolean(string $name, ?string $label = null): Property
    {
        $property = new Property($name, 'boolean', $label);
        $this->properties[$name] = $property;
        return $property;
    }

    /**
     * Create an object property.
     */
    public function object(string $name, ?string $label = null): Property
    {
        $property = new Property($name, 'object', $label);
        $this->properties[$name] = $property;
        return $property;
    }

    /**
     * Create an array property.
     */
    public function array(string $name, ?string $label = null): Property
    {
        $property = new Property($name, 'array', $label);
        $this->properties[$name] = $property;
        return $property;
    }
}
