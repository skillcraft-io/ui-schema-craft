<?php

namespace Skillcraft\UiSchemaCraft\Schema;

class SchemaUtils
{
    public static function tailwindColor(string $name, array $defaults = []): Property
    {
        return Property::object($name)->withBuilder(function (PropertyBuilder $builder) use ($defaults) {
            $builder->string('background')
                ->default($defaults['background'] ?? 'bg-gray-500')
                ->description('Background color class');
            
            $builder->string('text')
                ->default($defaults['text'] ?? 'text-white')
                ->description('Text color class');
            
            $builder->string('hover')
                ->default($defaults['hover'] ?? 'hover:bg-gray-600')
                ->description('Hover state class');
        });
    }

    public static function tailwindSpacing(string $name, array $defaults = []): Property
    {
        return Property::object($name)->withBuilder(function (PropertyBuilder $builder) use ($defaults) {
            $builder->string('padding')
                ->default($defaults['padding'] ?? 'p-4')
                ->description('Padding class');
            
            $builder->string('margin')
                ->default($defaults['margin'] ?? 'm-0')
                ->description('Margin class');
            
            $builder->string('gap')
                ->default($defaults['gap'] ?? 'gap-4')
                ->description('Gap class for flex/grid containers');
        });
    }

    public static function tailwindTypography(string $name, array $defaults = []): Property
    {
        return Property::object($name)->withBuilder(function (PropertyBuilder $builder) use ($defaults) {
            $builder->string('size')
                ->default($defaults['size'] ?? 'text-base')
                ->description('Font size class');
            
            $builder->string('weight')
                ->default($defaults['weight'] ?? 'font-normal')
                ->description('Font weight class');
            
            $builder->string('color')
                ->default($defaults['color'] ?? 'text-gray-900')
                ->description('Text color class');
            
            $builder->string('align')
                ->default($defaults['align'] ?? 'text-left')
                ->description('Text alignment class');
            
            $builder->string('lineHeight')
                ->default($defaults['lineHeight'] ?? 'leading-normal')
                ->description('Line height class');
        });
    }

    public static function tailwindContainer(string $name, array $defaults = []): Property
    {
        return Property::object($name)->withBuilder(function (PropertyBuilder $builder) use ($defaults) {
            $builder->string('background')
                ->default($defaults['background'] ?? 'bg-white')
                ->description('Background color class');
            
            $builder->string('rounded')
                ->default($defaults['rounded'] ?? 'rounded-lg')
                ->description('Border radius class');
            
            $builder->string('shadow')
                ->default($defaults['shadow'] ?? 'shadow')
                ->description('Box shadow class');
            
            $builder->string('border')
                ->default($defaults['border'] ?? 'border')
                ->description('Border class');
        });
    }

    public static function tailwindFlex(string $name, array $defaults = []): Property
    {
        return Property::object($name)->withBuilder(function (PropertyBuilder $builder) use ($defaults) {
            $builder->string('display')
                ->default($defaults['display'] ?? 'flex')
                ->description('Display class');
            
            $builder->string('direction')
                ->default($defaults['direction'] ?? 'flex-row')
                ->description('Flex direction class');
            
            $builder->string('wrap')
                ->default($defaults['wrap'] ?? 'flex-wrap')
                ->description('Flex wrap class');
            
            $builder->string('justify')
                ->default($defaults['justify'] ?? 'justify-start')
                ->description('Justify content class');
            
            $builder->string('align')
                ->default($defaults['align'] ?? 'items-start')
                ->description('Align items class');
            
            $builder->string('gap')
                ->default($defaults['gap'] ?? 'gap-4')
                ->description('Gap between items');
        });
    }

    public static function tailwindGrid(string $name, array $defaults = []): Property
    {
        return Property::object($name)->withBuilder(function (PropertyBuilder $builder) use ($defaults) {
            $builder->string('display')
                ->default($defaults['display'] ?? 'grid')
                ->description('Display class');
            
            $builder->string('cols')
                ->default($defaults['cols'] ?? 'grid-cols-1')
                ->description('Grid columns class');
            
            $builder->string('rows')
                ->default($defaults['rows'] ?? 'grid-rows-1')
                ->description('Grid rows class');
            
            $builder->string('gap')
                ->default($defaults['gap'] ?? 'gap-4')
                ->description('Gap between grid items');
            
            $builder->string('flow')
                ->default($defaults['flow'] ?? 'grid-flow-row')
                ->description('Grid auto flow direction');
        });
    }

    public static function tailwindResponsive(string $name, array $breakpoints): Property
    {
        return Property::object($name)->withBuilder(function (PropertyBuilder $builder) use ($breakpoints) {
            foreach ($breakpoints as $screen => $value) {
                $builder->string($screen)
                    ->default($value)
                    ->description("Value for {$screen} breakpoint");
            }
        });
    }

    public static function tailwindAnimation(string $name, array $defaults = []): Property
    {
        return Property::object($name)->withBuilder(function (PropertyBuilder $builder) use ($defaults) {
            $builder->string('transition')
                ->default($defaults['transition'] ?? 'transition')
                ->description('Transition class');
            
            $builder->string('duration')
                ->default($defaults['duration'] ?? 'duration-300')
                ->description('Transition duration class');
            
            $builder->string('timing')
                ->default($defaults['timing'] ?? 'ease-in-out')
                ->description('Transition timing function class');
            
            $builder->string('animate')
                ->default($defaults['animate'] ?? '')
                ->description('Animation class');
        });
    }

    public static function tailwindInteractive(string $name, array $defaults = []): Property
    {
        return Property::object($name)->withBuilder(function (PropertyBuilder $builder) use ($defaults) {
            $builder->string('hover')
                ->default($defaults['hover'] ?? '')
                ->description('Hover state classes');
            
            $builder->string('focus')
                ->default($defaults['focus'] ?? '')
                ->description('Focus state classes');
            
            $builder->string('active')
                ->default($defaults['active'] ?? '')
                ->description('Active state classes');
            
            $builder->string('disabled')
                ->default($defaults['disabled'] ?? '')
                ->description('Disabled state classes');
        });
    }
}
