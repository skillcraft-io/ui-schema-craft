# UI Schema Craft

A powerful UI component schema generator for Laravel applications.

## 🌟 Features

- Define UI components using a fluent schema builder
- Generate consistent UI structures across your application
- Support for dynamic data loading
- Example data generation for development
- Type-safe property definitions
- Component-based architecture

## 📦 Installation

You can install the package via composer:

```bash
composer require skillcraft-io/ui-schema-craft
```

## 🚀 Quick Start

### 1. Define Your UI Component Schema

Create a new schema by extending `UIComponentSchema`:

```php
namespace App\Schemas;

use UiSchemaCraft\Abstracts\UIComponentSchema;
use UiSchemaCraft\Facades\PropertyBuilder;

class UserProfileSchema extends UIComponentSchema
{
    // Define the unique component identifier
    protected string $component = 'user-profile-form';
    
    // Define the component type (optional, defaults to 'component')
    protected string $type = 'form';
    
    // Define example data for development
    protected array $example = [
        'firstName' => 'John',
        'lastName' => 'Doe',
        'email' => 'john@example.com',
        'country' => 'us'
    ];
    
    // Define the UI component properties
    protected function properties(): array
    {
        return [
            PropertyBuilder::text('firstName')
                ->label('First Name')
                ->placeholder('Enter your first name')
                ->required()
                ->minLength(2)
                ->maxLength(50)
                ->toArray(),
                
            PropertyBuilder::text('lastName')
                ->label('Last Name')
                ->placeholder('Enter your last name')
                ->required()
                ->minLength(2)
                ->maxLength(50)
                ->toArray(),
                
            PropertyBuilder::email('email')
                ->label('Email Address')
                ->placeholder('Enter your email')
                ->required()
                ->toArray(),
                
            PropertyBuilder::select('country')
                ->label('Country')
                ->options([
                    'us' => 'United States',
                    'ca' => 'Canada',
                    'uk' => 'United Kingdom'
                ])
                ->defaultValue('us')
                ->toArray(),
        ];
    }
    
    // Define how to fetch live data for this component
    public function getLiveData(): array
    {
        // Fetch from your data source
        $user = auth()->user();
        
        return [
            'firstName' => $user->first_name,
            'lastName' => $user->last_name,
            'email' => $user->email,
            'country' => $user->country
        ];
    }
}
```

### 2. Register Your Schema

Register your schema through the HookFlow system in your service provider:

```php
use Skillcraft\HookFlow\Facades\Hook;
use App\Schemas\UserProfileSchema;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Hook::execute(AddUiComponentHook::class, [
            'value' => null,
            'schema' => [
                UserProfileSchema::class
            ]
        ]);
    }
}
```

### 3. Use in Your Frontend

#### Backend Controller
```php
use UiSchemaCraft\Services\UiSchemaCraftService;
use Inertia\Inertia;

class ComponentController extends Controller 
{
    public function getUserProfile(UiSchemaCraftService $service) 
    {
        // Get the schema including live data
        $schema = $service->getSchema('user-profile-form');
        
        return Inertia::render('UserProfile/Form', [
            'schema' => $schema
        ]);
    }
}
```

#### Vue.js Implementation
```vue
<template>
  <form @submit.prevent="submit" class="space-y-6">
    <template v-for="(field, name) in schema.props.config.properties" :key="name">
      <!-- Text & Email Input -->
      <div v-if="['text', 'email'].includes(field.type)" class="form-group">
        <label :for="name" class="block text-sm font-medium text-gray-700">
          {{ field.label }}
        </label>
        <input
          :id="name"
          :type="field.type"
          :name="name"
          v-model="form[name]"
          :placeholder="field.placeholder"
          :required="field.required"
          :minlength="field.minLength"
          :maxlength="field.maxLength"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
          :class="{ 'border-red-500': form.errors[name] }"
        />
        <p v-if="form.errors[name]" class="mt-1 text-sm text-red-600">
          {{ form.errors[name] }}
        </p>
      </div>

      <!-- Select Input -->
      <div v-else-if="field.type === 'select'" class="form-group">
        <label :for="name" class="block text-sm font-medium text-gray-700">
          {{ field.label }}
        </label>
        <select
          :id="name"
          :name="name"
          v-model="form[name]"
          :required="field.required"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
          :class="{ 'border-red-500': form.errors[name] }"
        >
          <option v-for="(label, value) in field.options" :key="value" :value="value">
            {{ label }}
          </option>
        </select>
        <p v-if="form.errors[name]" class="mt-1 text-sm text-red-600">
          {{ form.errors[name] }}
        </p>
      </div>
    </template>

    <div class="flex justify-end">
      <button
        type="submit"
        class="inline-flex justify-center rounded-md border border-transparent bg-primary-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
        :disabled="form.processing"
      >
        Save Changes
      </button>
    </div>
  </form>
</template>

<script>
import { useForm } from '@inertiajs/vue3'

export default {
  props: {
    schema: {
      type: Object,
      required: true
    }
  },

  setup(props) {
    // Initialize form with live data or example data
    const form = useForm({
      ...props.schema.data || props.schema.example
    })

    const submit = () => {
      form.post(route('user-profile.update'))
    }

    return {
      form,
      submit
    }
  }
}
</script>
```

