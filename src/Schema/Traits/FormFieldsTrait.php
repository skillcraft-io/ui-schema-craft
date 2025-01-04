<?php

namespace Skillcraft\UiSchemaCraft\Schema\Traits;

use Skillcraft\UiSchemaCraft\Schema\Property;

trait FormFieldsTrait
{
    /**
     * Create a text field property.
     */
    public function textField(string $name, ?string $label = null, ?string $placeholder = null): Property
    {
        $property = Property::object($name)
            ->description($label ?? ucwords(str_replace('_', ' ', $name)))
            ->withBuilder(function ($builder) use ($placeholder) {
                $builder->string('value')
                    ->description('Text field value');

                if ($placeholder !== null) {
                    $builder->string('placeholder')
                        ->addAttribute('default', $placeholder)
                        ->description('Placeholder text');
                }
            });

        $this->properties[$name] = $property;
        return $property;
    }

    /**
     * Create an email field property.
     */
    public function email(string $name, ?string $label = null): Property
    {
        $property = new Property($name, 'string', $label ?? 'Email Address');
        $property->addAttribute('format', 'email');
        return $property;
    }

    /**
     * Create a password field property.
     */
    public function password(string $name, ?string $label = null): Property
    {
        $property = new Property($name, 'string', $label ?? 'Password');
        $property->addAttribute('format', 'password');
        return $property;
    }

    /**
     * Create a phone field property.
     */
    public function phone(string $name, ?string $label = null): Property
    {
        $property = new Property($name, 'string', $label ?? 'Contact Number');
        $property->addAttribute('format', 'phone');
        return $property;
    }

    /**
     * Create a URL field property.
     */
    public function url(string $name, ?string $label = null): Property
    {
        return $this->textField($name, $label)
            ->pattern('^https?:\/\/(?:www\.)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b(?:[-a-zA-Z0-9()@:%_\+.~#?&\/=]*)$')
            ->withBuilder(function ($builder) {
                $builder->boolean('requireHttps')
                    ->addAttribute('default', false)
                    ->description('Require HTTPS protocol');
            })
            ->description('URL field');
    }

    /**
     * Create a color picker field property.
     */
    public function color(string $name, ?string $label = null): Property
    {
        return $this->textField($name, $label)
            ->pattern('^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$')
            ->withBuilder(function ($builder) {
                $builder->string('format')
                    ->enum(['hex', 'rgb', 'hsl'])
                    ->addAttribute('default', 'hex')
                    ->description('Color format');

                $builder->boolean('alpha')
                    ->addAttribute('default', false)
                    ->description('Allow alpha channel');

                $builder->object('swatches')
                    ->properties([
                        'enabled' => ['type' => 'boolean', 'default' => true],
                        'colors' => ['type' => 'array', 'items' => ['type' => 'string']]
                    ])
                    ->description('Color swatches configuration');
            })
            ->description('Color picker field');
    }

    /**
     * Create a file upload field property.
     */
    public function file(string $name, ?string $label = null): Property
    {
        $property = new Property($name, 'object', $label);
        $property->addAttribute('properties', [
            'name' => ['type' => 'string'],
            'size' => ['type' => 'number'],
            'type' => ['type' => 'string'],
            'lastModified' => ['type' => 'string', 'format' => 'date-time'],
            'preview' => ['type' => 'string'],
            'progress' => ['type' => 'number', 'minimum' => 0, 'maximum' => 100]
        ]);
        return $property;
    }

    /**
     * Create a rich text editor field property.
     */
    public function richText(string $name, ?string $label = null): Property
    {
        return $this->object($name)
            ->description($label ?? ucwords(str_replace('_', ' ', $name)))
            ->withBuilder(function ($builder) {
                $builder->string('value')
                    ->description('Rich text content');

                $builder->object('toolbar')
                    ->properties([
                        'enabled' => ['type' => 'boolean', 'default' => true],
                        'items' => ['type' => 'array', 'items' => ['type' => 'string']],
                        'position' => ['type' => 'string', 'enum' => ['top', 'bottom'], 'default' => 'top']
                    ])
                    ->description('Toolbar configuration');

                $builder->object('plugins')
                    ->properties([
                        'enabled' => ['type' => 'boolean', 'default' => true],
                        'items' => ['type' => 'array', 'items' => ['type' => 'string']]
                    ])
                    ->description('Editor plugins configuration');

                $builder->object('options')
                    ->properties([
                        'height' => ['type' => 'string'],
                        'placeholder' => ['type' => 'string'],
                        'readonly' => ['type' => 'boolean', 'default' => false],
                        'autofocus' => ['type' => 'boolean', 'default' => false]
                    ])
                    ->description('Editor options');
            });
    }
}
