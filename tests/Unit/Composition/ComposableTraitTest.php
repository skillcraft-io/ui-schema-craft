<?php

namespace Skillcraft\UiSchemaCraft\Tests\Unit\Composition;

use Skillcraft\UiSchemaCraft\Tests\TestCase;
use Skillcraft\UiSchemaCraft\Composition\ComposableTrait;
use Skillcraft\UiSchemaCraft\Abstracts\UIComponentSchema;

class ComposableTraitTest extends TestCase
{
    private $composable;

    protected function setUp(): void
    {
        parent::setUp();
        $this->composable = new class {
            use ComposableTrait;

            public function toArray(): array
            {
                return $this->getChildrenSchema();
            }
        };
    }

    /** @test */
    public function it_can_add_child_component()
    {
        $component = $this->createMock(UIComponentSchema::class);
        
        $result = $this->composable->addChild('test', $component);
        
        $this->assertSame($this->composable, $result);
        $this->assertTrue($this->composable->hasChildren());
        $this->assertCount(1, $this->composable->getChildren());
    }

    /** @test */
    public function it_can_get_children()
    {
        $component1 = $this->createMock(UIComponentSchema::class);
        $component2 = $this->createMock(UIComponentSchema::class);
        
        $this->composable->addChild('test1', $component1);
        $this->composable->addChild('test2', $component2);
        
        $children = $this->composable->getChildren();
        
        $this->assertCount(2, $children);
        $this->assertContains($component1, $children);
        $this->assertContains($component2, $children);
    }

    /** @test */
    public function it_can_remove_child()
    {
        $component = $this->createMock(UIComponentSchema::class);
        
        $this->composable->addChild('test', $component);
        $this->assertTrue($this->composable->hasChildren());
        
        $result = $this->composable->removeChild('test');
        
        $this->assertSame($this->composable, $result);
        $this->assertFalse($this->composable->hasChildren());
        $this->assertEmpty($this->composable->getChildren());
    }

    /** @test */
    public function it_can_check_if_has_children()
    {
        $this->assertFalse($this->composable->hasChildren());
        
        $component = $this->createMock(UIComponentSchema::class);
        $this->composable->addChild('test', $component);
        
        $this->assertTrue($this->composable->hasChildren());
    }

    /** @test */
    public function it_can_get_children_schema()
    {
        $component = $this->createMock(UIComponentSchema::class);
        $component->method('toArray')->willReturn(['type' => 'test']);
        
        $this->composable->addChild('test', $component);
        
        $schema = $this->composable->toArray();
        
        $this->assertEquals(['test' => ['type' => 'test']], $schema);
    }
}
