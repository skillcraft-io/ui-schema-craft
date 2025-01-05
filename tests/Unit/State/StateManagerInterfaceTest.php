<?php

namespace Skillcraft\UiSchemaCraft\Tests\Unit\State;

use Skillcraft\UiSchemaCraft\Tests\TestCase;
use Skillcraft\UiSchemaCraft\State\StateManagerInterface;

class StateManagerInterfaceTest extends TestCase
{
    public function test_interface_exists(): void
    {
        $this->assertTrue(interface_exists(StateManagerInterface::class));
    }

    public function test_interface_methods(): void
    {
        $methods = get_class_methods(StateManagerInterface::class);
        
        $this->assertContains('save', $methods);
        $this->assertContains('load', $methods);
        $this->assertContains('delete', $methods);
        $this->assertContains('getStatesForComponent', $methods);
    }

    public function test_interface_method_parameters(): void
    {
        $reflection = new \ReflectionClass(StateManagerInterface::class);
        
        // Test save method parameters
        $saveMethod = $reflection->getMethod('save');
        $saveParams = $saveMethod->getParameters();
        $this->assertCount(3, $saveParams);
        $this->assertEquals('id', $saveParams[0]->getName());
        $this->assertEquals('component', $saveParams[1]->getName());
        $this->assertEquals('state', $saveParams[2]->getName());
        
        // Test load method parameters
        $loadMethod = $reflection->getMethod('load');
        $loadParams = $loadMethod->getParameters();
        $this->assertCount(1, $loadParams);
        $this->assertEquals('id', $loadParams[0]->getName());
        
        // Test delete method parameters
        $deleteMethod = $reflection->getMethod('delete');
        $deleteParams = $deleteMethod->getParameters();
        $this->assertCount(1, $deleteParams);
        $this->assertEquals('id', $deleteParams[0]->getName());
        
        // Test getStatesForComponent method parameters
        $getStatesMethod = $reflection->getMethod('getStatesForComponent');
        $getStatesParams = $getStatesMethod->getParameters();
        $this->assertCount(1, $getStatesParams);
        $this->assertEquals('componentType', $getStatesParams[0]->getName());
    }
}
