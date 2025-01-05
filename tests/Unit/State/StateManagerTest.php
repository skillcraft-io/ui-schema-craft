<?php

namespace Skillcraft\UiSchemaCraft\Tests\Unit\State;

use Mockery;
use Mockery\MockInterface;
use Skillcraft\UiSchemaCraft\Tests\TestCase;
use Skillcraft\UiSchemaCraft\State\StateManager;
use Illuminate\Contracts\Cache\Repository as Cache;
use Skillcraft\UiSchemaCraft\Abstracts\UIComponentSchema;
use Skillcraft\UiSchemaCraft\State\StateManagerInterface;

class StateManagerTest extends TestCase
{
    private StateManager $stateManager;
    private MockInterface $cache;
    private MockInterface $component;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->cache = Mockery::mock(Cache::class);
        $this->component = Mockery::mock(UIComponentSchema::class);
        $this->stateManager = new StateManager($this->cache);
    }

    public function test_implements_state_manager_interface(): void
    {
        $this->assertInstanceOf(StateManagerInterface::class, $this->stateManager);
    }

    /**
     * Test that the save method throws a TypeError when the state is not an array.
     */
    public function test_interface_type_constraints(): void
    {
        $this->expectException(\TypeError::class);
        
        // This should throw a TypeError since state must be an array
        $this->stateManager->save('test-id', $this->component, 'not-an-array');
    }

    public function test_save_stores_state_and_updates_index(): void
    {
        $id = 'test-id';
        $componentType = 'test-component';
        $state = ['key' => 'value'];
        $version = '1.0';

        $this->component->expects('getIdentifier')->twice()->andReturn($componentType);
        $this->component->expects('getVersion')->once()->andReturn($version);

        $this->cache->expects('get')
            ->once()
            ->with("ui_schema_index:{$componentType}", [])
            ->andReturn([]);

        $this->cache->expects('put')
            ->once()
            ->withArgs(function ($key, $value, $ttl) use ($id, $componentType, $state, $version) {
                return $key === "ui_schema_state:{$id}" &&
                    $value['component_type'] === $componentType &&
                    $value['state'] === $state &&
                    $value['schema_version'] === $version &&
                    isset($value['updated_at']) &&
                    $ttl === 3600;
            });

        $this->cache->expects('put')
            ->once()
            ->with("ui_schema_index:{$componentType}", [$id], 3600);

        $this->stateManager->save($id, $this->component, $state);
        $this->assertTrue(true, 'State was saved successfully');
    }

    public function test_load_returns_state(): void
    {
        $id = 'test-id';
        $stateData = ['some' => 'data'];

        $this->cache->expects('get')
            ->once()
            ->with("ui_schema_state:{$id}")
            ->andReturn($stateData);

        $result = $this->stateManager->load($id);
        $this->assertEquals($stateData, $result);
    }

    public function test_load_returns_null_when_state_not_found(): void
    {
        $id = 'non-existent-id';

        $this->cache->expects('get')
            ->once()
            ->with("ui_schema_state:{$id}")
            ->andReturn(null);

        $result = $this->stateManager->load($id);
        $this->assertNull($result);
    }

    public function test_delete_removes_state_and_updates_index(): void
    {
        $id = 'test-id';
        $componentType = 'test-component';
        $stateData = [
            'component_type' => $componentType,
            'state' => ['key' => 'value']
        ];

        $this->cache->expects('get')
            ->once()
            ->with("ui_schema_state:{$id}")
            ->andReturn($stateData);

        $this->cache->expects('get')
            ->once()
            ->with("ui_schema_index:{$componentType}", [])
            ->andReturn([$id]);

        $this->cache->expects('put')
            ->once()
            ->with("ui_schema_index:{$componentType}", [], 3600);

        $this->cache->expects('forget')
            ->once()
            ->with("ui_schema_state:{$id}");

        $this->stateManager->delete($id);
        $this->assertTrue(true, 'State was deleted successfully');
    }

    public function test_delete_does_nothing_when_state_not_found(): void
    {
        $id = 'non-existent-id';

        $this->cache->expects('get')
            ->once()
            ->with("ui_schema_state:{$id}")
            ->andReturn(null);

        $this->stateManager->delete($id);
        $this->assertTrue(true, 'Delete operation completed without errors');
    }

    public function test_get_states_for_component_returns_all_states(): void
    {
        $componentType = 'test-component';
        $id1 = 'id1';
        $id2 = 'id2';
        $state1 = ['state' => 1];
        $state2 = ['state' => 2];

        $this->cache->expects('get')
            ->once()
            ->with("ui_schema_index:{$componentType}", [])
            ->andReturn([$id1, $id2]);

        $this->cache->expects('get')
            ->once()
            ->with("ui_schema_state:{$id1}")
            ->andReturn($state1);

        $this->cache->expects('get')
            ->once()
            ->with("ui_schema_state:{$id2}")
            ->andReturn($state2);

        $result = $this->stateManager->getStatesForComponent($componentType);
        $this->assertEquals([
            $id1 => $state1,
            $id2 => $state2
        ], $result);
    }

    public function test_get_states_for_component_handles_missing_states(): void
    {
        $componentType = 'test-component';
        $id1 = 'id1';
        $id2 = 'id2';
        $state1 = ['state' => 1];

        $this->cache->expects('get')
            ->once()
            ->with("ui_schema_index:{$componentType}", [])
            ->andReturn([$id1, $id2]);

        $this->cache->expects('get')
            ->once()
            ->with("ui_schema_state:{$id1}")
            ->andReturn($state1);

        $this->cache->expects('get')
            ->once()
            ->with("ui_schema_state:{$id2}")
            ->andReturn(null);

        $result = $this->stateManager->getStatesForComponent($componentType);
        $this->assertEquals([
            $id1 => $state1
        ], $result);
    }

    public function test_save_updates_existing_index(): void
    {
        $id = 'test-id';
        $existingId = 'existing-id';
        $componentType = 'test-component';
        $state = ['key' => 'value'];
        $version = '1.0';

        $this->component->expects('getIdentifier')->twice()->andReturn($componentType);
        $this->component->expects('getVersion')->once()->andReturn($version);

        $this->cache->expects('get')
            ->once()
            ->with("ui_schema_index:{$componentType}", [])
            ->andReturn([$existingId]);

        $this->cache->expects('put')
            ->once()
            ->withArgs(function ($key, $value, $ttl) use ($id, $componentType, $state, $version) {
                return $key === "ui_schema_state:{$id}" &&
                    $value['component_type'] === $componentType &&
                    $value['state'] === $state &&
                    $value['schema_version'] === $version &&
                    isset($value['updated_at']) &&
                    $ttl === 3600;
            });

        $this->cache->expects('put')
            ->once()
            ->with("ui_schema_index:{$componentType}", [$existingId, $id], 3600);

        $this->stateManager->save($id, $this->component, $state);
        $this->assertTrue(true, 'State was saved and index was updated successfully');
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
