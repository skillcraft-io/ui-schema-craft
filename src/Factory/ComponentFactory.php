<?php

namespace Skillcraft\UiSchemaCraft\Factory;

use Skillcraft\UiSchemaCraft\Abstracts\UIComponentSchema;
use Skillcraft\UiSchemaCraft\Registry\ComponentRegistryInterface;
use Skillcraft\UiSchemaCraft\Validation\CompositeValidator;
use Skillcraft\UiSchemaCraft\Exceptions\ComponentTypeNotFoundException;

class ComponentFactory implements ComponentFactoryInterface
{
    public function __construct(
        private readonly ComponentRegistryInterface $registry,
        private readonly CompositeValidator $validator
    ) {}

    public function create(string $type, array $config = []): UIComponentSchema
    {
        if (!$this->registry->has($type)) {
            throw new ComponentTypeNotFoundException($type);
        }

        $baseComponent = $this->registry->get($type);
        return $this->configure(clone $baseComponent, $config);
    }

    public function createFromSchema(array $schema): UIComponentSchema
    {
        if (!isset($schema['type'])) {
            throw new ComponentTypeNotFoundException('Schema must specify a component type');
        }

        return $this->create($schema['type'], $schema['config'] ?? []);
    }

    private function configure(UIComponentSchema $component, array $config): UIComponentSchema
    {
        foreach ($config as $name => $value) {
            $component->setPropertyValue($name, $value);
        }

        return $component;
    }
}
