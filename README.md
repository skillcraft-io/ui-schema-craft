# UI Schema Craft

A flexible and extensible UI schema system for Laravel applications.

## Overview

UI Schema Craft provides a robust foundation for building dynamic UI components with:
- Type-safe component definitions
- Flexible validation (via `validation-craft`)
- State management (via `state-craft`)
- Component discovery across packages
- Component composition and nesting

## Installation

```bash
composer require skillcraft/ui-schema-craft
```

This will also install the required packages:
- `skillcraft/validation-craft`: For validation
- `skillcraft/state-craft`: For state management

## Basic Usage

### 1. Create a Component Schema

```php
namespace App\UiSchemas;

use Skillcraft\UiSchemaCraft\Abstracts\UIComponentSchema;
use Skillcraft\ValidationCraft\ValidationSchema;

class TextInputSchema extends UIComponentSchema
{
    protected string $version = '1.0.0';
    
    public function properties(): array
    {
        return [
            'label' => 'Text Input',
            'placeholder' => 'Enter text...',
            'required' => false
        ];
    }

    protected function getValidationSchema(): ValidationSchema
    {
        return ValidationSchema::make()
            ->string('value')
            ->when('required', true, fn($schema) => $schema->required());
    }
}
```

### 2. Register Components

You can register components in several ways:

```php
// In a service provider:

// 1. Register a namespace
$uiSchema->registerNamespace('App\\UiSchemas');

// 2. Register individual components
$uiSchema->registerComponent(TextInputSchema::class);

// 3. Configure default namespace in config/ui-schema-craft.php
return [
    'components_namespace' => 'App\\UiSchemas'
];
```

### 3. Use Components

```php
use Skillcraft\UiSchemaCraft\Facades\UiSchema;

// Get component schema
$schema = UiSchema::getComponent('text-input');

// With state
$schema = UiSchema::getComponent('text-input', $stateId);

// Save state
$stateId = UiSchema::saveState('text-input', [
    'value' => 'Hello World'
]);
```

## Component Composition

UI Schema Craft supports component composition, allowing you to build complex UIs by nesting components:

### 1. Create a Composable Component

```php
namespace App\UiSchemas;

use Skillcraft\UiSchemaCraft\Abstracts\UIComponentSchema;
use Skillcraft\UiSchemaCraft\Composition\ComposableInterface;
use Skillcraft\UiSchemaCraft\Composition\ComposableTrait;

class FormSchema extends UIComponentSchema implements ComposableInterface
{
    use ComposableTrait;

    // You can define component metadata that will be included in the output schema
    protected string $title = 'Contact Form';
    protected string $description = 'A contact form with multiple inputs';
    
    public function properties(): array
    {
        // The properties() method should return the base properties for this component
        $props = [
            'submitText' => 'Send Message',
        ];
        
        // When your component has a title or description, include them in properties
        if (isset($this->title)) {
            $props['title'] = $this->title;
        }
        
        if (isset($this->description)) {
            $props['description'] = $this->description;
        }
        
        // For composable components, include children
        $props['children'] = $this->getChildrenSchema();
        
        return $props;
    }
}
```

### 2. Create Child Components

```php
namespace App\UiSchemas;

use Skillcraft\UiSchemaCraft\Abstracts\UIComponentSchema;
use Skillcraft\ValidationCraft\ValidationSchema;

class EmailInputSchema extends UIComponentSchema
{
    protected string $title = 'Email Input';
    protected string $description = 'An input field for email addresses';
    
    public function properties(): array
    {
        // Include component properties
        $props = [
            'type' => 'email',
            'label' => 'Email Address',
            'placeholder' => 'Enter your email'
        ];
        
        // Include metadata properties
        if (isset($this->title)) {
            $props['title'] = $this->title;
        }
        
        if (isset($this->description)) {
            $props['description'] = $this->description;
        }
        
        return $props;
    }
    
    protected function getValidationSchema(): ValidationSchema
    {
        return ValidationSchema::make()
            ->string('value')
            ->email()
            ->required()
            ->message('The email address is required and must be valid');
    }
}

class MessageInputSchema extends UIComponentSchema
{
    protected string $title = 'Message Input';
    protected string $description = 'A textarea for message content';
    
    public function properties(): array
    {
        $props = [
            'type' => 'textarea',
            'label' => 'Message',
            'rows' => 4
        ];
        
        if (isset($this->title)) {
            $props['title'] = $this->title;
        }
        
        if (isset($this->description)) {
            $props['description'] = $this->description;
        }
        
        return $props;
    }
    
    protected function getValidationSchema(): ValidationSchema
    {
        return ValidationSchema::make()
            ->string('value')
            ->min(10)
            ->max(500)
            ->required()
            ->message('Please provide a message between 10 and 500 characters');
    }
}
```

