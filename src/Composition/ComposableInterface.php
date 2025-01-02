<?php

namespace Skillcraft\UiSchemaCraft\Composition;

use Skillcraft\UiSchemaCraft\Abstracts\UIComponentSchema;

interface ComposableInterface
{
    /**
     * Add a child component
     *
     * @param UIComponentSchema $component
     * @param string|null $slot Optional slot name
     * @return self
     */
    public function addChild(UIComponentSchema $component, ?string $slot = null): self;

    /**
     * Get child components
     *
     * @param string|null $slot Optional slot name to filter by
     * @return array<UIComponentSchema>
     */
    public function getChildren(?string $slot = null): array;

    /**
     * Remove a child component
     *
     * @param UIComponentSchema $component
     * @return self
     */
    public function removeChild(UIComponentSchema $component): self;

    /**
     * Check if component has children
     *
     * @param string|null $slot Optional slot name to check
     * @return bool
     */
    public function hasChildren(?string $slot = null): bool;
}
