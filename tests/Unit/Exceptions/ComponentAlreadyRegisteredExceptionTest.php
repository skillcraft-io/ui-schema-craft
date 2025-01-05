<?php

namespace Skillcraft\UiSchemaCraft\Tests\Unit\Exceptions;

use PHPUnit\Framework\TestCase;
use Skillcraft\UiSchemaCraft\Exceptions\ComponentAlreadyRegisteredException;

class ComponentAlreadyRegisteredExceptionTest extends TestCase
{
    /** @test */
    public function it_creates_exception_with_correct_message()
    {
        $componentName = 'test-component';
        
        $exception = new ComponentAlreadyRegisteredException($componentName);
        
        $this->assertInstanceOf(ComponentAlreadyRegisteredException::class, $exception);
        $this->assertEquals("Component 'test-component' is already registered", $exception->getMessage());
    }
}
