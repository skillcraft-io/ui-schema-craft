<?php

namespace Skillcraft\UiSchemaCraft\Tests\Doubles;

use Skillcraft\UiSchemaCraft\Abstracts\UIComponentSchema;

class TestUIComponentSchema extends UIComponentSchema
{
    public function getType(): string
    {
        return 'form-field';
    }

    public function getComponent(): string
    {
        return 'form-field';
    }

    public function getVersion(): string
    {
        return '1.0.0';
    }

    public function properties(): array
    {
        return ['label' => 'string'];
    }

    public function toArray(): array
    {
        return [
            'type' => $this->getType(),
            'component' => $this->getComponent(),
            'version' => $this->getVersion(),
            'label' => 'Test Field'
        ];
    }

    public function validate(array $data): array
    {
        return [
            'valid' => true,
            'errors' => null
        ];
    }
}
