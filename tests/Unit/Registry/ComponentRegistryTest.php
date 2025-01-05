<?php

namespace Skillcraft\UiSchemaCraft\Tests\Unit\Registry;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversClass;
use Skillcraft\UiSchemaCraft\Registry\ComponentRegistry;
use Skillcraft\UiSchemaCraft\Registry\ComponentRegistryInterface;
use Skillcraft\UiSchemaCraft\Events\ComponentRegisteredEvent;
use Skillcraft\UiSchemaCraft\Exceptions\ComponentAlreadyRegisteredException;
use Skillcraft\UiSchemaCraft\Abstracts\UIComponentSchema;
use Illuminate\Contracts\Events\Dispatcher;
use Skillcraft\UiSchemaCraft\Validation\ValidationResult;

#[CoversClass(ComponentRegistry::class)]
class ComponentRegistryTest extends TestCase
{
    private Dispatcher $events;
    private ComponentRegistry $registry;
    private UIComponentSchema $mockComponent;

    protected function setUp(): void
    {
        $this->events = $this->createMock(Dispatcher::class);
        $this->registry = new ComponentRegistry($this->events);
        
        // Create a mock UIComponentSchema
        $this->mockComponent = new class extends UIComponentSchema {
            protected string $type = 'test';
            protected string $component = 'test-component';
            public function properties(): array { return []; }
            public function getExampleData(): array { return []; }
        };
    }

    #[Test]
    public function it_implements_registry_interface()
    {
        $this->assertInstanceOf(ComponentRegistryInterface::class, $this->registry);
    }

    #[Test]
    public function it_registers_component()
    {
        $name = 'test-component';

        $this->events->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(function ($event) use ($name) {
                return $event instanceof ComponentRegisteredEvent
                    && $event->name === $name
                    && $event->component === $this->mockComponent;
            }));

        $this->registry->register($name, $this->mockComponent);

        $this->assertTrue($this->registry->has($name));
        $this->assertSame($this->mockComponent, $this->registry->get($name));
    }

    #[Test]
    public function it_throws_exception_when_registering_existing_component()
    {
        $name = 'test-component';

        $this->registry->register($name, $this->mockComponent);

        $this->expectException(ComponentAlreadyRegisteredException::class);
        $this->expectExceptionMessage("Component 'test-component' is already registered");

        $anotherComponent = new class extends UIComponentSchema {
            protected string $type = 'another';
            protected string $component = 'another-component';
            public function properties(): array { return []; }
            public function getExampleData(): array { return []; }
        };

        $this->registry->register($name, $anotherComponent);
    }

    #[Test]
    public function it_returns_null_for_non_existent_component()
    {
        $this->assertNull($this->registry->get('non-existent'));
    }

    #[Test]
    public function it_checks_component_existence()
    {
        $name = 'test-component';

        $this->assertFalse($this->registry->has($name));

        $this->registry->register($name, $this->mockComponent);

        $this->assertTrue($this->registry->has($name));
    }

    #[Test]
    public function it_returns_all_registered_components()
    {
        $components = [
            'component1' => new class extends UIComponentSchema {
                protected string $type = 'type1';
                protected string $component = 'component1';
                public function properties(): array { return []; }
                public function getExampleData(): array { return []; }
            },
            'component2' => new class extends UIComponentSchema {
                protected string $type = 'type2';
                protected string $component = 'component2';
                public function properties(): array { return []; }
                public function getExampleData(): array { return []; }
            },
            'component3' => new class extends UIComponentSchema {
                protected string $type = 'type3';
                protected string $component = 'component3';
                public function properties(): array { return []; }
                public function getExampleData(): array { return []; }
            },
        ];

        foreach ($components as $name => $component) {
            $this->registry->register($name, $component);
        }

        $this->assertEquals($components, $this->registry->all());
    }

    #[Test]
    public function it_dispatches_event_on_registration()
    {
        $name = 'test-component';

        $this->events->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(function ($event) use ($name) {
                $this->assertInstanceOf(ComponentRegisteredEvent::class, $event);
                $this->assertEquals($name, $event->name);
                $this->assertSame($this->mockComponent, $event->component);
                return true;
            }));

        $this->registry->register($name, $this->mockComponent);
    }
}
