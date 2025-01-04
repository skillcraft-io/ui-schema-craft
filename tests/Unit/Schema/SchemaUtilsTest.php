<?php

namespace Skillcraft\UiSchemaCraft\Tests\Unit\Schema;

use PHPUnit\Framework\Attributes\Test;
use Skillcraft\UiSchemaCraft\Tests\TestCase;
use Skillcraft\UiSchemaCraft\Schema\SchemaUtils;
use Skillcraft\UiSchemaCraft\Schema\Property;

class SchemaUtilsTest extends TestCase
{
    #[Test]
    public function it_creates_tailwind_color_schema()
    {
        $property = SchemaUtils::tailwindColor('color', [
            'background' => 'bg-blue-500',
            'text' => 'text-blue-50',
            'hover' => 'hover:bg-blue-600'
        ]);

        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals('color', $property->getName());

        $data = $property->toArray();
        $this->assertEquals('bg-blue-500', $data['properties']['background']['default']);
        $this->assertEquals('text-blue-50', $data['properties']['text']['default']);
        $this->assertEquals('hover:bg-blue-600', $data['properties']['hover']['default']);
    }

    #[Test]
    public function it_creates_tailwind_color_schema_with_defaults()
    {
        $property = SchemaUtils::tailwindColor('color');

        $data = $property->toArray();
        $this->assertEquals('bg-gray-500', $data['properties']['background']['default']);
        $this->assertEquals('text-white', $data['properties']['text']['default']);
        $this->assertEquals('hover:bg-gray-600', $data['properties']['hover']['default']);
    }

    #[Test]
    public function it_creates_tailwind_spacing_schema()
    {
        $property = SchemaUtils::tailwindSpacing('spacing', [
            'padding' => 'p-6',
            'margin' => 'm-4',
            'gap' => 'gap-6'
        ]);

        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals('spacing', $property->getName());

        $data = $property->toArray();
        $this->assertEquals('p-6', $data['properties']['padding']['default']);
        $this->assertEquals('m-4', $data['properties']['margin']['default']);
        $this->assertEquals('gap-6', $data['properties']['gap']['default']);
    }

    #[Test]
    public function it_creates_tailwind_spacing_schema_with_defaults()
    {
        $property = SchemaUtils::tailwindSpacing('spacing');

        $data = $property->toArray();
        $this->assertEquals('p-4', $data['properties']['padding']['default']);
        $this->assertEquals('m-0', $data['properties']['margin']['default']);
        $this->assertEquals('gap-4', $data['properties']['gap']['default']);
    }

    #[Test]
    public function it_creates_tailwind_typography_schema()
    {
        $property = SchemaUtils::tailwindTypography('typography', [
            'size' => 'text-xl',
            'weight' => 'font-bold',
            'color' => 'text-blue-900',
            'align' => 'text-center',
            'lineHeight' => 'leading-tight'
        ]);

        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals('typography', $property->getName());

        $data = $property->toArray();
        $this->assertEquals('text-xl', $data['properties']['size']['default']);
        $this->assertEquals('font-bold', $data['properties']['weight']['default']);
        $this->assertEquals('text-blue-900', $data['properties']['color']['default']);
        $this->assertEquals('text-center', $data['properties']['align']['default']);
        $this->assertEquals('leading-tight', $data['properties']['lineHeight']['default']);
    }

    #[Test]
    public function it_creates_tailwind_typography_schema_with_defaults()
    {
        $property = SchemaUtils::tailwindTypography('typography');

        $data = $property->toArray();
        $this->assertEquals('text-base', $data['properties']['size']['default']);
        $this->assertEquals('font-normal', $data['properties']['weight']['default']);
        $this->assertEquals('text-gray-900', $data['properties']['color']['default']);
        $this->assertEquals('text-left', $data['properties']['align']['default']);
        $this->assertEquals('leading-normal', $data['properties']['lineHeight']['default']);
    }

    #[Test]
    public function it_creates_tailwind_container_schema()
    {
        $property = SchemaUtils::tailwindContainer('container', [
            'background' => 'bg-gray-100',
            'rounded' => 'rounded-xl',
            'shadow' => 'shadow-xl',
            'border' => 'border-2'
        ]);

        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals('container', $property->getName());

        $data = $property->toArray();
        $this->assertEquals('bg-gray-100', $data['properties']['background']['default']);
        $this->assertEquals('rounded-xl', $data['properties']['rounded']['default']);
        $this->assertEquals('shadow-xl', $data['properties']['shadow']['default']);
        $this->assertEquals('border-2', $data['properties']['border']['default']);
    }

    #[Test]
    public function it_creates_tailwind_container_schema_with_defaults()
    {
        $property = SchemaUtils::tailwindContainer('container');

        $data = $property->toArray();
        $this->assertEquals('bg-white', $data['properties']['background']['default']);
        $this->assertEquals('rounded-lg', $data['properties']['rounded']['default']);
        $this->assertEquals('shadow', $data['properties']['shadow']['default']);
        $this->assertEquals('border', $data['properties']['border']['default']);
    }

