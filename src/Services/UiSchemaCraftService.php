<?php

namespace Skillcraft\UiSchemaCraft\Services;

use Illuminate\Support\Collection;

class UiSchemaCraftService
{
    protected Collection $schemas;
    protected string $config = 'ui-schema-craft.schemas';

    public function __construct()
    {
        $this->schemas = collect(config($this->config));
    }

    public function addSchema(string $schemaClass): void
    {
        $schema = new $schemaClass;
        $this->schemas->put($schema->getIdentifier(), $schemaClass);
    }

    public function addSchemas(array $schemaClasses): void
    {
        foreach ($schemaClasses as $schemaClass) {
            $this->addSchema($schemaClass);
        }
    }

    public function registerSchema(string $name, string $schemaClass): void
    {
        $this->schemas->put($name, $schemaClass);
    }

    public function getSchema(string $name): ?array
    {
        if (! $this->schemas->has($name)) {
            return null;
        }

        $schemaClass = $this->schemas->get($name);
        $schema = new $schemaClass;

        return $schema->toArray();
    }

    public function getExampleData(string $name): ?array
    {
        if (! $this->schemas->has($name)) {
            return null;
        }

        $schemaClass = $this->schemas->get($name);
        $schema = new $schemaClass;

        return $schema->getExampleData();
    }

    public function getAllSchemas(): array
    {
        return $this->schemas->map(function ($schemaClass) {
            $schema = new $schemaClass;

            return $schema->toArray();
        })->toArray();
    }

    public function getAllExampleData(): array
    {
        return $this->schemas->map(function ($schemaClass) {
            $schema = new $schemaClass;

            return $schema->getExampleData();
        })->toArray();
    }
}
