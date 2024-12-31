<?php

namespace Skillcraft\UiSchemaCraft\Schema;

class PresetSchema
{
    public static function button(string $name, array $defaults = []): Property
    {
        return Property::object($name)->withBuilder(function (PropertyBuilder $builder) use ($defaults) {
            // Core properties
            $builder->string('text')
                ->default($defaults['text'] ?? '')
                ->description('Button text content');

            $builder->string('type')
                ->enum(['button', 'submit', 'reset'])
                ->default($defaults['type'] ?? 'button')
                ->description('HTML button type');

            $builder->boolean('disabled')
                ->default($defaults['disabled'] ?? false)
                ->description('Whether the button is disabled');

            // Variant presets
            $builder->string('variant')
                ->enum(['primary', 'secondary', 'outline', 'text'])
                ->default($defaults['variant'] ?? 'primary')
                ->description('Button style variant');

            $builder->string('size')
                ->enum(['sm', 'md', 'lg'])
                ->default($defaults['size'] ?? 'md')
                ->description('Button size variant');

            // Icon support
            $builder->string('iconLeft')
                ->default($defaults['iconLeft'] ?? '')
                ->description('Icon class to show before text');

            $builder->string('iconRight')
                ->default($defaults['iconRight'] ?? '')
                ->description('Icon class to show after text');

            // UI customization
            $builder->add(SchemaUtils::tailwindContainer('container', [
                'background' => 'bg-blue-500',
                'rounded' => 'rounded-md',
            ]));

            $builder->add(SchemaUtils::tailwindSpacing('spacing', [
                'padding' => 'px-4 py-2',
            ]));

            $builder->add(SchemaUtils::tailwindTypography('text', [
                'color' => 'text-white',
                'weight' => 'font-medium',
            ]));

            $builder->add(SchemaUtils::tailwindInteractive('states', [
                'hover' => 'hover:bg-blue-600',
                'focus' => 'focus:ring-2 focus:ring-blue-500 focus:ring-offset-2',
                'disabled' => 'opacity-50 cursor-not-allowed',
            ]));
        });
    }

    public static function input(string $name, array $defaults = []): Property
    {
        return Property::object($name)->withBuilder(function (PropertyBuilder $builder) use ($defaults) {
            // Core properties
            $builder->string('type')
                ->enum(['text', 'email', 'password', 'number', 'tel', 'url'])
                ->default($defaults['type'] ?? 'text')
                ->description('Input type');

            $builder->string('placeholder')
                ->default($defaults['placeholder'] ?? '')
                ->description('Placeholder text');

            $builder->boolean('required')
                ->default($defaults['required'] ?? false)
                ->description('Whether the input is required');

            $builder->boolean('disabled')
                ->default($defaults['disabled'] ?? false)
                ->description('Whether the input is disabled');

            // Validation
            $builder->string('pattern')
                ->default($defaults['pattern'] ?? '')
                ->description('HTML5 validation pattern');

            $builder->string('min')
                ->default($defaults['min'] ?? '')
                ->description('Minimum value for number inputs');

            $builder->string('max')
                ->default($defaults['max'] ?? '')
                ->description('Maximum value for number inputs');

            // UI customization
            $builder->add(SchemaUtils::tailwindContainer('container', [
                'background' => 'bg-white',
                'rounded' => 'rounded-md',
                'border' => 'border-gray-300',
            ]));

            $builder->add(SchemaUtils::tailwindSpacing('spacing', [
                'padding' => 'px-3 py-2',
            ]));

            $builder->add(SchemaUtils::tailwindTypography('text', [
                'size' => 'text-sm',
                'color' => 'text-gray-900',
            ]));

            $builder->add(SchemaUtils::tailwindInteractive('states', [
                'hover' => 'hover:border-gray-400',
                'focus' => 'focus:ring-2 focus:ring-blue-500 focus:border-blue-500',
                'disabled' => 'opacity-50 cursor-not-allowed',
            ]));
        });
    }

    public static function card(string $name, array $defaults = []): Property
    {
        return Property::object($name)->withBuilder(function (PropertyBuilder $builder) use ($defaults) {
            // Core properties
            $builder->string('title')
                ->default($defaults['title'] ?? '')
                ->description('Card title');

            $builder->string('subtitle')
                ->default($defaults['subtitle'] ?? '')
                ->description('Card subtitle');

            $builder->string('image')
                ->default($defaults['image'] ?? '')
                ->description('Card image URL');

            // Layout customization
            $builder->add(SchemaUtils::tailwindContainer('container', [
                'background' => 'bg-white',
                'rounded' => 'rounded-lg',
                'shadow' => 'shadow-md',
            ]));

            $builder->add(SchemaUtils::tailwindSpacing('spacing', [
                'padding' => 'p-4',
            ]));

            // Typography
            $builder->add(SchemaUtils::tailwindTypography('title', [
                'size' => 'text-lg',
                'weight' => 'font-semibold',
                'color' => 'text-gray-900',
            ]));

            $builder->add(SchemaUtils::tailwindTypography('subtitle', [
                'size' => 'text-sm',
                'color' => 'text-gray-500',
            ]));

            // Interactive states
            $builder->add(SchemaUtils::tailwindInteractive('states', [
                'hover' => 'hover:shadow-lg',
            ]));
        });
    }