### 3. Compose Components

```php
use Skillcraft\UiSchemaCraft\Facades\UiSchema;

// Create components using the service
$form = UiSchema::createComponent('form');
$emailInput = UiSchema::createComponent('email-input');
$messageInput = UiSchema::createComponent('message-input');

// Add child components with location identifiers
$form->addChild($emailInput, 'main')
     ->addChild($messageInput, 'main');

// Get complete schema
$schema = $form->toArray();

// You can also check for the existence of children
$hasChildren = $form->hasChildren();         // true
$hasChildrenInMain = $form->hasChildren('main');  // true
$childCount = count($form->getChildren());   // 2

// And remove children when needed
$form->removeChild($emailInput);
```

### 4. Example Output

```json
{
    "type": "form",
    "version": "1.0.0",
    "component": "",
    "properties": {
        "submitText": "Send Message",
        "title": "Contact Form",
        "description": "A contact form with multiple inputs",
        "children": [
            {
                "type": "email-input",
                "version": "1.0.0",
                "component": "",
                "properties": {
                    "type": "email",
                    "label": "Email Address",
                    "placeholder": "Enter your email",
                    "title": "Email Input",
                    "description": "An input field for email addresses"
                },
                "title": "Email Input",
                "description": "An input field for email addresses"
            },
            {
                "type": "message-input",
                "version": "1.0.0",
                "component": "",
                "properties": {
                    "type": "textarea",
                    "label": "Message",
                    "rows": 4,
                    "title": "Message Input",
                    "description": "A textarea for message content"
                },
                "title": "Message Input",
                "description": "A textarea for message content"
            }
        ]
    },
    "title": "Contact Form",
    "description": "A contact form with multiple inputs",
    "children": [
        {
            "type": "email-input",
            "version": "1.0.0",
            "component": "",
            "properties": {
                "type": "email",
                "label": "Email Address",
                "placeholder": "Enter your email",
                "title": "Email Input",
                "description": "An input field for email addresses"
            },
            "title": "Email Input",
            "description": "An input field for email addresses"
        },
        {
            "type": "message-input",
            "version": "1.0.0",
            "component": "",
            "properties": {
                "type": "textarea",
                "label": "Message",
                "rows": 4,
                "title": "Message Input",
                "description": "A textarea for message content"
            },
            "title": "Message Input",
            "description": "A textarea for message content"
        }
    ]
}
```

### 5. Using with State and Validation

One of UI Schema Craft's most powerful features is the combination of component composition, state management, and validation. Here's how they work together:

```php
use Skillcraft\UiSchemaCraft\Facades\UiSchema;

// 1. Create a form with child components
$form = UiSchema::createComponent('form');
$emailInput = UiSchema::createComponent('email-input');
$messageInput = UiSchema::createComponent('message-input');

$form->addChild($emailInput, 'form-body')
     ->addChild($messageInput, 'form-body');

// 2. Save initial state
$stateId = UiSchema::saveState('form', [
    'email' => '',
    'message' => ''
]);

// 3. Retrieve form with the saved state
$form = UiSchema::getComponent('form', $stateId);

// 4. Validate the component against its validation schema
$validationResult = $form->validate([
    'email' => 'invalid-email',
    'message' => 'Too short'
]);

// 5. Check validation result
if (!$validationResult['valid']) {
    // Handle validation errors
    $errors = $validationResult['errors'];
    // $errors will contain validation failures for both email and message components
}

// 6. Update state with valid data
$validData = [
    'email' => 'user@example.com',
    'message' => 'This is a message that meets the requirements!'
];

$form->validate($validData); // Will return ['valid' => true, 'errors' => null]

// 7. Update state
UiSchema::saveState('form', $validData, $stateId);
```

