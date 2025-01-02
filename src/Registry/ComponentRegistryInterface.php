<?php

namespace Skillcraft\UiSchemaCraft\Registry;

use Skillcraft\UiSchemaCraft\Abstracts\UIComponentSchema;

interface ComponentRegistryInterface
{
    /**
     * Register a component with the registry
     *
     * @param string $name
     * @param UIComponentSchema $component
     * @return void
     * @throws ComponentAlreadyRegisteredException
     */
    public function register(string $name, UIComponentSchema $component): void;

    /**
     * Get a component by name
     *
     * @param string $name
     * @return UIComponentSchema|null
     */
    public function get(string $name): ?UIComponentSchema;

    /**
     * Check if a component exists
     *
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool;

    /**
     * Get all registered components
     *
     * @return array<string, UIComponentSchema>
     */
    public function all(): array;
}
