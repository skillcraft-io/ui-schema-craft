<?php

namespace Skillcraft\UiSchemaCraft\Tests\Unit;

use Skillcraft\UiSchemaCraft\Tests\TestCase;
use Skillcraft\UiSchemaCraft\Schema\PropertyBuilder;

class PropertyBuilderTest extends TestCase
{
    /** @test */
    public function it_can_create_a_string_property()
    {
        $builder = new PropertyBuilder();
        $property = $builder->string('name', 'Full Name');

        $this->assertEquals('name', $property->getName());
        $this->assertEquals('Full Name', $property->getDescription());
        $this->assertEquals('string', $property->getType());
    }

    /** @test */
    public function it_can_create_a_dynamic_form()
    {
        $builder = new PropertyBuilder();
        $form = $builder->dynamicForm('registration', 'Registration Form');

        $this->assertEquals('registration', $form->getName());
        $this->assertEquals('Registration Form', $form->getDescription());
    }

    /** @test */
    public function it_can_create_a_chart()
    {
        $builder = new PropertyBuilder();
        $chart = $builder->chart('sales', 'Sales Chart')
            ->withBuilder(function($b) {
                $b->value->type('line');
            });

        $this->assertEquals('sales', $chart->getName());
        $this->assertEquals('Sales Chart', $chart->getDescription());
    }
}
