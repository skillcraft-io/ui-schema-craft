<?php

namespace Skillcraft\UiSchemaCraft\HookFlow;

use Skillcraft\HookFlow\HookDefinition;
use Skillcraft\UiSchemaCraft\Services\UiSchemaCraftService;

class AddUiComponentHook extends HookDefinition
{
    public function isFilter(): bool
    {
        return true;
    }

    public function getDescription(): string
    {
        return 'Register Schema for UI Components to the system via hook';
    }

    public function getPlugin(): string
    {
        return 'ui-schema-craft';
    }

    public function getParameters(): array
    {
        return [
            'schema' => 'string',
        ];
    }

    public function getTriggerPoint(): string
    {
        return 'UiSchemaCraftServiceProvider@boot';
    }

    public function apply($value, array $args): UiSchemaCraftService
    {
        $newSchemas = collect($this->getRegisteredSchemas())
            ->push(data_get($args, 'schema', []))
            ->toArray();

        config(['ui-schema-craft.schemas' => $newSchemas]);

        return $this->addSchemas();
    }

    protected function addSchemas(): UiSchemaCraftService
    {
        $schema = app(UiSchemaCraftService::class);

        $schema->addSchemas($this->getRegisteredSchemas());

        return $schema;
    }

    protected function getRegisteredSchemas(): array
    {
        return config('ui-schema-craft.schemas', []);
    }
}
