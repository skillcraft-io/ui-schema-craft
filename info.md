# UI Schema Craft

A Laravel package for building dynamic UI components with schema-based validation and state management.

## Core Features

- Schema-based UI component definition
- Laravel validation integration
- Component composition
- State management
- Meta information support

## Installation

```bash
composer require skillcraft/ui-schema-craft
```

## Basic Usage

```php
use Skillcraft\UiSchemaCraft\Schema\Schema;
use Skillcraft\UiSchemaCraft\Schema\PropertyBuilder;
use Illuminate\Validation\Rule;

class UserForm extends UIComponentSchema
{
    protected function defineSchema(): Schema
    {
        $schema = new Schema();

        // Simple string property with validation
        $schema->addProperty(
            PropertyBuilder::string('username')
                ->required()
                ->min(3)
                ->max(20)
                ->unique('users', 'username')
                ->description('Choose a unique username')
        );

        // Email property with custom error message
        $schema->addProperty(
            PropertyBuilder::email('email')
                ->required()
                ->unique('users', 'email')
        );

        // Object property with nested fields
        $schema->addProperty(
            PropertyBuilder::object('address')
                ->addChild(
                    PropertyBuilder::string('street')->required()
                )
                ->addChild(
                    PropertyBuilder::string('city')->required()
                )
                ->addChild(
                    PropertyBuilder::string('country')
                        ->required()
                        ->in(['US', 'CA', 'UK'])
                )
        );

        // Array property
        $schema->addProperty(
            PropertyBuilder::array('roles')
                ->required()
                ->in(['admin', 'user', 'editor'])
        );

        // Custom validation messages
        $schema->withMessages([
            'email.unique' => 'This email is already registered',
            'roles.in' => 'Invalid role selected'
        ]);

        // Custom attribute names
        $schema->withAttributes([
            'address.street' => 'street address'
        ]);

        return $schema;
    }
}

## Property Types

- `string`: Text values
- `number`: Numeric values
- `integer`: Integer values
- `boolean`: True/false values
- `array`: List of values
- `object`: Nested properties
- `date`: Date values
- `email`: Email addresses

## Validation Rules

All Laravel validation rules are supported:

```php
PropertyBuilder::string('field')
    ->required()                     // Required field
    ->nullable()                     // Can be null
    ->min(5)                         // Minimum value/length
    ->max(100)                       // Maximum value/length
    ->between(5, 10)                 // Between range
    ->pattern('/^[A-Z]+$/')         // Regular expression
    ->in(['a', 'b', 'c'])           // Must be in array
    ->exists('table', 'column')      // Must exist in database
    ->unique('table', 'column')      // Must be unique in database
    ->rules('custom_rule|other_rule') // Custom rules
```

## Conditional Validation

The package supports Laravel's conditional validation rules:

```php
// Basic conditional validation
PropertyBuilder::string('shipping_address')
    ->when('needs_shipping', true, ['required', 'string', 'min:10'])
    ->whenNot('pickup_in_store', true, ['required']);

// Required with other fields
PropertyBuilder::string('last_name')
    ->requiredWith('first_name');

// Required without other fields
PropertyBuilder::string('phone')
    ->requiredWithout('email');

// Required if condition
PropertyBuilder::string('company_name')
    ->requiredIf('is_business', true);

// Required unless condition
PropertyBuilder::string('guardian_name')
    ->requiredUnless('age', 18);

// Prohibited if condition
PropertyBuilder::string('business_tax_id')
    ->prohibitedIf('is_personal', true);

// Exclude if condition
PropertyBuilder::string('alternate_email')
    ->excludeIf('no_alternate_contact', true);

// Multiple field dependencies
PropertyBuilder::string('card_cvv')
    ->when([
        'payment_type' => 'credit_card',
        'card_present' => false
    ], true, ['required', 'digits:3']);

// Custom error messages for conditional rules
$schema->withMessages([
    'shipping_address.required_if' => 'Shipping address is required when shipping is needed',
    'card_cvv.required' => 'CVV is required for online credit card payments'
]);
```

## Advanced Conditional Validation

The package supports several types of conditional validation:

### 1. Basic Field Conditions

```php
// Simple field value check
PropertyBuilder::string('shipping_address')
    ->when('needs_shipping', true, ['required', 'string'])
    ->whenNot('pickup_in_store', true, ['required']);
```

### 2. Array-Based Conditions

```php
// Multiple field conditions
PropertyBuilder::string('card_cvv')
    ->when([
        'payment_type' => 'credit_card',
        'card_present' => false,
        'is_international' => true
    ], true, ['required', 'digits:3']);

// Array value conditions
PropertyBuilder::string('special_notes')
    ->whenInArray('user_type', ['vip', 'premium'], ['required']);
```

### 3. Pattern Matching Conditions

```php
// Regex pattern matching
PropertyBuilder::string('business_id')
    ->whenMatches('email', '/\.business$/', ['required'])
    ->whenMatches('country', '/^(US|CA)$/', ['digits:9']);
```

### 4. Comparison Conditions

```php
// Numeric comparisons
PropertyBuilder::string('guardian_consent')
    ->whenCompare('age', '<', 18, ['required'])
    ->whenCompare('risk_score', '>=', 7, ['required']);

// String comparisons
PropertyBuilder::string('vat_number')
    ->whenCompare('country', 'starts_with', 'EU', ['required'])
    ->whenCompare('business_type', 'contains', 'ltd', ['required']);
