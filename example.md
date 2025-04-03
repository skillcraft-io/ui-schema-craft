# UI Schema Craft Examples

This document provides practical examples of using UI Schema Craft in your Laravel application. These examples demonstrate various components and their implementations.

## Table of Contents
1. [Composable Form Schema](#composable-form-schema)
2. [Form Input Components](#form-input-components)
3. [Settings Panel Schema](#settings-panel-schema)
4. [Analytics Card Schema](#analytics-card-schema)
5. [Component Composition Example](#component-composition-example)

## Composable Form Schema

This example shows how to create a composable contact form with validation using UI Schema Craft.

```php
<?php

namespace App\UiSchemas;

use Skillcraft\UiSchemaCraft\Abstracts\UIComponentSchema;
use Skillcraft\UiSchemaCraft\Composition\ComposableInterface;
use Skillcraft\UiSchemaCraft\Composition\ComposableTrait;
use Skillcraft\UiSchemaCraft\Facades\PropertyBuilder;

class ContactFormSchema extends UIComponentSchema implements ComposableInterface
{
    use ComposableTrait;
    
    protected string $type = 'contact-form';
    protected string $component = 'form-component';

    public function properties(): array
    {
        $builder = PropertyBuilder::new();
        
        $builder->string('title', 'Form Title')
            ->default('Contact Form')
            ->rules(['required', 'max:100'])
            ->example('Contact Us');
            
        $builder->string('description', 'Form Description')
            ->default('A form for contacting us with inquiries')
            ->rules(['nullable', 'max:250'])
            ->example('Use this form to send us a message');
            
        $builder->string('submitText', 'Submit Button Text')
            ->default('Send Message')
            ->rules(['required'])
            ->example('Submit');
            
        $builder->string('successMessage', 'Success Confirmation Message')
            ->default('Thank you for your message!')
            ->rules(['required'])
            ->example('Your message has been sent successfully!');
        
        // Get properties array with children included
        $props = $builder->toArray();
        $props['children'] = $this->getChildrenSchema();
        
        return $props;
    }
    
    protected function getValidationSchema(): ValidationSchema
    {
        // Validation for the form itself
        // Child components will have their own validation
        return ValidationSchema::make()
            ->required('email')
            ->required('message');
    }
}
```

## Form Input Components

Here are examples of input components that can be used within the composable form:

```php
<?php

namespace App\UiSchemas;

use Skillcraft\UiSchemaCraft\Abstracts\UIComponentSchema;
use Skillcraft\UiSchemaCraft\Facades\PropertyBuilder;

class EmailInputSchema extends UIComponentSchema
{
    protected string $type = 'email-input';
    protected string $component = 'input-component';
    
    public function properties(): array
    {
        $builder = PropertyBuilder::new();
        
        $builder->string('title', 'Component Title')
            ->default('Email Input')
            ->rules(['required'])
            ->example('Email Address');
            
        $builder->string('description', 'Component Description')
            ->default('An input field for email addresses')
            ->rules(['nullable'])
            ->example('Enter your email address below');
            
        $builder->string('type', 'Input Type')
            ->default('email')
            ->rules(['required', 'in:email,text,tel'])
            ->example('email');
            
        $builder->string('label', 'Field Label')
            ->default('Email Address')
            ->rules(['required'])
            ->example('Your Email');
            
        $builder->string('placeholder', 'Placeholder Text')
            ->default('Enter your email address')
            ->rules(['nullable'])
            ->example('name@example.com');
            
        $builder->boolean('required', 'Is Required')
            ->default(true)
            ->rules(['boolean'])
            ->example(true);
        
        return $builder->toArray();
    }
    
    protected function getValidationSchema(): ValidationSchema
    {
        return ValidationSchema::make()
            ->string('value')
            ->email()
            ->required()
            ->message('Please enter a valid email address');
    }
}

class MessageInputSchema extends UIComponentSchema
{
    protected string $type = 'message-input';
    protected string $component = 'textarea-component';
    
    public function properties(): array
    {
        $builder = PropertyBuilder::new();
        
        $builder->string('title', 'Component Title')
            ->default('Message Input')
            ->rules(['required'])
            ->example('Your Message');
            
        $builder->string('description', 'Component Description')
            ->default('A textarea for your message')
            ->rules(['nullable'])
            ->example('Please tell us how we can help you');
            
        $builder->string('type', 'Input Type')
            ->default('textarea')
            ->rules(['required', 'in:textarea,text'])
            ->example('textarea');
            
        $builder->string('label', 'Field Label')
            ->default('Your Message')
            ->rules(['required'])
            ->example('Message Details');
            
        $builder->string('placeholder', 'Placeholder Text')
            ->default('Type your message here...')
            ->rules(['nullable'])
            ->example('Enter your question or feedback...');
            
        $builder->integer('rows', 'Number of Rows')
            ->default(5)
            ->rules(['integer', 'min:2', 'max:20'])
            ->example(8);
            
        $builder->boolean('required', 'Is Required')
            ->default(true)
            ->rules(['boolean'])
            ->example(true);
        
        return $builder->toArray();
    }
    
    protected function getValidationSchema(): ValidationSchema
    {
        return ValidationSchema::make()
            ->string('value')
            ->min(10)
            ->max(1000)
            ->required()
            ->message('Your message must be between 10 and 1000 characters');
    }
}
```

## Settings Panel Schema

This example demonstrates how to create a settings panel with toggles and selections.

```php
<?php

namespace App\UiSchemas;

use Skillcraft\UiSchemaCraft\Abstracts\UIComponentSchema;
use Skillcraft\UiSchemaCraft\Composition\ComposableInterface;
use Skillcraft\UiSchemaCraft\Composition\ComposableTrait;
use Skillcraft\UiSchemaCraft\Facades\PropertyBuilder;

class SettingsPanelSchema extends UIComponentSchema implements ComposableInterface
{
    use ComposableTrait;
    
    protected string $type = 'settings-panel';
    protected string $component = 'panel-component';

    public function properties(): array
    {
        $builder = PropertyBuilder::new();
        
        $builder->string('title', 'Settings Panel Title')
            ->default('Application Settings')
            ->rules(['required', 'max:100'])
            ->example('User Preferences');
            
        $builder->string('description', 'Settings Panel Description')
            ->default('Configure your application preferences')
            ->rules(['nullable', 'max:250'])
            ->example('Manage your account settings and preferences');
            
        $builder->string('saveButtonText', 'Save Button Text')
            ->default('Save Settings')
            ->rules(['required'])
            ->example('Update Settings');
            
        $builder->string('resetButtonText', 'Reset Button Text')
            ->default('Reset to Defaults')
            ->rules(['required'])
            ->example('Restore Defaults');
            
        $builder->boolean('showCancelButton', 'Show Cancel Button')
            ->default(true)
            ->rules(['boolean'])
            ->example(true);
            
        $builder->string('cancelButtonText', 'Cancel Button Text')
            ->default('Cancel')
            ->rules(['required_if:showCancelButton,true'])
            ->example('Go Back');
        
        // Get properties array
        $props = $builder->toArray();
        
        // Include children settings sections
        $props['children'] = $this->getChildrenSchema();
        
        return $props;
    }
    
    protected function getValidationSchema(): ValidationSchema
    {
        // Base validation requirements for settings
        return ValidationSchema::make()
            ->required(['theme', 'language']);
    }
}
```

## Analytics Card Schema
This example shows how to create an analytics card with date range and metrics.

```php
<?php

namespace App\UiSchemas;

use Skillcraft\UiSchemaCraft\Abstracts\UIComponentSchema;
use Skillcraft\UiSchemaCraft\Facades\PropertyBuilder;

class AnalyticsCardSchema extends UIComponentSchema
{
    protected string $type = 'analytics-card';
    protected string $component = 'chart-component';

    public function properties(): array
    {
        $builder = PropertyBuilder::new();
        
        $builder->string('title', 'Analytics Card Title')
            ->default('Performance Metrics')
            ->rules(['required', 'max:100'])
            ->example('Monthly Performance');
            
        $builder->string('description', 'Card Description')
            ->default('Key performance indicators for your application')
            ->rules(['nullable', 'max:250'])
            ->example('Overview of this month\'s performance metrics');
            
        $builder->string('chartType', 'Chart Visualization Type')
            ->default('line')
            ->rules(['required', 'in:line,bar,area,pie'])
            ->example('line');
            
        $builder->boolean('refresh', 'Auto-refresh Chart')
            ->default(true)
            ->rules(['boolean'])
            ->example(true);
            
        $builder->integer('refreshInterval', 'Refresh Interval in MS')
            ->default(5000)
            ->rules(['integer', 'min:1000', 'max:60000'])
            ->example(5000);
            
        $builder->object('timeRange', 'Date Range for Analytics')
            ->properties([
                'start' => [
                    'type' => 'string',
                    'format' => 'date',
                    'description' => 'Start date for analytics period'
                ],
                'end' => [
                    'type' => 'string',
                    'format' => 'date',
                    'description' => 'End date for analytics period'
                ]
            ])
            ->rules(['required'])
            ->default([
                'start' => '2025-01-01',
                'end' => '2025-01-05'
            ])
            ->example([
                'start' => '2025-01-01',
                'end' => '2025-01-31'
            ]);
            
        $builder->object('metrics', 'Performance Metrics')
            ->properties([
                'visitors' => [
                    'type' => 'integer',
                    'description' => 'Number of unique visitors'
                ],
                'conversion' => [
                    'type' => 'number',
                    'description' => 'Conversion rate percentage'
                ],
                'avgSessionTime' => [
                    'type' => 'integer',
                    'description' => 'Average session time in seconds'
                ]
            ])
            ->rules(['required'])
            ->default([
                'visitors' => 1500,
                'conversion' => 2.5,
                'avgSessionTime' => 125
            ])
            ->example([
                'visitors' => 2500,
                'conversion' => 3.8,
                'avgSessionTime' => 142
            ]);
        
        return $builder->toArray();
    }
    
    protected function getValidationSchema(): ValidationSchema
    {
        return ValidationSchema::make()
            ->object('timeRange', function($schema) {
                $schema->required('start')
                    ->date('start')
                    ->required('end')
                    ->date('end')
                    ->custom('end', function($value, $data) {
                        return $value > $data['start'] ? true : 'End date must be after start date';
                    });
            })
            ->object('metrics', function($schema) {
                $schema->integer('visitors')
                    ->min('visitors', 0)
                    ->number('conversion')
                    ->between('conversion', 0, 100)
                    ->number('avgSessionTime')
                    ->min('avgSessionTime', 0);
            });
    }
}
```

## Component Composition Example

This example demonstrates how to compose components together to build a complete UI:

```php
<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Skillcraft\UiSchemaCraft\Facades\UiSchema;
use App\UiSchemas\ContactFormSchema;
use App\UiSchemas\EmailInputSchema;
use App\UiSchemas\MessageInputSchema;

class ContactController extends Controller
{
    /**
     * Get the contact form schema with all components
     */
    public function getContactForm()
    {
        // Create state for the form
        $stateId = UiSchema::saveState('contact-form', [
            'email' => '',
            'message' => ''
        ]);
        
        // Create parent form component
        $form = UiSchema::createComponent('contact-form', $stateId, ContactFormSchema::class);
        
        // Create child components
        $emailInput = UiSchema::createComponent('email-input', $stateId, EmailInputSchema::class);
        $messageInput = UiSchema::createComponent('message-input', $stateId, MessageInputSchema::class);
        
        // Add children to form
        $form->addChild($emailInput, 'email');
        $form->addChild($messageInput, 'message');
        
        // Return complete schema for frontend rendering
        return response()->json([
            'schema' => $form->toArray(),
            'stateId' => $stateId
        ]);
    }
    
    /**
     * Handle form submission with validation
     */
    public function submit(Request $request)
    {
        $stateId = $request->input('stateId');
        $formData = $request->input('data');
        
        // Get form with all children
        $form = UiSchema::getComponent('contact-form', $stateId);
        
        // Validate submission against all component validation schemas
        $validationResult = $form->validate($formData);
        
        if (!$validationResult['valid']) {
            return response()->json([
                'success' => false,
                'errors' => $validationResult['errors']
            ], 422);
        }
        
        // Process form data...
        // ...
        
        // Update state with new data
        UiSchema::saveState('contact-form', $formData, $stateId);
        
        return response()->json([
            'success' => true,
            'message' => 'Thank you for your message!'
        ]);
    }
}
```