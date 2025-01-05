<?php

namespace Skillcraft\UiSchemaCraft\Factory;

use Skillcraft\UiSchemaCraft\Abstracts\UIComponentSchema;

interface ComponentFactoryInterface
{
    /**
     * Create a new component instance
     *
     * @param string $type
     * @param array $config
     * @return UIComponentSchema
     * @throws ComponentTypeNotFoundException
     */
    public function create(string $type, array $config = []): UIComponentSchema;

    /**
     * Create a component from a schema array
     *
     * @param array $schema
     * @return UIComponentSchema
     * @throws ComponentTypeNotFoundException
     */
    public function createFromSchema(array $schema): UIComponentSchema;
}