```

### 5. Custom Closure Conditions

```php
// Complex custom logic
PropertyBuilder::string('tax_id')
    ->when(function($input) {
        return $input['country'] === 'US' && 
               $input['annual_revenue'] > 1000000 &&
               !empty($input['employees']);
    }, null, ['required', 'digits:9']);

// Date-based conditions
PropertyBuilder::string('passport_scan')
    ->when(function($input) {
        $expiryDate = new DateTime($input['passport_expiry']);
        $sixMonthsFromNow = new DateTime('+6 months');
        return $expiryDate <= $sixMonthsFromNow;
    }, null, ['required']);
```

### Complex Form Example

```php
class EmploymentForm extends UIComponentSchema
{
    protected function defineSchema(): Schema
    {
        $schema = new Schema();

        // Employment type with conditional fields
        $schema->addProperty(
            PropertyBuilder::string('employment_type')
                ->required()
                ->in(['full_time', 'part_time', 'contractor', 'self_employed'])
        );

        // Company information
        $schema->addProperty(
            PropertyBuilder::string('company_name')
                ->when([
                    'employment_type' => 'full_time',
                    'is_remote' => false
                ], true, ['required', 'string'])
                ->whenCompare('salary', '>', 100000, ['required'])
        );

        // Contractor-specific fields
        $schema->addProperty(
            PropertyBuilder::string('business_id')
                ->when('employment_type', 'contractor', ['required'])
                ->whenMatches('country', '/^(US|CA)$/', ['digits:9'])
                ->when(function($input) {
                    return $input['annual_revenue'] > 50000;
                }, null, ['required'])
        );

        // Work location
        $schema->addProperty(
            PropertyBuilder::object('work_location')
                ->when(function($input) {
                    return $input['employment_type'] !== 'contractor' &&
                           !$input['is_remote'];
                }, null, ['required'])
                ->whenInArray('office_type', ['headquarters', 'branch'], [
                    'required',
                    function($attribute, $value, $fail) {
                        if (empty($value['floor']) || empty($value['desk_number'])) {
                            $fail('Complete work location details required');
                        }
                    }
                ])
        );

        // Schedule preferences
        $schema->addProperty(
            PropertyBuilder::array('schedule_preferences')
                ->when('employment_type', 'part_time', ['required', 'min:3'])
                ->whenCompare('hours_per_week', '<', 20, ['required'])
        );

        // Custom validation messages
        $schema->withMessages([
            'company_name.required_if' => 'Company name is required for full-time on-site employees',
            'business_id.required_if' => 'Business ID is required for contractors',
            'work_location.required' => 'Work location is required for non-remote employees'
        ]);

        return $schema;
    }
}

## Complex Form Example

```php
class PaymentForm extends UIComponentSchema
{
    protected function defineSchema(): Schema
    {
        $schema = new Schema();

        // Payment method selection
        $schema->addProperty(
            PropertyBuilder::string('payment_method')
                ->required()
                ->in(['credit_card', 'bank_transfer', 'paypal'])
        );

        // Credit card fields
        $schema->addProperty(
            PropertyBuilder::string('card_number')
                ->when('payment_method', 'credit_card', ['required', 'digits:16'])
        );

        $schema->addProperty(
            PropertyBuilder::string('card_expiry')
                ->when('payment_method', 'credit_card', ['required', 'date_format:m/Y'])
        );

        $schema->addProperty(
            PropertyBuilder::string('card_cvv')
                ->when('payment_method', 'credit_card', ['required', 'digits:3'])
        );

        // Bank transfer fields
        $schema->addProperty(
            PropertyBuilder::string('bank_account')
                ->when('payment_method', 'bank_transfer', ['required', 'string'])
        );

        $schema->addProperty(
            PropertyBuilder::string('bank_routing')
                ->when('payment_method', 'bank_transfer', ['required', 'digits:9'])
        );

        // PayPal fields
        $schema->addProperty(
            PropertyBuilder::email('paypal_email')
                ->when('payment_method', 'paypal', ['required', 'email'])
        );

        // Billing address (required for credit card and bank transfer)
        $schema->addProperty(
            PropertyBuilder::object('billing_address')
                ->when('payment_method', ['credit_card', 'bank_transfer'], [
                    'required',
                    function($attribute, $value, $fail) {
                        if (empty($value['street']) || empty($value['city'])) {
                            $fail('Complete billing address is required');
                        }
                    }
                ])
        );

        // Custom error messages
        $schema->withMessages([
            'card_number.required' => 'Card number is required for credit card payments',
            'bank_account.required' => 'Bank account number is required for bank transfers',
            'paypal_email.required' => 'PayPal email is required for PayPal payments'
        ]);

        return $schema;
    }
}

## Meta Information

Add UI-specific metadata to properties:

```php
PropertyBuilder::string('field')
    ->meta('placeholder', 'Enter value')
    ->meta('tooltip', 'Help text')
    ->meta('class', 'form-control');
```

## Component Composition

Create complex nested components:

```php
$form = PropertyBuilder::object('form')
    ->addChild(
        PropertyBuilder::string('title')
    )
    ->addChild(
        PropertyBuilder::array('items')
            ->addChild(
                PropertyBuilder::object('item')
                    ->addChild(PropertyBuilder::string('name'))
                    ->addChild(PropertyBuilder::number('quantity'))
            )
    );
```

## State Management

```php
// Save state
$state = $component->saveState([
    'username' => 'john.doe',
    'email' => 'john@example.com'
]);

// Get component with state
$component = $schema->getComponent($state['stateId']);

// Validate state
$validation = $schema->validate([
    'username' => 'john.doe',
    'email' => 'invalid-email'
]);
```

## Testing

```bash
composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.