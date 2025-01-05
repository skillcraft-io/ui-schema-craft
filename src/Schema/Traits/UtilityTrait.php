<?php

namespace Skillcraft\UiSchemaCraft\Schema\Traits;

use Skillcraft\UiSchemaCraft\Schema\Property;
use Skillcraft\UiSchemaCraft\Schema\PropertyBuilder;

trait UtilityTrait
{
    /**
     * Create a group property.
     */
    public function group(string $name, callable $callback, ?string $description = null): Property
    {
        $property = new Property($name, 'object');
        $property->description($description ?? ucwords(str_replace('_', ' ', $name)));
        $builder = new PropertyBuilder();
        $callback($builder);
        $property->addAttribute('properties', $builder->toArray());
        return $property;
    }

    /**
     * Create a list property.
     */
    public function list(string $name, callable $callback, ?string $description = null): Property
    {
        $property = new Property($name, 'array');
        $property->description($description ?? ucwords(str_replace('_', ' ', $name)));
        $builder = new PropertyBuilder();
        $callback($builder);
        $property->addAttribute('items', [
            'type' => 'object',
            'properties' => $builder->toArray()
        ]);
        return $property;
    }

    /**
     * Create a conditional property.
     */
    public function conditional(string $name, array $conditions, callable $callback, ?string $description = null): Property
    {
        $property = new Property($name, 'object');
        $property->description($description ?? ucwords(str_replace('_', ' ', $name)));
        $builder = new PropertyBuilder();
        $callback($builder);
        
        $property->addAttribute('properties', $builder->toArray());
        $property->addAttribute('conditions', $conditions);
        
        return $property;
    }
}
