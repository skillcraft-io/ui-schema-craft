<?php

namespace Skillcraft\UiSchemaCraft\Schema\Traits;

use Skillcraft\UiSchemaCraft\Schema\Property;

trait LayoutTrait
{
    /**
     * Create a grid layout property.
     */
    public function grid(string $name, callable|string|null $labelOrCallback = null, array $options = []): Property
    {
        $property = new Property($name, 'object', is_string($labelOrCallback) ? $labelOrCallback : null);
        $property->addAttribute('properties', [
            'cols' => ['type' => 'string', 'default' => $options['cols'] ?? 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3'],
            'gap' => ['type' => 'string', 'default' => $options['gap'] ?? 'gap-4'],
            'items' => ['type' => 'array', 'items' => ['type' => 'object']],
            'style' => ['type' => 'object', 'properties' => [
                'width' => ['type' => 'string'],
                'padding' => ['type' => 'string'],
                'margin' => ['type' => 'string']
            ]]
        ]);

        if (is_callable($labelOrCallback)) {
            $builder = $this->new();
            $labelOrCallback($builder);
            $property->addAttribute('properties', array_merge(
                $property->getAttribute('properties', []),
                $builder->toArray()
            ));
        }

        return $property;
    }

    /**
     * Create a flex layout property.
     */
    public function flex(string $name, callable|string|null $labelOrCallback = null, array $options = []): Property
    {
        $property = new Property($name, 'object', is_string($labelOrCallback) ? $labelOrCallback : null);
        $property->addAttribute('properties', [
            'justify' => ['type' => 'string', 'default' => $options['justify'] ?? 'start'],
            'align' => ['type' => 'string', 'default' => $options['align'] ?? 'start'],
            'direction' => ['type' => 'string', 'default' => $options['direction'] ?? 'row'],
            'wrap' => ['type' => 'boolean', 'default' => $options['wrap'] ?? false],
            'spacing' => ['type' => 'string', 'default' => $options['spacing'] ?? 'space-x-4'],
            'items' => ['type' => 'array', 'items' => ['type' => 'object']],
            'style' => ['type' => 'object', 'properties' => [
                'width' => ['type' => 'string'],
                'padding' => ['type' => 'string'],
                'margin' => ['type' => 'string']
            ]]
        ]);

        if (is_callable($labelOrCallback)) {
            $builder = $this->new();
            $labelOrCallback($builder);
            $property->addAttribute('properties', array_merge(
                $property->getAttribute('properties', []),
                $builder->toArray()
            ));
        }

        return $property;
    }

    /**
     * Create a container layout property.
     */
    public function container(string $name, callable|string|null $labelOrCallback = null, array $options = []): Property
    {
        $property = new Property($name, 'object', is_string($labelOrCallback) ? $labelOrCallback : null);
        $property->addAttribute('properties', [
            'maxWidth' => ['type' => 'string', 'default' => $options['maxWidth'] ?? 'max-w-7xl'],
            'padding' => ['type' => 'string', 'default' => $options['padding'] ?? 'px-4 sm:px-6 lg:px-8'],
            'margin' => ['type' => 'string', 'default' => $options['margin'] ?? 'mx-auto'],
            'background' => ['type' => 'string', 'default' => $options['background'] ?? 'bg-white'],
            'items' => ['type' => 'array', 'items' => ['type' => 'object']],
            'style' => ['type' => 'object', 'properties' => [
                'width' => ['type' => 'string'],
                'padding' => ['type' => 'string'],
                'margin' => ['type' => 'string']
            ]]
        ]);

        if (is_callable($labelOrCallback)) {
            $builder = $this->new();
            $labelOrCallback($builder);
            $property->addAttribute('properties', array_merge(
                $property->getAttribute('properties', []),
                $builder->toArray()
            ));
        }

        return $property;
    }

    /**
     * Create a tabs layout property.
     */
    public function tabs(string $name, callable|string|null $labelOrCallback = null, array $options = []): Property
    {
        $property = new Property($name, 'object', is_string($labelOrCallback) ? $labelOrCallback : null);
        $property->addAttribute('properties', [
            'type' => ['type' => 'string', 'default' => $options['type'] ?? 'line'],
            'active' => ['type' => 'string', 'default' => $options['active'] ?? ''],
            'items' => ['type' => 'array', 'items' => ['type' => 'object', 'properties' => [
                'title' => ['type' => 'string'],
                'content' => ['type' => 'object']
            ]]],
            'style' => ['type' => 'object', 'properties' => [
                'width' => ['type' => 'string'],
                'padding' => ['type' => 'string'],
                'margin' => ['type' => 'string']
            ]]
        ]);

        if (is_callable($labelOrCallback)) {
            $builder = $this->new();
            $labelOrCallback($builder);
            $property->addAttribute('properties', array_merge(
                $property->getAttribute('properties', []),
                $builder->toArray()
            ));
        }

        return $property;
    }

    /**
     * Create a stack layout property.
     */
    public function stack(string $name, callable|string|null $labelOrCallback = null, array $options = []): Property
    {
        $property = new Property($name, 'object', is_string($labelOrCallback) ? $labelOrCallback : null);
        $property->addAttribute('spacing', $options['spacing'] ?? 'space-y-4');
        $property->addAttribute('properties', [
            'align' => ['type' => 'string', 'default' => $options['align'] ?? 'start'],
            'direction' => ['type' => 'string', 'default' => $options['direction'] ?? 'vertical'],
            'items' => ['type' => 'array', 'items' => ['type' => 'object']],
            'style' => ['type' => 'object', 'properties' => [
                'width' => ['type' => 'string'],
                'padding' => ['type' => 'string'],
                'margin' => ['type' => 'string']
            ]]
        ]);

        if (is_callable($labelOrCallback)) {
            $builder = $this->new();
            $labelOrCallback($builder);
            $property->addAttribute('properties', array_merge(
                $property->getAttribute('properties', []),
                $builder->toArray()
            ));
        }

        return $property;
    }

    /**
     * Create a section layout property.
     */
    public function section(string $name, callable|string|null $labelOrCallback = null, array $options = []): Property
    {
        $property = new Property($name, 'object', is_string($labelOrCallback) ? $labelOrCallback : null);
        $property->addAttribute('class', $options['class'] ?? '');
        $property->addAttribute('properties', [
            'title' => ['type' => 'string', 'default' => $options['title'] ?? ''],
            'content' => ['type' => 'object', 'default' => $options['content'] ?? null],
            'collapsible' => ['type' => 'boolean', 'default' => $options['collapsible'] ?? false],
            'items' => ['type' => 'array', 'items' => ['type' => 'object']],
            'style' => ['type' => 'object', 'properties' => [
                'width' => ['type' => 'string'],
                'padding' => ['type' => 'string'],
                'margin' => ['type' => 'string']
            ]]
        ]);

        if (is_callable($labelOrCallback)) {
            $builder = $this->new();
            $labelOrCallback($builder);
            $property->addAttribute('properties', array_merge(
                $property->getAttribute('properties', []),
                $builder->toArray()
            ));
        }

        return $property;
    }
}