### 6. Advanced Component Discovery

UI Schema Craft allows components to be discovered from various sources:

```php
// Register components from a namespace
UiSchema::registerNamespace('App\UiSchemas\Forms');
UiSchema::registerNamespace('App\UiSchemas\Layouts');

// Register components from a package
UiSchema::registerNamespace('VendorName\Package\UiSchemas');

// Register individual components
UiSchema::registerComponent(CustomButtonSchema::class);

// Discover available components
$availableComponents = UiSchema::getAvailableComponents();

// Check if a component type exists
$exists = UiSchema::hasComponent('custom-button');
```

### 7. Testing UI Components

UI Schema Craft is designed to be testable. Here are examples of how to test your component schemas:

```php
use Skillcraft\UiSchemaCraft\Services\UiSchemaCraftService;
use Skillcraft\SchemaState\Contracts\StateManagerInterface;
use Skillcraft\UiSchemaCraft\ComponentResolver;
use Skillcraft\ValidationCraft\Contracts\ValidatorInterface;
use Mockery;

class UiSchemaCraftTest extends TestCase
{
    protected UiSchemaCraftService $service;
    protected ComponentResolver $resolver;
    protected StateManagerInterface $stateManager;
    protected ValidatorInterface $validator;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Setup mocks
        $this->resolver = Mockery::mock(ComponentResolver::class);
        $this->stateManager = Mockery::mock(StateManagerInterface::class);
        $this->validator = Mockery::mock(ValidatorInterface::class);
        
        // Create service with mocks
        $this->service = new UiSchemaCraftService(
            $this->resolver,
            $this->stateManager,
            $this->validator
        );
    }
    
    /** @test */
    public function testCreateComponent()
    {
        // Setup component class mock
        $componentClass = YourComponentSchema::class;
        $componentInstance = new $componentClass();
        
        // Setup resolver expectations
        $this->resolver->shouldReceive('resolve')
            ->with('your-component')
            ->once()
            ->andReturn($componentClass);
            
        // Create component
        $component = $this->service->createComponent('your-component');
        
        // Assert component type
        $this->assertEquals('your-component', $component->getType());
    }
    
    /** @test */
    public function testComponentValidation()
    {
        // Setup mock expectations
        $this->resolver->shouldReceive('resolve')
            ->andReturn(YourComponentSchema::class);
            
        $this->validator->shouldReceive('validate')
            ->once()
            ->andReturn(true);
        
        // Create component
        $component = $this->service->createComponent('your-component');
        
        // Test validation
        $result = $component->validate(['field' => 'value']);
        
        // Assert validation passed
        $this->assertTrue($result['valid']);
    }
    
    /** @test */
    public function testComponentComposition()
    {
        // Create parent component
        $parentComponent = new ComposableTestComponent();
        $parentComponent->setTitle('Parent Container');
        $parentComponent->setDescription('A container component with children');
        
        // Create child components
        $childComponent1 = new TestComponent();
        $childComponent1->setTitle('First Child');
        
        $childComponent2 = new TestComponent();
        $childComponent2->setTitle('Second Child');
        
        // Add children
        $parentComponent->addChild($childComponent1, 'main');
        $parentComponent->addChild($childComponent2, 'sidebar');
        
        // Verify parent has children
        $this->assertTrue($parentComponent->hasChildren());
        $this->assertEquals(2, count($parentComponent->getChildren()));
        
        // Get schema
        $schema = $parentComponent->toArray();
        
        // Verify structure
        $this->assertEquals('Parent Container', $schema['title']);
        $this->assertCount(2, $schema['children']);
        
        // Verify child titles are present
        $childNames = array_map(function($child) {
            return $child['title'] ?? null;
        }, $schema['children']);
        
        $this->assertContains('First Child', $childNames);
        $this->assertContains('Second Child', $childNames);
    }
    
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
```

