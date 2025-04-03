<?php

namespace Skillcraft\UiSchemaCraft\Tests\Feature\TestComponents;

use Skillcraft\UiSchemaCraft\Abstracts\UIComponentSchema;
use Skillcraft\UiSchemaCraft\Composition\ComposableInterface;
// Don't use ComposableTrait as it has incompatible signatures
use Skillcraft\UiSchemaCraft\Facades\PropertyBuilder;

/**
 * FormContainerComponent Test Class
 *
 * A container component that implements component composition to manage child form elements.
 * This component demonstrates the hierarchical structure of UI components, allowing
 * parent components to contain and manage children, with schema generation that properly
 * includes nested components.
 *
 * @package Skillcraft\UiSchemaCraft\Tests\Feature\TestComponents
 *
 * Expected Array Output Format (with children):
 * ```php
 * [
 *     // Core component information
 *     'type' => 'form-container-component',
 *     'version' => '1.0.0',
 *     'component' => 'form-component',
 *     
 *     // Property schema definitions (for validation)
 *     'properties' => [
 *         'title' => [
 *             'type' => 'string',
 *             'nullable' => false,
 *             'description' => 'Form Title',
 *             'default' => 'Form Container',
 *             'rules' => ['required'],
 *             'example_data' => 'Contact Form',
 *             'required' => true
 *         ],
 *         'description' => [
 *             'type' => 'string',
 *             'nullable' => true,
 *             'description' => 'Form Description',
 *             'default' => 'A container for form elements',
 *             'rules' => ['nullable'],
 *             'example_data' => 'Use this form to contact us',
 *             'required' => false
 *         ],
 *         // Other properties omitted for brevity
 *     ],
 *     
 *     // Child components array - demonstrates component composition
 *     'children' => [
 *         // Each child component follows its own schema structure
 *         [
 *             'type' => 'form-input-component',
 *             'version' => '1.0.0',
 *             'component' => 'input-component',
 *             'properties' => [...],
 *             'label' => 'Name',
 *             'placeholder' => 'Enter your name',
 *             'inputType' => 'text',
 *             // Other direct property values
 *         ],
 *         [
 *             'type' => 'form-input-component',
 *             'version' => '1.0.0',
 *             'component' => 'input-component',
 *             'properties' => [...],
 *             'label' => 'Email',
 *             'placeholder' => 'Enter your email',
 *             'inputType' => 'email',
 *             // Other direct property values
 *         ],
 *         // More child components can be added here
 *     ]
 * ]
 * ```
 */
class FormContainerComponent extends UIComponentSchema implements ComposableInterface
{
    /**
     * Child components organized by slots
     *
     * @var array<string|null, array<UIComponentSchema>>
     */
    protected array $children = [];
    
    protected string $type = 'form-container-component';
    protected string $component = 'form-component';
    protected string $title = 'Form Container';
    protected string $description = 'A container for form elements';
    
    public function properties(): array
    {
        $builder = PropertyBuilder::new();
        
        $builder->string('title', 'Form Title')
            ->default('Form Container')
            ->rules(['required'])
            ->example('Contact Form');
            
        $builder->string('description', 'Form Description')
            ->default('A container for form elements')
            ->rules(['nullable'])
            ->example('Use this form to contact us');
            
        $builder->string('submitText', 'Submit Button Text')
            ->default('Submit')
            ->rules(['required'])
            ->example('Send');
            
        $builder->boolean('showResetButton', 'Show Reset Button')
            ->default(true)
            ->rules(['boolean'])
            ->example(true);
            
        $builder->string('resetText', 'Reset Button Text')
            ->default('Reset')
            ->rules(['required_if:showResetButton,true'])
            ->example('Clear Form');
            
        $builder->string('action', 'Form Action URL')
            ->default('')
            ->rules(['nullable', 'url'])
            ->example('https://example.com/submit');
            
        $builder->string('method', 'Form Method')
            ->default('POST')
            ->rules(['in:GET,POST,PUT,DELETE'])
            ->example('POST');
        
        // Get properties array
        $props = $builder->toArray();
        
        // We'll add children directly to the schema in the toArray method instead
        // This ensures they appear at the root level rather than in properties
        
        return $props;
    }
    
    /**
     * Add a child component
     *
     * @param UIComponentSchema $component
     * @param string|null $slot Optional slot name
     * @return self
     */
    public function addChild(UIComponentSchema $component, ?string $slot = null): ComposableInterface
    {
        if (!isset($this->children[$slot])) {
            $this->children[$slot] = [];
        }
        
        $this->children[$slot][] = $component;
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
     * @return self
     */
    public function removeChild(UIComponentSchema $component): ComposableInterface
    {
        foreach ($this->children as $slot => $slotChildren) {
            foreach ($slotChildren as $key => $child) {
                if ($child === $component) {
                    unset($this->children[$slot][$key]);
                    // Reindex array
                    $this->children[$slot] = array_values($this->children[$slot]);
                    // If slot is empty, remove it
                    if (empty($this->children[$slot])) {
                        unset($this->children[$slot]);
                    }
                    break 2;
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
        
        return !empty($this->children);
    }
    
    /**
     * Override toArray to include children in the schema
     * 
     * @return array
     */
    public function toArray(): array
    {
        // Get base schema from parent
        $schema = parent::toArray();
        
        // Add children to schema directly (not in properties)
        if ($this->hasChildren()) {
            $schema['children'] = [];
            foreach ($this->getChildren() as $child) {
                // Get child schema
                $childSchema = $child->toArray();
                
                // Ensure the child has explicit property values, not schema objects
                // This is a fallback in case the child's toArray method doesn't properly handle this
                if ($child instanceof FormInputComponent) {
                    // Make sure key properties are simple values, not schema objects
                    if (!isset($childSchema['label']) || is_array($childSchema['label'])) {
                        $childSchema['label'] = $child->getProperty('label', 'Input');
                    }
                    
                    if (!isset($childSchema['inputType'])) {
                        $childSchema['inputType'] = $child->getProperty('type', 'text');
                    }
                }
                
                $schema['children'][] = $childSchema;
            }
        }
        
        return $schema;
    }
}
