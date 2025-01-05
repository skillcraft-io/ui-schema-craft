<?php

namespace Skillcraft\UiSchemaCraft\Schema\Traits;

use Skillcraft\UiSchemaCraft\Schema\Property;

trait EditorTrait
{
    /**
     * Create a code editor property.
     */
    public function codeEditor(string $name, ?string $description = null): Property
    {
        $property = new Property($name, 'object', $description);
        $property->addAttribute('properties', [
            'language' => ['type' => 'string', 'default' => 'plaintext'],
            'theme' => ['type' => 'string', 'default' => 'vs'],
            'value' => ['type' => 'string'],
            'readOnly' => ['type' => 'boolean', 'default' => false],
            'minimap' => ['type' => 'boolean', 'default' => true],
            'lineNumbers' => ['type' => 'boolean', 'default' => true],
            'wordWrap' => ['type' => 'boolean', 'default' => false]
        ]);
        return $property;
    }

    /**
     * Create a markdown editor property.
     */
    public function markdown(string $name, ?string $label = null): Property
    {
        $property = new Property($name, 'object', $label);
        $property->addAttribute('properties', [
            'value' => ['type' => 'string'],
            'preview' => ['type' => 'boolean', 'default' => true],
            'toolbar' => [
                'type' => 'object',
                'properties' => [
                    'bold' => ['type' => 'boolean', 'default' => true],
                    'italic' => ['type' => 'boolean', 'default' => true],
                    'heading' => ['type' => 'boolean', 'default' => true],
                    'code' => ['type' => 'boolean', 'default' => true],
                    'quote' => ['type' => 'boolean', 'default' => true],
                    'link' => ['type' => 'boolean', 'default' => true],
                    'image' => ['type' => 'boolean', 'default' => true],
                    'list' => ['type' => 'boolean', 'default' => true]
                ]
            ]
        ]);
        return $property;
    }

    /**
     * Create a JSON editor property.
     */
    public function jsonEditor(string $name, ?string $label = null): Property
    {
        $property = new Property($name, 'object', $label);
        $property->addAttribute('properties', [
            'value' => ['type' => 'object'],
            'mode' => ['type' => 'string', 'enum' => ['tree', 'code', 'form', 'text'], 'default' => 'tree'],
            'schema' => ['type' => 'object'],
            'readOnly' => ['type' => 'boolean', 'default' => false],
            'indentation' => ['type' => 'number', 'default' => 2]
        ]);
        return $property;
    }

    /**
     * Create a masked input property.
     */
    public function mask(string $name, ?string $label = null): Property
    {
        $property = new Property($name, 'string', $label);
        $property->addAttribute('format', 'mask');
        return $property;
    }
}