    public static function badge(string $name, array $defaults = []): Property
    {
        return Property::object($name)->withBuilder(function (PropertyBuilder $builder) use ($defaults) {
            // Core properties
            $builder->string('text')
                ->default($defaults['text'] ?? '')
                ->description('Badge text');

            $builder->string('variant')
                ->enum(['success', 'warning', 'error', 'info'])
                ->default($defaults['variant'] ?? 'info')
                ->description('Badge style variant');

            // UI customization
            $builder->add(SchemaUtils::tailwindContainer('container', [
                'background' => 'bg-blue-100',
                'rounded' => 'rounded-full',
            ]));

            $builder->add(SchemaUtils::tailwindSpacing('spacing', [
                'padding' => 'px-2.5 py-0.5',
            ]));

            $builder->add(SchemaUtils::tailwindTypography('text', [
                'size' => 'text-xs',
                'weight' => 'font-medium',
                'color' => 'text-blue-800',
            ]));
        });
    }

    public static function alert(string $name, array $defaults = []): Property
    {
        return Property::object($name)->withBuilder(function (PropertyBuilder $builder) use ($defaults) {
            // Core properties
            $builder->string('title')
                ->default($defaults['title'] ?? '')
                ->description('Alert title');

            $builder->string('message')
                ->default($defaults['message'] ?? '')
                ->description('Alert message');

            $builder->string('type')
                ->enum(['success', 'warning', 'error', 'info'])
                ->default($defaults['type'] ?? 'info')
                ->description('Alert type');

            $builder->boolean('dismissible')
                ->default($defaults['dismissible'] ?? true)
                ->description('Whether the alert can be dismissed');

            // UI customization
            $builder->add(SchemaUtils::tailwindContainer('container', [
                'background' => 'bg-blue-50',
                'rounded' => 'rounded-md',
                'border' => 'border-l-4 border-blue-400',
            ]));

            $builder->add(SchemaUtils::tailwindSpacing('spacing', [
                'padding' => 'p-4',
            ]));

            $builder->add(SchemaUtils::tailwindTypography('title', [
                'size' => 'text-sm',
                'weight' => 'font-medium',
                'color' => 'text-blue-800',
            ]));

            $builder->add(SchemaUtils::tailwindTypography('message', [
                'size' => 'text-sm',
                'color' => 'text-blue-700',
            ]));

            // Icon configuration
            $builder->string('icon')
                ->default($defaults['icon'] ?? 'fas fa-info-circle')
                ->description('Alert icon class');
        });
    }

    public static function modal(string $name, array $defaults = []): Property
    {
        return Property::object($name)->withBuilder(function (PropertyBuilder $builder) use ($defaults) {
            // Core properties
            $builder->string('title')
                ->default($defaults['title'] ?? '')
                ->description('Modal title');

            $builder->boolean('open')
                ->default($defaults['open'] ?? false)
                ->description('Whether the modal is open');

            $builder->string('size')
                ->enum(['sm', 'md', 'lg', 'xl', 'full'])
                ->default($defaults['size'] ?? 'md')
                ->description('Modal size');

            // UI customization
            $builder->add(SchemaUtils::tailwindContainer('overlay', [
                'background' => 'bg-black bg-opacity-50',
            ]));

            $builder->add(SchemaUtils::tailwindContainer('container', [
                'background' => 'bg-white',
                'rounded' => 'rounded-lg',
                'shadow' => 'shadow-xl',
            ]));

            $builder->add(SchemaUtils::tailwindSpacing('spacing', [
                'padding' => 'p-6',
            ]));

            $builder->add(SchemaUtils::tailwindTypography('title', [
                'size' => 'text-lg',
                'weight' => 'font-semibold',
                'color' => 'text-gray-900',
            ]));

            // Animation
            $builder->add(SchemaUtils::tailwindAnimation('animation', [
                'transition' => 'transition-all',
                'duration' => 'duration-300',
                'timing' => 'ease-out',
            ]));
        });
    }
}
