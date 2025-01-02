<?php

namespace Skillcraft\UiSchemaCraft\Registry;

use Skillcraft\UiSchemaCraft\Abstracts\UIComponentSchema;
use Skillcraft\UiSchemaCraft\Events\ComponentRegisteredEvent;
use Skillcraft\UiSchemaCraft\Exceptions\ComponentAlreadyRegisteredException;
use Skillcraft\UiSchemaCraft\Validation\CompositeValidator;
use Illuminate\Contracts\Events\Dispatcher;

class ComponentRegistry implements ComponentRegistryInterface
{
    private array $components = [];
    private CompositeValidator $validator;
    private Dispatcher $events;

    public function __construct(CompositeValidator $validator, Dispatcher $events)
    {
        $this->validator = $validator;
        $this->events = $events;
    }

    public function register(string $name, UIComponentSchema $component): void
    {
        if ($this->has($name)) {
            throw new ComponentAlreadyRegisteredException($name);
        }

        $this->components[$name] = $component;
        
        // Dispatch component registered event
        $this->events->dispatch(new ComponentRegisteredEvent($name, $component));
    }

    public function get(string $name): ?UIComponentSchema
    {
        return $this->components[$name] ?? null;
    }

    public function has(string $name): bool
    {
        return isset($this->components[$name]);
    }

    public function all(): array
    {
        return $this->components;
    }

    public function getValidator(): CompositeValidator
    {
        return $this->validator;
    }
}
