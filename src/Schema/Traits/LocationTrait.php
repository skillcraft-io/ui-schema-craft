<?php

namespace Skillcraft\UiSchemaCraft\Schema\Traits;

use Skillcraft\UiSchemaCraft\Schema\Property;

trait LocationTrait
{
    /**
     * Create a coordinates property.
     */
    public function coordinates(string $name, ?string $label = null): Property
    {
        return $this->object($name)
            ->description($label ?? ucwords(str_replace('_', ' ', $name)))
            ->withBuilder(function ($builder) {
                $builder->object('value')
                    ->properties([
                        'latitude' => [
                            'type' => 'number',
                            'minimum' => -90,
                            'maximum' => 90
                        ],
                        'longitude' => [
                            'type' => 'number',
                            'minimum' => -180,
                            'maximum' => 180
                        ],
                        'altitude' => [
                            'type' => 'number',
                            'nullable' => true
                        ]
                    ]);
            });
    }

    /**
     * Create a map property.
     */
    public function map(string $name, ?string $label = null): Property
    {
        return $this->object($name)
            ->description($label ?? ucwords(str_replace('_', ' ', $name)))
            ->withBuilder(function ($builder) {
                $builder->object('value')
                    ->properties([
                        'center' => [
                            'type' => 'object',
                            'properties' => [
                                'latitude' => ['type' => 'number'],
                                'longitude' => ['type' => 'number']
                            ]
                        ],
                        'zoom' => [
                            'type' => 'number',
                            'minimum' => 0,
                            'maximum' => 20,
                            'default' => 13
                        ],
                        'markers' => [
                            'type' => 'array',
                            'items' => [
                                'type' => 'object',
                                'properties' => [
                                    'latitude' => ['type' => 'number'],
                                    'longitude' => ['type' => 'number'],
                                    'title' => ['type' => 'string'],
                                    'description' => ['type' => 'string']
                                ]
                            ]
                        ]
                    ]);
            });
    }

    /**
     * Create a cascader property.
     */
    public function cascader(string $name, ?string $label = null): Property
    {
        return $this->object($name)
            ->description($label ?? ucwords(str_replace('_', ' ', $name)))
            ->withBuilder(function ($builder) {
                $builder->array('value')
                    ->items([
                        'type' => 'object',
                        'properties' => [
                            'value' => ['type' => 'string'],
                            'label' => ['type' => 'string'],
                            'children' => ['type' => 'array']
                        ]
                    ]);

                $builder->boolean('multiple')
                    ->addAttribute('default', false)
                    ->description('Allow multiple selections');

                $builder->boolean('clearable')
                    ->addAttribute('default', true)
                    ->description('Allow clearing selection');
            });
    }
}
