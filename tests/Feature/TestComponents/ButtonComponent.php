<?php

namespace Skillcraft\UiSchemaCraft\Tests\Feature\TestComponents;

use Skillcraft\UiSchemaCraft\Abstracts\UIComponentSchema;
use Skillcraft\UiSchemaCraft\Facades\PropertyBuilder;
use Skillcraft\UiSchemaCraft\Traits\SimplifiedArraySerializationTrait;

/**
 * ButtonComponent Test Class
 *
 * A button component for UI Schema Craft testing.
 *
 * @package Skillcraft\UiSchemaCraft\Tests\Feature\TestComponents
 *
 * Expected JSON Output Format:
 * ```json
 * {
 *     "type": "button-component",
 *     "version": "1.0.0",
 *     "component": "button-component",
 *     "properties": {
 *         "title": {
 *             "type": "string",
 *             "nullable": false,
 *             "description": "Component Title",
 *             "default": "Button Component",
 *             "rules": ["required"],
 *             "example_data": "Submit Button",
 *             "required": true
 *         },
 *         "description": {
 *             "type": "string",
 *             "nullable": true,
 *             "description": "Component Description",
 *             "default": "A standard button component",
 *             "rules": ["nullable"],
 *             "example_data": "Used for form submission",
 *             "required": false
 *         },
 *         "text": {
 *             "type": "string",
 *             "nullable": false,
 *             "description": "Button Text",
 *             "default": "Submit",
 *             "rules": ["required"],
 *             "example_data": "Save",
 *             "required": true
 *         },
 *         "variant": {
 *             "type": "string",
 *             "nullable": false,
 *             "description": "Button Style Variant",
 *             "default": "primary",
 *             "rules": ["required", "in:primary,secondary,danger,success"],
 *             "example_data": "primary",
 *             "required": true
 *         },
 *         "enabled": {
 *             "type": "boolean",
 *             "nullable": false,
 *             "description": "Button Enabled State",
 *             "default": true,
 *             "rules": ["boolean"],
 *             "example_data": true,
 *             "required": false
 *         }
 *     }
 * }
 * ```
 */
class ButtonComponent extends UIComponentSchema
{
    use SimplifiedArraySerializationTrait;
    
    protected string $type = 'button-component';
    protected string $component = 'button-component';
    protected string $title = 'Button Component';
    protected string $description = 'A standard button component';
    
    public function properties(): array
    {
        $builder = PropertyBuilder::new();
        
        $builder->string('title', 'Component Title')
            ->default('Button Component')
            ->rules(['required'])
            ->example('Submit Button');
            
        $builder->string('description', 'Component Description')
            ->default('A standard button component')
            ->rules(['nullable'])
            ->example('Used for form submission');
            
        $builder->string('text', 'Button Text')
            ->default('Submit')
            ->rules(['required'])
            ->example('Save');
            
        $builder->string('variant', 'Button Style Variant')
            ->default('primary')
            ->rules(['required', 'in:primary,secondary,danger,success'])
            ->example('primary');
            
        $builder->boolean('enabled', 'Button Enabled State')
            ->default(true)
            ->rules(['boolean'])
            ->example(true);
            
        $builder->string('size', 'Button Size')
            ->default('medium')
            ->rules(['in:small,medium,large'])
            ->example('medium');
            
        $builder->string('icon', 'Optional Button Icon')
            ->default('')
            ->rules(['nullable'])
            ->example('save');
        
        return $builder->toArray();
    }
    
    /**
     * Override toArray for button-specific behavior
     *
     * @return array The component schema with property values
     */
    public function toArray(): array
    {
        // Get base schema with properties from trait
        $schema = parent::toArray();
        
        // Ensure button-specific properties are included
        $schema['text'] = $this->getProperty('text', 'Submit');
        $schema['variant'] = $this->getProperty('variant', 'primary');
        $schema['enabled'] = $this->getProperty('enabled', true);
        $schema['size'] = $this->getProperty('size', 'medium');
        $schema['icon'] = $this->getProperty('icon', '');
        
        return $schema;
    }
}
