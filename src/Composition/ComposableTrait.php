<?php

namespace Skillcraft\UiSchemaCraft\Composition;

use Skillcraft\UiSchemaCraft\Abstracts\UIComponentSchema;

trait ComposableTrait
{
    /**
     * Child components organized by slots
     *
     * @var array<string, array<UIComponentSchema>>
     */
    protected array $children = [
        'default' => []
    ];

    public function addChild(UIComponentSchema $component, ?string $slot = null): self
    {
        $slot = $slot ?? 'default';
        
        if (!isset($this->children[$slot])) {
            $this->children[$slot] = [];
        }

        $this->children[$slot][] = $component;
        return $this;
    }

    public function getChildren(?string $slot = null): array
    {
        if ($slot === null) {
            return array_merge(...array_values($this->children));
        }

        return $this->children[$slot] ?? [];
    }

    public function removeChild(UIComponentSchema $component): self
    {
        foreach ($this->children as $slot => $components) {
            $this->children[$slot] = array_filter(
                $components,
                fn($child) => $child !== $component
            );
        }

        return $this;
    }

    public function hasChildren(?string $slot = null): bool
    {
        if ($slot === null) {
            return !empty(array_merge(...array_values($this->children)));
        }

        return !empty($this->children[$slot] ?? []);
    }

    /**
     * Include children in component schema
     *
     * @return array
     */
    protected function getChildrenSchema(): array
    {
        $schema = [];
        foreach ($this->children as $slot => $components) {
            if (!empty($components)) {
                $schema[$slot] = array_map(
                    fn($component) => $component->toArray(),
                    $components
                );
            }
        }
        return $schema;
    }
}