### 8. Integration with Laravel Applications

You can easily integrate UI Schema Craft with your Laravel application's frontend:

```php
// In your controller
public function getForm()
{
    // Create form with state
    $stateId = UiSchema::saveState('form', [
        'email' => '',
        'message' => ''
    ]);
    
    $form = UiSchema::getComponent('form', $stateId);
    
    return response()->json([
        'schema' => $form->toArray(),
        'stateId' => $stateId
    ]);
}

// In your API endpoint for form submission
public function submitForm(Request $request)
{
    $stateId = $request->input('stateId');
    $formData = $request->input('data');
    
    // Get form with state
    $form = UiSchema::getComponent('form', $stateId);
    
    // Validate submission
    $validationResult = $form->validate($formData);
    
    if (!$validationResult['valid']) {
        return response()->json([
            'success' => false,
            'errors' => $validationResult['errors']
        ], 422);
    }
    
    // Process valid form data
    // ...
    
    // Update state
    UiSchema::saveState('form', $formData, $stateId);
    
    return response()->json([
        'success' => true
    ]);
}
```

### 9. Frontend Integration Example

```javascript
// Vue.js example
export default {
  data() {
    return {
      schema: {},
      stateId: null,
      formData: {}
    }
  },
  
  async mounted() {
    // Fetch schema from backend
    const response = await fetch('/api/form-schema');
    const data = await response.json();
    
    this.schema = data.schema;
    this.stateId = data.stateId;
    this.initializeFormData();
  },
  
  methods: {
    initializeFormData() {
      // Extract initial values from schema
      this.formData = {
        email: '',
        message: ''
      };
    },
    
    async submitForm() {
      try {
        const response = await fetch('/api/submit-form', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            stateId: this.stateId,
            data: this.formData
          })
        });
        
        const result = await response.json();
        
        if (!response.ok) {
          // Handle validation errors
          this.errors = result.errors;
          return;
        }
        
        // Handle success
        this.resetForm();
      } catch (error) {
        console.error('Form submission error:', error);
      }
    },
    
    resetForm() {
      this.formData = {
        email: '',
        message: ''
      };
      this.errors = {};
    }
  }
}
```

## Conclusion

UI Schema Craft provides a robust foundation for building dynamic, composable UI components in Laravel applications. By combining powerful validation, state management, and component composition capabilities, it enables you to create complex, interactive interfaces with clean, maintainable code.

Key benefits:

- **Type-safe Component Definitions**: Define component schemas with proper type definitions
- **Flexible Validation**: Validate component state against defined schemas
- **Stateful Components**: Manage and persist component state
- **Composable Architecture**: Build complex UIs from simple, reusable components
- **Package Discovery**: Seamlessly integrate components from multiple packages
- **Testing Support**: Comprehensive testing utilities for your components

## Creating Component Packages

You can create packages with reusable components:

1. Create your package structure:
```
my-components/
├── src/
│   ├── Components/
│   │   ├── CustomInputSchema.php
│   │   └── CustomSelectSchema.php
│   └── MyComponentsServiceProvider.php
└── composer.json
```

2. Register components in your service provider:
```php
namespace MyVendor\MyComponents;

use Illuminate\Support\ServiceProvider;
use Skillcraft\UiSchemaCraft\Services\UiSchemaCraftService;

class MyComponentsServiceProvider extends ServiceProvider
{
    public function boot(UiSchemaCraftService $uiSchema): void
    {
        $uiSchema->registerNamespace('MyVendor\\MyComponents\\Components');
    }
}
```

## Available Components

To list all available components:

```php
$types = UiSchema::getAvailableTypes();
```

## State Management

State is handled by the `state-craft` package:

```php
// Save state
$stateId = UiSchema::saveState('text-input', ['value' => 'Hello']);

// Get state
$schema = UiSchema::getComponent('text-input', $stateId);

// Delete state
UiSchema::deleteState($stateId);

// Get all states for a type
$states = UiSchema::getStates('text-input');
```

## Validation

Validation is handled by the `validation-craft` package. See the [validation-craft documentation](../validation-craft/README.md) for more details.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