## ⚙️ Configuration

After installation, publish the configuration file:

```bash
php artisan vendor:publish --provider="Skillcraft\UiSchemaCraft\UiSchemaCraftServiceProvider" --tag="config"
```

This will create `config/ui-schema-craft.php` with these options:

```php
return [
    // Register schemas that should be available by default
    'schemas' => [
        // Example:
        // App\Schemas\ButtonSchema::class,
        // App\Schemas\InputSchema::class,
    ],

    // Enable example schemas in local environment
    'enable_examples' => env('UI_SCHEMA_CRAFT_ENABLE_EXAMPLES', true),
    
    // Dump registered schemas for debugging
    'dd_examples' => env('UI_SCHEMA_CRAFT_DD_EXAMPLES', true),
];
```

### Registering Schemas

You have two ways to register schemas:

1. Via configuration (recommended for app-wide schemas):
```php
// config/ui-schema-craft.php
return [
    'schemas' => [
        App\Schemas\ButtonSchema::class,
        App\Schemas\InputSchema::class,
    ],
];
```

2. Via HookFlow (recommended for dynamic/plugin schemas):
```php
use Skillcraft\HookFlow\Facades\Hook;
use App\Schemas\CustomSchema;

Hook::execute(AddUiComponentHook::class, [
    'schema' => CustomSchema::class,
]);
```

### Debugging Registered Schemas

To view all registered schemas:

1. Set in your `.env`:
```
UI_SCHEMA_CRAFT_DD_EXAMPLES=true
```

2. Or use the service:
```php
use Skillcraft\UiSchemaCraft\Services\UiSchemaCraftService;

$service = app(UiSchemaCraftService::class);
dd($service->getAllSchemas());
```

## 🧰 Available Property Types

### 📝 Basic Types
- `string()` - Text input 📄
- `number()` - Numeric input 🔢
- `boolean()` - True/false toggle ✅
- `array()` - List of items 📋
- `object()` - Nested properties 🌳

### 🎯 Selection Fields
- `treeSelect()` - Hierarchical selection 🌲
- `multiSelect()` - Multiple item selection ✨
- `combobox()` - Combo box with search 🔍

### 📚 Rich Content
- `markdown()` - Markdown editor ✍️
- `codeEditor()` - Code editor 👨‍💻
- `jsonEditor()` - JSON editor 🔧

### 🖼️ Media
- `imageUpload()` - Image upload with preview 🖼️
- `mediaGallery()` - Media gallery manager 🎬
- `avatar()` - Profile picture upload 👤

### 📊 Analytics
- `timeRange()` - Date/time range picker 📅
- `duration()` - Duration input ⏱️
- `currency()` - Currency input 💰
- `percentage()` - Percentage input 📈

### 📋 Forms
- `dynamicForm()` - Dynamic form builder 🔄
- `wizard()` - Multi-step form 🧙‍♂️
- `matrix()` - Grid/matrix input 📊

### 🔒 Security
- `mfa()` - Multi-factor authentication 🔐
- `otp()` - One-time password 🔑
- `captcha()` - CAPTCHA verification 🤖

## 🛠️ Property Configuration

Properties can be configured using a fluent interface:

```php
PropertyBuilder::string('field_name')
    ->description('Field description')    // Add description 📝
    ->required()                         // Make field required ❗
    ->nullable()                         // Allow null values 🆕
    ->default('default value')           // Set default value ⭐
    ->enum(['option1', 'option2'])       // Set allowed values 📋
```

For object properties, you can nest other properties:

```php
PropertyBuilder::object('settings')
    ->description('User settings')
    ->properties([
        PropertyBuilder::boolean('notifications')->default(true),
        PropertyBuilder::string('theme')->enum(['light', 'dark']),
    ])
    ->required()
```

## 📚 Example Schemas

The package includes several example schemas that demonstrate best practices and common patterns:

