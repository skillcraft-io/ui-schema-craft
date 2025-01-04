<?php

namespace Skillcraft\UiSchemaCraft\Composition;

use Skillcraft\UiSchemaCraft\Abstracts\UIComponentSchema;

trait ComposableTrait
{
    /**
     * Child components organized by identifiers
     *
     * @var array<string, UIComponentSchema>
     */
    protected array $children = [];

    public function addChild(string $identifier, UIComponentSchema $component): self
    {
        $this->children[$identifier] = $component;
        return $this;
    }

    public function getChildren(): array
    {
        return array_values($this->children);
    }

    public function removeChild(string $identifier): self
    {
        unset($this->children[$identifier]);
        return $this;
    }

    public function hasChildren(): bool
    {
        return !empty($this->children);
    }

    /**
     * Include children in component schema
     *
     * @return array
     */
    protected function getChildrenSchema(): array
    {
        $schema = [];
        foreach ($this->children as $identifier => $component) {
            $schema[$identifier] = $component->toArray();
        }
        return $schema;
    }
}
