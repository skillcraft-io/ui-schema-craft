<?php

namespace Skillcraft\UiSchemaCraft\Schema\Traits;

use Skillcraft\UiSchemaCraft\Schema\Property;

trait DataHandlingTrait
{
    /**
     * Create an ID property.
     */
    public function id(string $name = 'id'): Property
    {
        return $this->string($name)
            ->required()
            ->description('Unique identifier');
    }

    /**
     * Create a foreign key property.
     */
    public function foreignKey(string $name, string $references): Property
    {
        return $this->string($name)
            ->required()
            ->description("Foreign key reference to $references");
    }

    /**
     * Create a timestamp property.
     */
    public function timestamp(string $name): Property
    {
        return $this->string($name)
            ->format('date-time')
            ->description('Timestamp field');
    }

    /**
     * Create a slug property.
     */
    public function slug(string $name = 'slug'): Property
    {
        return $this->string($name)
            ->pattern('^[a-z0-9]+(?:-[a-z0-9]+)*$')
            ->description('URL-friendly slug');
    }

    /**
     * Create a JSON property.
     */
    public function json(string $name): Property
    {
        $property = new Property($name, 'object', 'JSON data field');
        $property->addAttribute('format', 'json');
        return $property;
    }

    /**
     * Create a transfer property.
     */
    public function transfer(string $name, ?string $label = null): Property
    {
        $property = new Property($name, 'object', $label);
        $property->addAttribute('properties', [
            'source' => ['type' => 'string'],
            'destination' => ['type' => 'string'],
            'amount' => ['type' => 'number'],
            'currency' => ['type' => 'string'],
            'status' => ['type' => 'string', 'enum' => ['pending', 'completed', 'failed']],
            'timestamp' => ['type' => 'string', 'format' => 'date-time']
        ]);
        return $property;
    }

    /**
     * Create a table property.
     */
    public function table(string $name, ?string $label = null): Property
    {
        $property = new Property($name, 'object', $label);
        $property->addAttribute('properties', [
            'columns' => ['type' => 'array'],
            'data' => ['type' => 'array'],
            'pagination' => ['type' => 'object']
        ]);
        return $property;
    }

    /**
     * Create a dynamic form property.
     */
    public function dynamicForm(string $name, ?string $label = null): Property
    {
        $property = new Property($name, 'object', $label);
        $property->addAttribute('properties', [
            'fields' => ['type' => 'array'],
            'data' => ['type' => 'object']
        ]);
        return $property;
    }

    /**
     * Create a matrix property.
     */
    public function matrix(string $name, ?string $label = null): Property
    {
        $property = new Property($name, 'object', $label);
        $property->addAttribute('properties', [
            'rows' => ['type' => 'array'],
            'columns' => ['type' => 'array'],
            'cells' => ['type' => 'array']
        ]);
        return $property;
    }
}
