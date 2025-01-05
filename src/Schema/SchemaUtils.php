<?php

namespace Skillcraft\UiSchemaCraft\Schema;

class SchemaUtils
{
    public static function tailwindColor(string $name, array $defaults = []): Property
    {
        return Property::object($name)->withBuilder(function (PropertyBuilder $builder) use ($defaults) {
            $builder->string('background')
                ->setDefault($defaults['background'] ?? 'bg-gray-500')
                ->description('Background color class');
            
            $builder->string('text')
                ->setDefault($defaults['text'] ?? 'text-white')
                ->description('Text color class');
            
            $builder->string('hover')
                ->setDefault($defaults['hover'] ?? 'hover:bg-gray-600')
                ->description('Hover state class');
        });
    }

    public static function tailwindSpacing(string $name, array $defaults = []): Property
    {
        return Property::object($name)->withBuilder(function (PropertyBuilder $builder) use ($defaults) {
            $builder->string('padding')
                ->setDefault($defaults['padding'] ?? 'p-4')
                ->description('Padding class');
            
            $builder->string('margin')
                ->setDefault($defaults['margin'] ?? 'm-0')
                ->description('Margin class');
            
            $builder->string('gap')
                ->setDefault($defaults['gap'] ?? 'gap-4')
                ->description('Gap class for flex/grid containers');
        });
    }

    public static function tailwindTypography(string $name, array $defaults = []): Property
    {
        return Property::object($name)->withBuilder(function (PropertyBuilder $builder) use ($defaults) {
            $builder->string('size')
                ->setDefault($defaults['size'] ?? 'text-base')
                ->description('Font size class');
            
            $builder->string('weight')
                ->setDefault($defaults['weight'] ?? 'font-normal')
                ->description('Font weight class');
            
            $builder->string('color')
                ->setDefault($defaults['color'] ?? 'text-gray-900')
                ->description('Text color class');
            
            $builder->string('align')
                ->setDefault($defaults['align'] ?? 'text-left')
                ->description('Text alignment class');
            
            $builder->string('lineHeight')
                ->setDefault($defaults['lineHeight'] ?? 'leading-normal')
                ->description('Line height class');
        });
    }

    public static function tailwindContainer(string $name, array $defaults = []): Property
    {
        return Property::object($name)->withBuilder(function (PropertyBuilder $builder) use ($defaults) {
            $builder->string('background')
                ->setDefault($defaults['background'] ?? 'bg-white')
                ->description('Background color class');
            
            $builder->string('rounded')
                ->setDefault($defaults['rounded'] ?? 'rounded-lg')
                ->description('Border radius class');
            
            $builder->string('shadow')
                ->setDefault($defaults['shadow'] ?? 'shadow')
                ->description('Box shadow class');
            
            $builder->string('border')
                ->setDefault($defaults['border'] ?? 'border')
                ->description('Border class');
        });
    }

    public static function tailwindFlex(string $name, array $defaults = []): Property
    {
        return Property::object($name)->withBuilder(function (PropertyBuilder $builder) use ($defaults) {
            $builder->string('display')
                ->setDefault($defaults['display'] ?? 'flex')
                ->description('Display class');
            
            $builder->string('direction')
                ->setDefault($defaults['direction'] ?? 'flex-row')
                ->description('Flex direction class');
            
            $builder->string('wrap')
                ->setDefault($defaults['wrap'] ?? 'flex-wrap')
                ->description('Flex wrap class');
            
            $builder->string('justify')
                ->setDefault($defaults['justify'] ?? 'justify-start')
                ->description('Justify content class');
            
            $builder->string('align')
                ->setDefault($defaults['align'] ?? 'items-start')
                ->description('Align items class');
            
            $builder->string('gap')
                ->setDefault($defaults['gap'] ?? 'gap-4')
                ->description('Gap between items');
        });
    }

    public static function tailwindGrid(string $name, array $defaults = []): Property
    {
        return Property::object($name)->withBuilder(function (PropertyBuilder $builder) use ($defaults) {
            $builder->string('display')
                ->setDefault($defaults['display'] ?? 'grid')
                ->description('Display class');
            
            $builder->string('cols')
                ->setDefault($defaults['cols'] ?? 'grid-cols-1')
                ->description('Grid columns class');
            
            $builder->string('rows')
                ->setDefault($defaults['rows'] ?? 'grid-rows-1')
                ->description('Grid rows class');
            
            $builder->string('gap')
                ->setDefault($defaults['gap'] ?? 'gap-4')
                ->description('Gap between grid items');
            
            $builder->string('flow')
                ->setDefault($defaults['flow'] ?? 'grid-flow-row')
                ->description('Grid auto flow direction');
        });
    }

    public static function tailwindResponsive(string $name, array $breakpoints): Property
    {
        return Property::object($name)->withBuilder(function (PropertyBuilder $builder) use ($breakpoints) {
            foreach ($breakpoints as $screen => $value) {
                $builder->string($screen)
                    ->setDefault($value)
                    ->description("Value for {$screen} breakpoint");
            }
        });
    }

    public static function tailwindAnimation(string $name, array $defaults = []): Property
    {
        return Property::object($name)->withBuilder(function (PropertyBuilder $builder) use ($defaults) {
            $builder->string('transition')
                ->setDefault($defaults['transition'] ?? 'transition')
                ->description('Transition class');
            
            $builder->string('duration')
                ->setDefault($defaults['duration'] ?? 'duration-300')
                ->description('Transition duration class');
            
            $builder->string('timing')
                ->setDefault($defaults['timing'] ?? 'ease-in-out')
                ->description('Transition timing function class');
            
            $builder->string('animate')
                ->setDefault($defaults['animate'] ?? '')
                ->description('Animation class');
        });
    }

    public static function tailwindInteractive(string $name, array $defaults = []): Property
    {
        return Property::object($name)->withBuilder(function (PropertyBuilder $builder) use ($defaults) {
            $builder->string('hover')
                ->setDefault($defaults['hover'] ?? '')
                ->description('Hover state classes');
            
            $builder->string('focus')
                ->setDefault($defaults['focus'] ?? '')
                ->description('Focus state classes');
            
            $builder->string('active')
                ->setDefault($defaults['active'] ?? '')
                ->description('Active state classes');
            
            $builder->string('disabled')
                ->setDefault($defaults['disabled'] ?? '')
                ->description('Disabled state classes');
        });
    }
}
