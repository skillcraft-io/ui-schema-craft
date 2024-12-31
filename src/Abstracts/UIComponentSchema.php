<?php

namespace Skillcraft\UiSchemaCraft\Abstracts;

abstract class UIComponentSchema
{
    protected string $type = 'component';

    protected string $component;

    protected array $props = [];

    protected array $example = [];

    abstract protected function properties(): array;

    public function __construct()
    {
        $this->buildSchema();
    }

    protected function buildSchema(): void
    {
        $properties = $this->properties();
        $schema = [];

        foreach ($properties as $property) {
            $schema[$property->getName()] = $property->toArray();
        }

        $this->props = [
            'config' => [
                'type' => 'object',
                'properties' => $schema,
            ],
        ];
    }

    public function getIdentifier(): string
    {
        return $this->component;
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'component' => $this->component,
            'props' => $this->props,
            'example' => (! app()->isProduction()) ? $this->getExampleData() : null,
            'data' => $this->getLiveData(),
        ];
    }

    public function getExampleData(): array
    {
        return $this->example;
    }

    abstract public function getLiveData(): array;
}
