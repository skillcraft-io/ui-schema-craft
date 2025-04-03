<?php

namespace Skillcraft\UiSchemaCraft\Tests\Fixtures\Components;

use Skillcraft\UiSchemaCraft\Abstracts\UIComponentSchema;

class SelectSchema extends UIComponentSchema
{
    protected string $type = 'select';
    protected string $version = '1.0.0';

    public function properties(): array
    {
        return [
            'value' => [
                'type' => 'string',
                'required' => true
            ],
            'options' => [
                'type' => 'array',
                'items' => [
                    'type' => 'object',
                    'properties' => [
                        'label' => ['type' => 'string'],
                        'value' => ['type' => 'string']
                    ]
                ],
                'required' => true
            ]
        ];
    }

    protected function getValidationSchema(): ?array
    {
        return [
            'value' => ['type' => 'string', 'required' => true],
            'options' => [
                'type' => 'array',
                'items' => [
                    'type' => 'object',
                    'properties' => [
                        'label' => ['type' => 'string'],
                        'value' => ['type' => 'string']
                    ]
                ],
                'required' => true
            ]
        ];
    }
}
