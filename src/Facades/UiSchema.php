<?php

namespace Skillcraft\UiSchemaCraft\Facades;

use Illuminate\Support\Facades\Facade;
use Skillcraft\UiSchemaCraft\Services\UiSchemaCraftService;

/**
 * @method static array getComponent(string $type, ?string $stateId = null)
 * @method static array saveState(string $type, array $state, ?string $stateId = null)
 * @method static void deleteState(string $stateId)
 * @method static array getComponentStates(string $type)
 * @method static mixed createComponent(string $type, array $config = [])
 * @method static mixed createFromSchema(array $schema)
 * @method static bool hasComponent(string $type)
 * @method static array getComponents()
 * @method static void registerComponent(string $componentClass)
 * @method static void registerComponents(array $componentClasses)
 * @method static array resolveComponent(string $type, array $data = [])
 *
 * @see \Skillcraft\UiSchemaCraft\Services\UiSchemaCraftService
 */
class UiSchema extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'ui-schema';
    }
}
