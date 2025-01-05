<?php

namespace Skillcraft\UiSchemaCraft\State;

use Skillcraft\UiSchemaCraft\Abstracts\UIComponentSchema;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Support\Str;

class StateManager implements StateManagerInterface
{
    private const STATE_PREFIX = 'ui_schema_state:';
    private const STATE_INDEX_PREFIX = 'ui_schema_index:';

    public function __construct(
        private readonly Cache $cache,
        private readonly int $ttl = 3600 // 1 hour default TTL
    ) {}

    public function save(string $id, UIComponentSchema $component, array $state): void
    {
        $stateData = [
            'component_type' => $component->getIdentifier(),
            'state' => $state,
            'schema_version' => $component->getVersion(),
            'updated_at' => now()->timestamp,
        ];

        // Save the state
        $this->cache->put(
            $this->getStateKey($id),
            $stateData,
            $this->ttl
        );

        // Update the component index
        $this->updateComponentIndex($component->getIdentifier(), $id);
    }

    public function load(string $id): ?array
    {
        return $this->cache->get($this->getStateKey($id));
    }

    public function delete(string $id): void
    {
        $state = $this->load($id);
        if ($state) {
            // Remove from component index
            $this->removeFromComponentIndex($state['component_type'], $id);
            // Delete the state
            $this->cache->forget($this->getStateKey($id));
        }
    }

    public function getStatesForComponent(string $componentType): array
    {
        $indexKey = $this->getComponentIndexKey($componentType);
        $stateIds = $this->cache->get($indexKey, []);
        
        $states = [];
        foreach ($stateIds as $id) {
            $state = $this->load($id);
            if ($state) {
                $states[$id] = $state;
            }
        }

        return $states;
    }

    private function updateComponentIndex(string $componentType, string $stateId): void
    {
        $indexKey = $this->getComponentIndexKey($componentType);
        $index = $this->cache->get($indexKey, []);
        
        if (!in_array($stateId, $index)) {
            $index[] = $stateId;
            $this->cache->put($indexKey, $index, $this->ttl);
        }
    }

    private function removeFromComponentIndex(string $componentType, string $stateId): void
    {
        $indexKey = $this->getComponentIndexKey($componentType);
        $index = $this->cache->get($indexKey, []);
        
        $index = array_filter($index, fn($id) => $id !== $stateId);
        $this->cache->put($indexKey, $index, $this->ttl);
    }

    private function getStateKey(string $id): string
    {
        return self::STATE_PREFIX . $id;
    }

    private function getComponentIndexKey(string $componentType): string
    {
        return self::STATE_INDEX_PREFIX . $componentType;
    }
}
