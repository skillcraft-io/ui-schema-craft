<?php

namespace Skillcraft\UiSchemaCraft\Tests\Unit\Events;

use PHPUnit\Framework\TestCase;
use Skillcraft\UiSchemaCraft\Abstracts\UIComponentSchema;
use Skillcraft\UiSchemaCraft\Events\ComponentRegisteredEvent;

class ComponentRegisteredEventTest extends TestCase
{
    public function test_constructor_sets_properties(): void
    {
        $component = new class extends UIComponentSchema {
            protected string $type = 'test-component';
            protected string $component = 'test';

            public function properties(): array
            {
                return [];
            }

            protected function getExampleData(): array
            {
                return [];
            }
        };

        $name = 'test-component';
        $event = new ComponentRegisteredEvent($name, $component);

        $this->assertSame($name, $event->name);
        $this->assertSame($component, $event->component);
    }
}
