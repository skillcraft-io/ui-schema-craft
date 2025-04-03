<?php

namespace Skillcraft\UiSchemaCraft\Tests\Fixtures\Components;

use Skillcraft\UiSchemaCraft\Abstracts\UIComponentSchema;

class InputSchema extends UIComponentSchema
{
    protected string $type = 'input';
    protected string $version = '1.0.0';

    public function properties(): array
    {
        return [
            'value' => [
                'type' => 'string',
                'required' => true
            ],
            'placeholder' => [
                'type' => 'string',
                'required' => false
            ]
        ];
    }

    protected function getValidationSchema(): ?array
    {
        return [
            'value' => ['type' => 'string', 'required' => true],
            'placeholder' => ['type' => 'string', 'required' => false]
        ];
    }
}
