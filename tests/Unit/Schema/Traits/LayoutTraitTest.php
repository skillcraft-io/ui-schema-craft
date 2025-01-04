<?php

namespace Skillcraft\UiSchemaCraft\Tests\Unit\Schema\Traits;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversClass;
use Skillcraft\UiSchemaCraft\Tests\TestCase;
use Skillcraft\UiSchemaCraft\Schema\PropertyBuilder;
use Skillcraft\UiSchemaCraft\Schema\Traits\LayoutTrait;

#[CoversClass(LayoutTrait::class)]
class LayoutTraitTest extends TestCase
{
    protected PropertyBuilder $builder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->builder = new PropertyBuilder();
    }

    #[Test]
    public function it_creates_grid_layout_property()
    {
        $property = $this->builder->grid('gallery', 'Image Gallery', [
            'cols' => 'grid-cols-2',
            'gap' => 'gap-6'
        ]);
        $schema = $property->toArray();

        $this->assertEquals('gallery', $schema['name']);
        $this->assertEquals('Image Gallery', $schema['description']);
        $this->assertEquals('object', $schema['type']);
        
        // Check grid properties
        $this->assertArrayHasKey('cols', $schema['properties']);
        $this->assertArrayHasKey('gap', $schema['properties']);
        $this->assertArrayHasKey('items', $schema['properties']);
        $this->assertArrayHasKey('style', $schema['properties']);
        
        // Check default values
        $this->assertEquals('grid-cols-2', $schema['properties']['cols']['default']);
        $this->assertEquals('gap-6', $schema['properties']['gap']['default']);
    }

    #[Test]
    public function it_creates_grid_with_default_values()
    {
        $property = $this->builder->grid('gallery');
        $schema = $property->toArray();

        $this->assertEquals('gallery', $schema['name']);
        $this->assertEquals('object', $schema['type']);
        
        // Check default values
        $this->assertEquals('grid-cols-1 md:grid-cols-2 lg:grid-cols-3', $schema['properties']['cols']['default']);
        $this->assertEquals('gap-4', $schema['properties']['gap']['default']);
    }

    #[Test]
    public function it_creates_grid_with_callback()
    {
        $property = $this->builder->grid('gallery', function (PropertyBuilder $builder) {
            $builder->string('title');
            $builder->string('imageUrl');
        });
        $schema = $property->toArray();

        $this->assertEquals('gallery', $schema['name']);
        $this->assertEquals('object', $schema['type']);
        
        // Check builder properties
        $this->assertArrayHasKey('title', $schema['properties']);
        $this->assertArrayHasKey('imageUrl', $schema['properties']);
    }

    #[Test]
    public function it_creates_flex_layout_property()
    {
        $property = $this->builder->flex('toolbar', 'Action Buttons', [
            'justify' => 'end',
            'align' => 'center',
            'direction' => 'row',
            'wrap' => true,
            'spacing' => 'space-x-6'
        ]);
        $schema = $property->toArray();

        $this->assertEquals('toolbar', $schema['name']);
        $this->assertEquals('Action Buttons', $schema['description']);
        $this->assertEquals('object', $schema['type']);
        
        // Check flex properties
        $this->assertArrayHasKey('justify', $schema['properties']);
        $this->assertArrayHasKey('align', $schema['properties']);
        $this->assertArrayHasKey('direction', $schema['properties']);
        $this->assertArrayHasKey('wrap', $schema['properties']);
        $this->assertArrayHasKey('spacing', $schema['properties']);
        $this->assertArrayHasKey('items', $schema['properties']);
        $this->assertArrayHasKey('style', $schema['properties']);
        
        // Check default values
        $this->assertEquals('end', $schema['properties']['justify']['default']);
        $this->assertEquals('center', $schema['properties']['align']['default']);
        $this->assertEquals('row', $schema['properties']['direction']['default']);
        $this->assertTrue($schema['properties']['wrap']['default']);
        $this->assertEquals('space-x-6', $schema['properties']['spacing']['default']);
    }

    #[Test]
    public function it_creates_flex_with_default_values()
    {
        $property = $this->builder->flex('toolbar');
        $schema = $property->toArray();

        $this->assertEquals('toolbar', $schema['name']);
        $this->assertEquals('object', $schema['type']);
        
        // Check default values
        $this->assertEquals('start', $schema['properties']['justify']['default']);
        $this->assertEquals('start', $schema['properties']['align']['default']);
        $this->assertEquals('row', $schema['properties']['direction']['default']);
        $this->assertFalse($schema['properties']['wrap']['default']);
        $this->assertEquals('space-x-4', $schema['properties']['spacing']['default']);
    }

    #[Test]
    public function it_creates_flex_with_callback()
    {
        $property = $this->builder->flex('toolbar', function (PropertyBuilder $builder) {
            $builder->string('title');
            $builder->boolean('disabled');
        });
        $schema = $property->toArray();

        $this->assertEquals('toolbar', $schema['name']);
        $this->assertEquals('object', $schema['type']);
        
        // Check builder properties
        $this->assertArrayHasKey('title', $schema['properties']);
        $this->assertArrayHasKey('disabled', $schema['properties']);
    }

    #[Test]
    public function it_creates_container_layout_property()
    {
        $property = $this->builder->container('main', 'Main Content', [
            'maxWidth' => 'max-w-5xl',
            'padding' => 'px-6',
            'margin' => 'my-8',
            'background' => 'bg-gray-50'
        ]);
        $schema = $property->toArray();

        $this->assertEquals('main', $schema['name']);
        $this->assertEquals('Main Content', $schema['description']);
        $this->assertEquals('object', $schema['type']);
        
        // Check container properties
        $this->assertArrayHasKey('maxWidth', $schema['properties']);
        $this->assertArrayHasKey('padding', $schema['properties']);
        $this->assertArrayHasKey('margin', $schema['properties']);
        $this->assertArrayHasKey('background', $schema['properties']);
        $this->assertArrayHasKey('items', $schema['properties']);
        $this->assertArrayHasKey('style', $schema['properties']);
        
        // Check default values
        $this->assertEquals('max-w-5xl', $schema['properties']['maxWidth']['default']);
        $this->assertEquals('px-6', $schema['properties']['padding']['default']);
        $this->assertEquals('my-8', $schema['properties']['margin']['default']);
        $this->assertEquals('bg-gray-50', $schema['properties']['background']['default']);
    }

    #[Test]
    public function it_creates_container_with_default_values()
    {
        $property = $this->builder->container('main');
        $schema = $property->toArray();

        $this->assertEquals('main', $schema['name']);
        $this->assertEquals('object', $schema['type']);
        
        // Check default values
        $this->assertEquals('max-w-7xl', $schema['properties']['maxWidth']['default']);
        $this->assertEquals('px-4 sm:px-6 lg:px-8', $schema['properties']['padding']['default']);
        $this->assertEquals('mx-auto', $schema['properties']['margin']['default']);
        $this->assertEquals('bg-white', $schema['properties']['background']['default']);
    }

    #[Test]
    public function it_creates_container_with_callback()
    {
        $property = $this->builder->container('main', function (PropertyBuilder $builder) {
            $builder->string('header');
            $builder->string('footer');
        });
        $schema = $property->toArray();

        $this->assertEquals('main', $schema['name']);
        $this->assertEquals('object', $schema['type']);
        
        // Check builder properties
        $this->assertArrayHasKey('header', $schema['properties']);
        $this->assertArrayHasKey('footer', $schema['properties']);
    }

    #[Test]
    public function it_creates_tabs_layout_property()
    {
        $property = $this->builder->tabs('content_tabs', 'Content Sections', [
            'type' => 'card',
            'active' => 'tab1'
        ]);
        $schema = $property->toArray();

        $this->assertEquals('content_tabs', $schema['name']);
        $this->assertEquals('Content Sections', $schema['description']);
        $this->assertEquals('object', $schema['type']);
        
        // Check tabs properties
        $this->assertArrayHasKey('type', $schema['properties']);
        $this->assertArrayHasKey('active', $schema['properties']);
        $this->assertArrayHasKey('items', $schema['properties']);
        $this->assertArrayHasKey('style', $schema['properties']);
        
        // Check default values
        $this->assertEquals('card', $schema['properties']['type']['default']);
        $this->assertEquals('tab1', $schema['properties']['active']['default']);
        
        // Check items structure
        $items = $schema['properties']['items'];
        $this->assertEquals('array', $items['type']);
        $this->assertArrayHasKey('title', $items['items']['properties']);
        $this->assertArrayHasKey('content', $items['items']['properties']);
    }

    #[Test]
    public function it_creates_tabs_with_default_values()
    {
        $property = $this->builder->tabs('content_tabs');
        $schema = $property->toArray();

        $this->assertEquals('content_tabs', $schema['name']);
        $this->assertEquals('object', $schema['type']);
        
        // Check default values
        $this->assertEquals('line', $schema['properties']['type']['default']);
        $this->assertEquals('', $schema['properties']['active']['default']);
    }

    #[Test]
    public function it_creates_tabs_with_callback()
    {
        $property = $this->builder->tabs('content_tabs', function (PropertyBuilder $builder) {
            $builder->string('icon');
            $builder->string('badge');
        });
        $schema = $property->toArray();

        $this->assertEquals('content_tabs', $schema['name']);
        $this->assertEquals('object', $schema['type']);
        
        // Check builder properties
        $this->assertArrayHasKey('icon', $schema['properties']);
        $this->assertArrayHasKey('badge', $schema['properties']);
    }

    #[Test]
    public function it_creates_stack_layout_property()
    {
        $property = $this->builder->stack('form_section', 'Form Fields', [
            'spacing' => 'space-y-6',
            'align' => 'center',
            'direction' => 'vertical'
        ]);
        $schema = $property->toArray();

        $this->assertEquals('form_section', $schema['name']);
        $this->assertEquals('Form Fields', $schema['description']);
        $this->assertEquals('object', $schema['type']);
        $this->assertEquals('space-y-6', $schema['spacing']);
        
        // Check stack properties
        $this->assertArrayHasKey('align', $schema['properties']);
        $this->assertArrayHasKey('direction', $schema['properties']);
        $this->assertArrayHasKey('items', $schema['properties']);
        $this->assertArrayHasKey('style', $schema['properties']);
        
        // Check default values
        $this->assertEquals('center', $schema['properties']['align']['default']);
        $this->assertEquals('vertical', $schema['properties']['direction']['default']);
    }

    #[Test]
    public function it_creates_stack_with_default_values()
    {
        $property = $this->builder->stack('form_section');
        $schema = $property->toArray();

        $this->assertEquals('form_section', $schema['name']);
        $this->assertEquals('object', $schema['type']);
        $this->assertEquals('space-y-4', $schema['spacing']);
        
        // Check default values
        $this->assertEquals('start', $schema['properties']['align']['default']);
        $this->assertEquals('vertical', $schema['properties']['direction']['default']);
    }

    #[Test]
    public function it_creates_stack_with_callback()
    {
        $property = $this->builder->stack('form_section', function (PropertyBuilder $builder) {
            $builder->string('label');
            $builder->boolean('required');
        });
        $schema = $property->toArray();

        $this->assertEquals('form_section', $schema['name']);
        $this->assertEquals('object', $schema['type']);
        
        // Check builder properties
        $this->assertArrayHasKey('label', $schema['properties']);
        $this->assertArrayHasKey('required', $schema['properties']);
    }

    #[Test]
    public function it_creates_section_layout_property()
    {
        $property = $this->builder->section('user_section', 'User Information', [
            'title' => 'Personal Details',
            'collapsible' => true,
            'class' => 'bg-gray-100'
        ]);
        $schema = $property->toArray();

        $this->assertEquals('user_section', $schema['name']);
        $this->assertEquals('User Information', $schema['description']);
        $this->assertEquals('object', $schema['type']);
        $this->assertEquals('bg-gray-100', $schema['class']);
        
        // Check section properties
        $this->assertArrayHasKey('title', $schema['properties']);
        $this->assertArrayHasKey('content', $schema['properties']);
        $this->assertArrayHasKey('collapsible', $schema['properties']);
        $this->assertArrayHasKey('items', $schema['properties']);
        $this->assertArrayHasKey('style', $schema['properties']);
        
        // Check default values
        $this->assertEquals('Personal Details', $schema['properties']['title']['default']);
        $this->assertTrue($schema['properties']['collapsible']['default']);
    }

    #[Test]
    public function it_creates_section_with_default_values()
    {
        $property = $this->builder->section('user_section');
        $schema = $property->toArray();

        $this->assertEquals('user_section', $schema['name']);
        $this->assertEquals('object', $schema['type']);
        $this->assertEquals('', $schema['class']);
        
        // Check default values
        $this->assertEquals('', $schema['properties']['title']['default']);
        $this->assertNull($schema['properties']['content']['default']);
        $this->assertFalse($schema['properties']['collapsible']['default']);
    }

    #[Test]
    public function it_creates_section_with_callback()
    {
        $property = $this->builder->section('user_section', function (PropertyBuilder $builder) {
            $builder->string('subtitle');
            $builder->boolean('expanded');
        });
        $schema = $property->toArray();

        $this->assertEquals('user_section', $schema['name']);
        $this->assertEquals('object', $schema['type']);
        
        // Check builder properties
        $this->assertArrayHasKey('subtitle', $schema['properties']);
        $this->assertArrayHasKey('expanded', $schema['properties']);
    }
}
