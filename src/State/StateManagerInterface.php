<?php

namespace Skillcraft\UiSchemaCraft\State;

use Skillcraft\UiSchemaCraft\Abstracts\UIComponentSchema;

interface StateManagerInterface
{
    /**
     * Save component state
     *
     * @param string $id Unique identifier for the component state
     * @param UIComponentSchema $component Component instance
     * @param array $state State data to save
     * @return void
     */
    public function save(string $id, UIComponentSchema $component, array $state): void;

    /**
     * Load component state
     *
     * @param string $id Unique identifier for the component state
     * @return array|null State data or null if not found
     */
    public function load(string $id): ?array;

    /**
     * Delete component state
     *
     * @param string $id Unique identifier for the component state
     * @return void
     */
    public function delete(string $id): void;

    /**
     * Get all states for a component type
     *
     * @param string $componentType
     * @return array
     */
    public function getStatesForComponent(string $componentType): array;
}
