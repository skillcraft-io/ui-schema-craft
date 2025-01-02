<?php

namespace Skillcraft\UiSchemaCraft\Services;

use Skillcraft\UiSchemaCraft\Registry\ComponentRegistryInterface;
use Skillcraft\UiSchemaCraft\Factory\ComponentFactoryInterface;
use Skillcraft\UiSchemaCraft\State\StateManagerInterface;
use Skillcraft\UiSchemaCraft\Validation\ValidationResult;
use Illuminate\Support\Str;
use Skillcraft\UiSchemaCraft\Exceptions\ComponentTypeNotFoundException;

class UiSchemaCraftService
{
    public function __construct(
        private readonly ComponentRegistryInterface $registry,
        private readonly ComponentFactoryInterface $factory,
        private readonly StateManagerInterface $stateManager
    ) {}

    /**
     * Get component schema with optional state
     *
     * @param string $type Component type
     * @param string|null $stateId Optional state ID
     * @return array{schema: array, state: ?array}
     * @throws ComponentTypeNotFoundException
     */
    public function getComponent(string $type, ?string $stateId = null): array
    {
        $component = $this->createComponent($type);
        $schema = $component->toArray();
        
        $state = null;
        if ($stateId) {
            $state = $this->stateManager->load($stateId);
        }

        return [
            'schema' => $schema,
            'state' => $state,
        ];
    }

    /**
     * Save component state
     *
     * @param string $type Component type
     * @param array $state State data
     * @param string|null $stateId Optional state ID
     * @return array{stateId: string, state: array, validation: ?array}
     * @throws ComponentTypeNotFoundException
     */
    public function saveState(string $type, array $state, ?string $stateId = null): array
    {
        $component = $this->createComponent($type);
        
        // Validate state
        $validationResult = $component->validate($state);
        if ($validationResult->hasErrors()) {
            return [
                'stateId' => $stateId,
                'state' => $state,
                'validation' => $validationResult->toArray(),
            ];
        }

        // Generate ID if not provided
        $stateId = $stateId ?? (string) Str::uuid();
        
        $this->stateManager->save($stateId, $component, $state);

        return [
            'stateId' => $stateId,
            'state' => $state,
            'validation' => null,
        ];
    }

    /**
     * Delete component state
     *
     * @param string $stateId
     * @return void
     */
    public function deleteState(string $stateId): void
    {
        $this->stateManager->delete($stateId);
    }

    /**
     * Get all states for a component type
     *
     * @param string $type Component type
     * @return array
     * @throws ComponentTypeNotFoundException
     */
    public function getComponentStates(string $type): array
    {
        if (!$this->hasComponent($type)) {
            throw new ComponentTypeNotFoundException($type);
        }

        return $this->stateManager->getStatesForComponent($type);
    }

    /**
     * Create a new component instance
     *
     * @param string $type
     * @param array $config
     * @return mixed
     */
    public function createComponent(string $type, array $config = []): mixed
    {
        return $this->factory->create($type, $config);
    }

    /**
     * Create a component from a schema array
     *
     * @param array $schema
     * @return mixed
     */
    public function createFromSchema(array $schema): mixed
    {
        return $this->factory->createFromSchema($schema);
    }

    /**
     * Validate component data
     *
     * @param string $type
     * @param array $data
     * @return ValidationResult
     */
    public function validate(string $type, array $data): ValidationResult
    {
        $component = $this->registry->get($type);
        return $component->validate($data);
    }

    /**
     * Get all registered components
     *
     * @return array
     */
    public function getComponents(): array
    {
        return $this->registry->all();
    }

    /**
     * Check if a component type exists
     *
     * @param string $type
     * @return bool
     */
    public function hasComponent(string $type): bool
    {
        return $this->registry->has($type);
    }
}
