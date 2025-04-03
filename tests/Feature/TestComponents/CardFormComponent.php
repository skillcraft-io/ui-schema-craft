<?php

namespace Skillcraft\UiSchemaCraft\Tests\Feature\TestComponents;

use Skillcraft\SchemaValidation\Interfaces\ValidatorInterface;
use Skillcraft\UiSchemaCraft\Abstracts\UIComponentSchema;
use Skillcraft\UiSchemaCraft\Composition\ComposableInterface;
use Skillcraft\UiSchemaCraft\Facades\PropertyBuilder;
use Skillcraft\UiSchemaCraft\Traits\SimplifiedArraySerializationTrait;

/**
 * CardFormComponent Test Class
 *
 * A card container component that holds a form schema compatible with Vueform.
 * This component demonstrates the integration between UI Schema Craft and Vueform,
 * showing how complex nested forms can be represented in a schema.
 *
 * @package Skillcraft\UiSchemaCraft\Tests\Feature\TestComponents
 *
 * Expected Array Output Format:
 * ```php
 * [
 *     // Core component information
 *     'type' => 'card-form-component',
 *     'version' => '1.0.0',
 *     'component' => 'card-component',
 *     
 *     // Property schema definitions
 *     'properties' => [
 *         'title' => [
 *             'type' => 'string',
 *             'nullable' => false,
 *             'description' => 'Card Title',
 *             'default' => 'Form Card',
 *             'rules' => ['required'],
 *             'example_data' => 'Contact Information',
 *             'required' => true
 *         ],
 *         'subtitle' => [
 *             'type' => 'string',
 *             'nullable' => true,
 *             'description' => 'Card Subtitle',
 *             'default' => '',
 *             'rules' => ['nullable'],
 *             'example_data' => 'Please fill out all fields',
 *             'required' => false
 *         ],
 *         // Other card properties...
 *     ],
 *     
 *     // Vueform compatible schema
 *     'formSchema' => [
 *         'fields' => [
 *             'name' => [
 *                 'type' => 'text',
 *                 'label' => 'Full Name',
 *                 'placeholder' => 'Enter your full name',
 *                 'rules' => 'required',
 *                 'default' => ''
 *             ],
 *             'email' => [
 *                 'type' => 'email',
 *                 'label' => 'Email Address',
 *                 'placeholder' => 'your@email.com',
 *                 'rules' => 'required|email',
 *                 'default' => ''
 *             ],
 *             // Additional form fields...
 *         ],
 *         'buttons' => [
 *             'submit' => [
 *                 'label' => 'Submit',
 *                 'color' => 'primary'
 *             ],
 *             'reset' => [
 *                 'label' => 'Reset',
 *                 'color' => 'secondary'
 *             ]
 *         ]
 *     ],
 *     
 *     // Direct property values
 *     'cardVariant' => 'default',
 *     'bordered' => true,
 *     'shadow' => true,
 *     'padding' => 'normal'
 * ]
 * ```
 */
class CardFormComponent extends UIComponentSchema implements ComposableInterface
{
    use SimplifiedArraySerializationTrait;
    
    /**
     * Child components organized by slots
     *
     * @var array<string|null, array<UIComponentSchema>>
     */
    protected array $children = [];
    
    protected string $type = 'card-form-component';
    protected string $component = 'card-component';
    protected string $title = 'Form Card';
    protected string $description = 'A card containing a Vueform compatible form schema';
    
    /**
     * Properties that should not be included in the simplified output
     * Already defined in trait, but can be overridden here
     */
    protected array $hiddenProperties = [
        'validator',
    ];
    
    /**
     * Define the component properties schema
     *
     * @return array The component properties schema
     */
    public function properties(): array
    {
        $builder = PropertyBuilder::new();
        
        $builder->string('name', 'Form Name')
            ->default('vueform')
            ->rules(['required'])
            ->example('contact-form');
            
        $builder->string('title', 'Card Title')
            ->default('Form Card')
            ->rules(['required'])
            ->example('Contact Form');
            
        $builder->string('subtitle', 'Card Subtitle')
            ->default('')
            ->rules(['nullable'])
            ->example('Please fill out all fields');
            
        $builder->string('cardVariant', 'Card Style Variant')
            ->default('default')
            ->rules(['in:default,primary,secondary,info,success,warning,danger'])
            ->example('default');
            
        $builder->boolean('bordered', 'Show Card Border')
            ->default(true)
            ->rules(['boolean'])
            ->example(true);
            
        $builder->boolean('shadow', 'Show Card Shadow')
            ->default(false)
            ->rules(['boolean'])
            ->example(false);
            
        $builder->string('padding', 'Card Padding')
            ->default('normal')
            ->rules(['in:none,small,normal,large'])
            ->example('normal');
        
        return $builder->toArray();
    }
    