- 📊 `AnalyticsDashboardSchema` - Analytics dashboard configuration
- 📝 `BlogPostSchema` - Blog post editor
- 🛍️ `ProductConfigurationSchema` - Product management
- 👤 `UserProfileSchema` - User profile settings

## 🎨 Using Preset Components

UI Schema Craft comes with pre-built schema definitions for common UI components. These presets include styling, variants, and common properties out of the box.

### Button Component

```php
use Skillcraft\UiSchemaCraft\Schema\PresetSchema;
use Skillcraft\UiSchemaCraft\Abstracts\UIComponentSchema;

class ButtonSchema extends UIComponentSchema
{
    protected string $component = 'primary-button';
    
    protected function properties(): array
    {
        return [
            PresetSchema::button('submit')
                ->withDefaults([
                    'text' => 'Save Changes',
                    'type' => 'submit',
                    'variant' => 'primary',
                    'size' => 'lg',
                    'iconRight' => 'heroicon-o-arrow-right'
                ])
                ->toArray()
        ];
    }
    
    public function getLiveData(): array
    {
        return [];
    }
}
```

This will generate a schema with:
- Button text and type
- Size variants (sm, md, lg)
- Style variants (primary, secondary, outline, text)
- Icon support (before/after text)
- Tailwind CSS classes for styling
- Interactive states (hover, focus, disabled)

### Form Input Component

```php
class InputSchema extends UIComponentSchema
{
    protected string $component = 'text-input';
    
    protected function properties(): array
    {
        return [
            PresetSchema::input('email')
                ->withDefaults([
                    'type' => 'email',
                    'placeholder' => 'Enter your email',
                    'required' => true,
                    'pattern' => '[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$'
                ])
                ->toArray()
        ];
    }
    
    public function getLiveData(): array
    {
        return [
            'email' => auth()->user()->email ?? ''
        ];
    }
}
```

The input preset includes:
- Common input types (text, email, password, etc.)
- Validation attributes (required, pattern, min, max)
- Placeholder text
- Disabled state
- Tailwind styling and states

### Using in Vue.js

```vue
<template>
  <div>
    <!-- Button Component -->
    <button
      :type="schema.props.config.properties.submit.type"
      :disabled="schema.props.config.properties.submit.disabled"
      :class="[
        // Base classes
        schema.props.config.properties.submit.container.background,
        schema.props.config.properties.submit.container.rounded,
        schema.props.config.properties.submit.spacing.padding,
        // Typography
        schema.props.config.properties.submit.text.color,
        schema.props.config.properties.submit.text.weight,
        // Interactive states
        schema.props.config.properties.submit.states.hover,
        schema.props.config.properties.submit.states.focus,
        schema.props.config.properties.submit.disabled ? schema.props.config.properties.submit.states.disabled : ''
      ]"
    >
      <i v-if="schema.props.config.properties.submit.iconLeft" 
         :class="schema.props.config.properties.submit.iconLeft" 
         class="mr-2" />
      {{ schema.props.config.properties.submit.text }}
      <i v-if="schema.props.config.properties.submit.iconRight" 
         :class="schema.props.config.properties.submit.iconRight" 
         class="ml-2" />
    </button>

    <!-- Input Component -->
    <input
      :type="schema.props.config.properties.email.type"
      :placeholder="schema.props.config.properties.email.placeholder"
      :required="schema.props.config.properties.email.required"
      :pattern="schema.props.config.properties.email.pattern"
      :disabled="schema.props.config.properties.email.disabled"
      v-model="form.email"
      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
    />
  </div>
</template>

<script>
import { useForm } from '@inertiajs/vue3'

export default {
  props: {
    schema: {
      type: Object,
      required: true
    }
  },

  setup(props) {
    const form = useForm({
      email: props.schema.data?.email || ''
    })

    return { form }
  }
}
</script>
```

### Available Presets

UI Schema Craft includes these preset components:
- `PresetSchema::button()` - Buttons with variants and icons
- `PresetSchema::input()` - Form inputs with validation
- `PresetSchema::card()` - Content cards with header/body/footer
- `PresetSchema::badge()` - Status badges and labels
- `PresetSchema::alert()` - Notification alerts
- `PresetSchema::modal()` - Modal dialogs

Each preset comes with:
- Sensible defaults
- Tailwind CSS styling
- Interactive states
- Accessibility attributes
- Common variants
- Icon support (where applicable)

## 🧪 Testing

```bash
composer test
```

## 🤝 Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## 🔒 Security

If you discover any security related issues, please email skillcraft.opensource@pm.me instead of using the issue tracker.

## 👥 Credits

- [William Troiano](https://williamtroiano.com)
- [All Contributors](../../contributors)

## 📄 License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
