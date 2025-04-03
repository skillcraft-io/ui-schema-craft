<?php

namespace Skillcraft\UiSchemaCraft\Tests\Feature\TestComponents;

use Skillcraft\UiSchemaCraft\Abstracts\UIComponentSchema;
use Skillcraft\UiSchemaCraft\Facades\PropertyBuilder;
use Skillcraft\UiSchemaCraft\Traits\SimplifiedArraySerializationTrait;


/**
 * FormInputComponent Test Class
 *
 * An input field component for form UI testing.
 * This component handles various input types and their associated properties.
 *
 * @package Skillcraft\UiSchemaCraft\Tests\Feature\TestComponents
 *
 * Expected Array Output Format:
 * ```php
 * [
 *     // Core component information
 *     'type' => 'form-input-component',
 *     'version' => '1.0.0',
 *     'component' => 'input-component',
 *     
 *     // Property schema definitions (for validation)
 *     'properties' => [
 *         'title' => [
 *             'type' => 'string',
 *             'nullable' => false,
 *             'description' => 'Component Title',
 *             'default' => 'Form Input Field',
 *             'rules' => ['required'],
 *             'example_data' => 'Name Field',
 *             'required' => true
 *         ],
 *         'description' => [
 *             'type' => 'string',
 *             'nullable' => true,
 *             'description' => 'Component Description',
 *             'default' => 'An input field for forms',
 *             'rules' => ['nullable'],
 *             'example_data' => 'Enter your full name',
 *             'required' => false
 *         ],
 *         'type' => [
 *             'type' => 'string',
 *             'nullable' => false,
 *             'description' => 'Input Type',
 *             'default' => 'text',
 *             'rules' => ['required', 'in:text,email,password,tel,number,date'],
 *             'example_data' => 'text',
 *             'required' => true
 *         ],
 *         'label' => [
 *             'type' => 'string',
 *             'nullable' => false,
 *             'description' => 'Field Label',
 *             'default' => 'Input',
 *             'rules' => ['required'],
 *             'example_data' => 'Full Name',
 *             'required' => true
 *         ],
 *         // Additional properties omitted for brevity
 *     ],
 *     
 *     // Direct property values with appropriate defaults
 *     'label' => 'Input',  // String value, not schema definition
 *     'placeholder' => 'Enter a value',
 *     'value' => '',
 *     'required' => false,
 *     'helpText' => '',
 *     'maxLength' => null,
 *     
 *     // Store input type as 'inputType' to avoid conflicts with component type
 *     'inputType' => 'text'
 * ]
 * ```
 */
class FormInputComponent extends UIComponentSchema
{
    use SimplifiedArraySerializationTrait;
    
    protected string $type = 'form-input-component';
    protected string $component = 'input-component';
    protected string $title = 'Form Input Field';
    protected string $description = 'An input field for forms';
    
    public function properties(): array
    {
        $builder = PropertyBuilder::new();
        
        $builder->string('title', 'Component Title')
            ->default('Form Input Field')
            ->rules(['required'])
            ->example('Name Field');
            
        $builder->string('description', 'Component Description')
            ->default('An input field for forms')
            ->rules(['nullable'])
            ->example('Enter your full name');
            
        $builder->string('type', 'Input Type')
            ->default('text')
            ->rules(['required', 'in:text,email,password,tel,number,date'])
            ->example('text');
            
        $builder->string('label', 'Field Label')
            ->default('Input')
            ->rules(['required'])
            ->example('Full Name');
            
        $builder->string('placeholder', 'Placeholder Text')
            ->default('Enter a value')
            ->rules(['nullable'])
            ->example('John Doe');
            
        $builder->string('value', 'Current Value')
            ->default('')
            ->rules(['nullable'])
            ->example('John Smith');
            
        $builder->boolean('required', 'Is Required')
            ->default(false)
            ->rules(['boolean'])
            ->example(true);
            
        $builder->string('helpText', 'Help Text')
            ->default('')
            ->rules(['nullable'])
            ->example('Enter your legal full name');
            
        $builder->string('maxLength', 'Maximum Input Length')
            ->default(null)
            ->rules(['nullable', 'integer', 'min:1'])
            ->example('100');
        
        return $builder->toArray();
    }
    
    /**
     * Override toArray to include input-specific property values
     * 
     * @return array
     */
    public function toArray(): array
    {
        // Get base schema with properties from trait
        $schema = parent::toArray();
        
        // Add direct property values with appropriate defaults
        $schema['label'] = $this->getProperty('label', 'Input');
        $schema['placeholder'] = $this->getProperty('placeholder', 'Enter a value');
        $schema['value'] = $this->getProperty('value', '');
        $schema['required'] = $this->getProperty('required', false);
        $schema['helpText'] = $this->getProperty('helpText', '');
        $schema['maxLength'] = $this->getProperty('maxLength', null);
        
        // Store input type as 'inputType' to avoid conflicts with component type
        $schema['inputType'] = $this->getProperty('type', 'text');
        
        return $schema;
    }
    
    /**
     * Get validation schema based on component properties
     */
    protected function getValidationSchema(): ?array
    {
        $rules = [];
        
        // Only add validation if the field is required
        if ($this->getProperty('required', false)) {
            $rules['value'][] = 'required';
        } else {
            $rules['value'][] = 'nullable';
        }
        
        // Add type-specific validation
        $type = $this->getProperty('type', 'text');
        
        switch ($type) {
            case 'email':
                $rules['value'][] = 'email';
                break;
                
            case 'number':
                $rules['value'][] = 'numeric';
                break;
                
            case 'date':
                $rules['value'][] = 'date';
                break;
                
            default:
                $rules['value'][] = 'string';
        }
        
        // Add max length validation if specified
        $maxLength = $this->getProperty('maxLength');
        if ($maxLength) {
            $rules['value'][] = 'max:' . $maxLength;
        }
        
        return $rules;
    }
}