    /**
     * Form field definitions for Vueform
     */
    protected array $formFields = [];
    
    /**
     * Form button configurations
     */
    protected array $formButtons = [
        'submit' => [
            'label' => 'Submit',
            'color' => 'primary'
        ],
        'reset' => [
            'label' => 'Reset',
            'color' => 'secondary'
        ]
    ];
    
    /**
     * Extend the schema with form-specific structure
     *
     * @param array $schema Base schema from trait
     * @return array Extended schema with form-specific data
     */
    protected function extendSchema(array $schema): array
    {
        // Add Vueform-specific name property required by the framework
        $schema['name'] = $this->getProperty('name', 'vueform');
        
        // Add form schema with fields and buttons
        $schema['formSchema'] = [
            'fields' => $this->formFields,
            'buttons' => $this->formButtons
        ];
        
        return $schema;
    }
    
    /**
     * Override to ensure children are added to the schema
     *
     * @param array $schema Current schema
     * @return array Updated schema with children
     */
    protected function addChildrenToSchema(array $schema): array
    {
        // Initialize children array
        $schema['children'] = [];
        
        // Add child components if this component has children
        if ($this->hasChildren()) {
            foreach ($this->getChildren() as $child) {
                $schema['children'][] = $child->toArray();
            }
        }
        
        return $schema;
    }
    
    /**
     * Configure the form schema with Vueform compatible fields and options
     *
     * @param array $fields Form fields in Vueform format
     * @param array $buttons Form buttons configuration
     * @return self For method chaining
     */
    public function configureForm(array $fields, array $buttons = []): self
    {
        $this->formFields = $fields;
        
        if (!empty($buttons)) {
            $this->formButtons = $buttons;
        }
        
        return $this;
    }
    
    /**
     * Add a single field to the form schema
     *
     * @param string $name Field name/key
     * @param array $config Field configuration in Vueform format
     * @return self For method chaining
     */
    public function addField(string $name, array $config): self
    {
        $this->formFields[$name] = $config;
        return $this;
    }
    
    /**
     * Add a child component
     *
     * @param UIComponentSchema $component
     * @param string|null $slot Optional slot name
     * @return ComposableInterface
     */
    public function addChild(UIComponentSchema $component, ?string $slot = null): ComposableInterface
    {
        // If no slot is provided, use a default slot
        $slotKey = $slot ?? 'default';
        
        // Initialize the slot if it doesn't exist
        if (!isset($this->children[$slotKey])) {
            $this->children[$slotKey] = [];
        }
        
        // Add the component to the specified slot
        $this->children[$slotKey][] = $component;
        
        return $this;
    }
    
    /**
     * Get child components
     *
     * @param string|null $slot Optional slot name to filter by
     * @return array<UIComponentSchema>
     */
    public function getChildren(?string $slot = null): array
    {
        if ($slot !== null) {
            return $this->children[$slot] ?? [];
        }
        
        // If no slot specified, return all children flattened
        $allChildren = [];
        foreach ($this->children as $slotChildren) {
            foreach ($slotChildren as $child) {
                $allChildren[] = $child;
            }
        }
        
        return $allChildren;
    }

    /**
     * Remove a child component
     *
     * @param UIComponentSchema $component
     * @return ComposableInterface
     */
    public function removeChild(UIComponentSchema $component): ComposableInterface
    {
        foreach ($this->children as $slot => $slotChildren) {
            foreach ($slotChildren as $index => $child) {
                if ($child === $component) {
                    unset($this->children[$slot][$index]);
                    // Reindex array
                    $this->children[$slot] = array_values($this->children[$slot]);
                    // If slot is now empty, remove it
                    if (empty($this->children[$slot])) {
                        unset($this->children[$slot]);
                    }
                    return $this;
                }
            }
        }
        
        return $this;
    }

    /**
     * Check if component has children
     *
     * @param string|null $slot Optional slot name to check
     * @return bool
     */
    public function hasChildren(?string $slot = null): bool
    {
        if ($slot !== null) {
            return isset($this->children[$slot]) && !empty($this->children[$slot]);
        }
        
        // Check if any slots have children
        foreach ($this->children as $slotChildren) {
            if (!empty($slotChildren)) {
                return true;
            }
        }
        
        return false;
    }
    

}
