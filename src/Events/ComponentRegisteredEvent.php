<?php

namespace Skillcraft\UiSchemaCraft\Events;

use Skillcraft\UiSchemaCraft\Abstracts\UIComponentSchema;

class ComponentRegisteredEvent
{
    public function __construct(
        public readonly string $name,
        public readonly UIComponentSchema $component
    ) {}
}
