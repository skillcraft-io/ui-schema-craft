<?php

namespace Skillcraft\UiSchemaCraft\Schema\Traits;

use Skillcraft\UiSchemaCraft\Schema\Property;

trait SelectionTrait
{
    /**
     * Create a multi-select property.
     */
    public function multiSelect(string $name, ?string $label = null): Property
    {
        return (new Property($name, 'array', $label))
            ->description($label ?? ucwords(str_replace('_', ' ', $name)))
            ->items(['type' => 'string'])
            ->addAttribute('properties', [
                'options' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'value' => ['type' => 'string'],
                            'label' => ['type' => 'string'],
                            'disabled' => ['type' => 'boolean']
                        ]
                    ],
                    'description' => 'Available options'
                ],
                'searchable' => [
                    'type' => 'boolean',
                    'default' => true,
                    'description' => 'Enable search functionality'
                ],
                'clearable' => [
                    'type' => 'boolean',
                    'default' => true,
                    'description' => 'Allow clearing selection'
                ],
                'maxItems' => [
                    'type' => 'number',
                    'description' => 'Maximum number of items that can be selected'
                ]
            ]);
    }

    /**
     * Create a tree select property.
     */
    public function treeSelect(string $name, ?string $label = null): Property
    {
        return (new Property($name, 'object', $label))
            ->description($label ?? ucwords(str_replace('_', ' ', $name)))
            ->addAttribute('properties', [
                'value' => [
                    'type' => 'array',
                    'description' => 'Selected values'
                ],
                'options' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'value' => ['type' => 'string'],
                            'label' => ['type' => 'string'],
                            'children' => ['type' => 'array'],
                            'disabled' => ['type' => 'boolean']
                        ]
                    ],
                    'description' => 'Tree structure options'
                ],
                'multiple' => [
                    'type' => 'boolean',
                    'default' => false,
                    'description' => 'Allow multiple selections'
                ],
                'checkable' => [
                    'type' => 'boolean',
                    'default' => false,
                    'description' => 'Show checkboxes'
                ],
                'expandAll' => [
                    'type' => 'boolean',
                    'default' => false,
                    'description' => 'Expand all nodes by default'
                ]
            ]);
    }

    /**
     * Create a combobox property.
     */
    public function combobox(string $name, ?string $label = null): Property
    {
        return (new Property($name, 'object', $label))
            ->description($label ?? ucwords(str_replace('_', ' ', $name)))
            ->addAttribute('properties', [
                'value' => [
                    'type' => 'string',
                    'description' => 'Selected value'
                ],
                'options' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'value' => ['type' => 'string'],
                            'label' => ['type' => 'string'],
                            'group' => ['type' => 'string']
                        ]
                    ],
                    'description' => 'Available options'
                ],
                'allowCustom' => [
                    'type' => 'boolean',
                    'default' => false,
                    'description' => 'Allow custom values'
                ],
                'searchable' => [
                    'type' => 'boolean',
                    'default' => true,
                    'description' => 'Enable search functionality'
                ],
                'clearable' => [
                    'type' => 'boolean',
                    'default' => true,
                    'description' => 'Allow clearing selection'
                ]
            ]);
    }

    /**
     * Create an autocomplete property.
     */
    public function autocomplete(string $name, ?string $label = null): Property
    {
        return (new Property($name, 'object', $label))
            ->description($label ?? ucwords(str_replace('_', ' ', $name)))
            ->addAttribute('properties', [
                'value' => [
                    'type' => 'string',
                    'description' => 'Selected value'
                ],
                'suggestions' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'value' => ['type' => 'string'],
                            'label' => ['type' => 'string'],
                            'description' => ['type' => 'string']
                        ]
                    ],
                    'description' => 'Suggestion items'
                ],
                'minChars' => [
                    'type' => 'number',
                    'default' => 1,
                    'description' => 'Minimum characters before showing suggestions'
                ],
                'debounce' => [
                    'type' => 'number',
                    'default' => 300,
                    'description' => 'Delay before searching'
                ],
                'highlightMatch' => [
                    'type' => 'boolean',
                    'default' => true,
                    'description' => 'Highlight matching text'
                ]
            ]);
    }
}
