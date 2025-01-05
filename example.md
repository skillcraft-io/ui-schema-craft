# UI Schema Craft Examples

This document provides practical examples of using UI Schema Craft in your Laravel application. These examples demonstrate various components and their implementations.

## Table of Contents
1. [Contact Form Schema](#contact-form-schema)
2. [Settings Panel Schema](#settings-panel-schema)
3. [Analytics Card Schema](#analytics-card-schema)

## Contact Form Schema

This example shows how to create a contact form with validation using UI Schema Craft.

```php
<?php

namespace App\Schemas;

use Skillcraft\UiSchemaCraft\Abstracts\UIComponentSchema;
use Skillcraft\UiSchemaCraft\Schema\PropertyBuilder;

class ContactFormSchema extends UIComponentSchema
{
    protected string $type = 'contact-form';
    protected string $component = 'form-component';

    public function getExampleData(): array
    {
        return [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'General Inquiry',
            'message' => 'Hello, I would like to know more about...'
        ];
    }

    public function properties(): array
    {
        return [
            PropertyBuilder::new()
                ->validate('name', ['required', 'string', 'max:100'])
                ->default('') // Empty string default
                ->validate('email', ['required', 'email'])
                ->default('') // Empty string default
                ->validate('subject', ['required', 'string', 'max:200'])
                ->default('General Inquiry') // Common default subject
                ->validate('message', ['required', 'string', 'max:1000'])
                ->default('') // Empty string default
                ->toArray()
        ];
    }
}
```

## Settings Panel Schema

This example demonstrates how to create a settings panel with toggles and selections.

```php
<?php

namespace App\Schemas;

use Skillcraft\UiSchemaCraft\Abstracts\UIComponentSchema;
use Skillcraft\UiSchemaCraft\Schema\PropertyBuilder;

class SettingsPanelSchema extends UIComponentSchema
{
    protected string $type = 'settings-panel';
    protected string $component = 'panel-component';

    public function getExampleData(): array
    {
        return [
            'notifications' => true,
            'theme' => 'dark',
            'language' => 'en',
            'autoSave' => true
        ];
    }

    public function properties(): array
    {
        $builder = PropertyBuilder::new();
        
        return [
            $builder->boolean('notifications', 'Enable Notifications')
                ->enum('theme', ['light', 'dark', 'system'])
                ->enum('language', ['en', 'es', 'fr', 'de'])
                ->boolean('autoSave', 'Enable Auto-Save')
                ->toArray()
        ];
    }
}
```

## Analytics Card Schema
This example shows how to create an analytics card with date range and metrics.

```php
<?php

namespace App\Schemas;

use Skillcraft\UiSchemaCraft\Abstracts\UIComponentSchema;
use Skillcraft\UiSchemaCraft\Schema\PropertyBuilder;

class AnalyticsCardSchema extends UIComponentSchema
{
    protected string $type = 'analytics-card';
    protected string $component = 'card-component';

    public function getExampleData(): array
    {
        return [
            'timeRange' => [
                'start' => '2025-01-01',
                'end' => '2025-01-05'
            ],
            'metrics' => [
                'visitors' => 1500,
                'conversion' => 2.5
            ]
        ];
    }

    public function properties(): array
    {
        return [
            PropertyBuilder::new()
                ->object('timeRange', [
                    'start' => ['required', 'date'],
                    'end' => ['required', 'date', 'after:start']
                ])
                ->object('metrics', [
                    'visitors' => ['required', 'integer'],
                    'conversion' => ['required', 'numeric', 'between:0,100']
                ])
                ->toArray()
        ];
    }
}
```