    #[Test]
    public function it_creates_tailwind_flex_schema()
    {
        $property = SchemaUtils::tailwindFlex('flex', [
            'display' => 'inline-flex',
            'direction' => 'flex-col',
            'wrap' => 'flex-nowrap',
            'justify' => 'justify-center',
            'align' => 'items-center',
            'gap' => 'gap-6'
        ]);

        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals('flex', $property->getName());

        $data = $property->toArray();
        $this->assertEquals('inline-flex', $data['properties']['display']['default']);
        $this->assertEquals('flex-col', $data['properties']['direction']['default']);
        $this->assertEquals('flex-nowrap', $data['properties']['wrap']['default']);
        $this->assertEquals('justify-center', $data['properties']['justify']['default']);
        $this->assertEquals('items-center', $data['properties']['align']['default']);
        $this->assertEquals('gap-6', $data['properties']['gap']['default']);
    }

    #[Test]
    public function it_creates_tailwind_flex_schema_with_defaults()
    {
        $property = SchemaUtils::tailwindFlex('flex');

        $data = $property->toArray();
        $this->assertEquals('flex', $data['properties']['display']['default']);
        $this->assertEquals('flex-row', $data['properties']['direction']['default']);
        $this->assertEquals('flex-wrap', $data['properties']['wrap']['default']);
        $this->assertEquals('justify-start', $data['properties']['justify']['default']);
        $this->assertEquals('items-start', $data['properties']['align']['default']);
        $this->assertEquals('gap-4', $data['properties']['gap']['default']);
    }

    #[Test]
    public function it_creates_tailwind_grid_schema()
    {
        $property = SchemaUtils::tailwindGrid('grid', [
            'display' => 'inline-grid',
            'cols' => 'grid-cols-3',
            'rows' => 'grid-rows-2',
            'gap' => 'gap-6',
            'flow' => 'grid-flow-col'
        ]);

        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals('grid', $property->getName());

        $data = $property->toArray();
        $this->assertEquals('inline-grid', $data['properties']['display']['default']);
        $this->assertEquals('grid-cols-3', $data['properties']['cols']['default']);
        $this->assertEquals('grid-rows-2', $data['properties']['rows']['default']);
        $this->assertEquals('gap-6', $data['properties']['gap']['default']);
        $this->assertEquals('grid-flow-col', $data['properties']['flow']['default']);
    }

    #[Test]
    public function it_creates_tailwind_grid_schema_with_defaults()
    {
        $property = SchemaUtils::tailwindGrid('grid');

        $data = $property->toArray();
        $this->assertEquals('grid', $data['properties']['display']['default']);
        $this->assertEquals('grid-cols-1', $data['properties']['cols']['default']);
        $this->assertEquals('grid-rows-1', $data['properties']['rows']['default']);
        $this->assertEquals('gap-4', $data['properties']['gap']['default']);
        $this->assertEquals('grid-flow-row', $data['properties']['flow']['default']);
    }

    #[Test]
    public function it_creates_tailwind_responsive_schema()
    {
        $property = SchemaUtils::tailwindResponsive('responsive', [
            'sm' => 'text-sm',
            'md' => 'text-base',
            'lg' => 'text-lg'
        ]);

        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals('responsive', $property->getName());

        $data = $property->toArray();
        $this->assertEquals('text-sm', $data['properties']['sm']['default']);
        $this->assertEquals('text-base', $data['properties']['md']['default']);
        $this->assertEquals('text-lg', $data['properties']['lg']['default']);
    }

    #[Test]
    public function it_creates_tailwind_animation_schema()
    {
        $property = SchemaUtils::tailwindAnimation('animation', [
            'transition' => 'transition-all',
            'duration' => 'duration-500',
            'timing' => 'ease-out',
            'animate' => 'animate-spin'
        ]);

        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals('animation', $property->getName());

        $data = $property->toArray();
        $this->assertEquals('transition-all', $data['properties']['transition']['default']);
        $this->assertEquals('duration-500', $data['properties']['duration']['default']);
        $this->assertEquals('ease-out', $data['properties']['timing']['default']);
        $this->assertEquals('animate-spin', $data['properties']['animate']['default']);
    }

    #[Test]
    public function it_creates_tailwind_animation_schema_with_defaults()
    {
        $property = SchemaUtils::tailwindAnimation('animation');

        $data = $property->toArray();
        $this->assertEquals('transition', $data['properties']['transition']['default']);
        $this->assertEquals('duration-300', $data['properties']['duration']['default']);
        $this->assertEquals('ease-in-out', $data['properties']['timing']['default']);
        $this->assertEquals('', $data['properties']['animate']['default']);
    }

    #[Test]
    public function it_creates_tailwind_interactive_schema()
    {
        $property = SchemaUtils::tailwindInteractive('interactive', [
            'hover' => 'hover:bg-blue-600',
            'focus' => 'focus:ring-2',
            'active' => 'active:bg-blue-700',
            'disabled' => 'disabled:opacity-50'
        ]);

        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals('interactive', $property->getName());

        $data = $property->toArray();
        $this->assertEquals('hover:bg-blue-600', $data['properties']['hover']['default']);
        $this->assertEquals('focus:ring-2', $data['properties']['focus']['default']);
        $this->assertEquals('active:bg-blue-700', $data['properties']['active']['default']);
        $this->assertEquals('disabled:opacity-50', $data['properties']['disabled']['default']);
    }

    #[Test]
    public function it_creates_tailwind_interactive_schema_with_defaults()
    {
        $property = SchemaUtils::tailwindInteractive('interactive');

        $data = $property->toArray();
        $this->assertEquals('', $data['properties']['hover']['default']);
        $this->assertEquals('', $data['properties']['focus']['default']);
        $this->assertEquals('', $data['properties']['active']['default']);
        $this->assertEquals('', $data['properties']['disabled']['default']);
    }
}
