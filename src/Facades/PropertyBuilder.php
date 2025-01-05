<?php

namespace Skillcraft\UiSchemaCraft\Facades;

use Illuminate\Support\Facades\Facade;
use Skillcraft\UiSchemaCraft\Schema\PropertyBuilder as CorePropertyBuilder;

/**
 * @method static \Skillcraft\UiSchemaCraft\Schema\Property string(string $name, ?string $label = null) Create a string property
 * @method static \Skillcraft\UiSchemaCraft\Schema\Property number(string $name, ?string $label = null) Create a number property
 * @method static \Skillcraft\UiSchemaCraft\Schema\Property boolean(string $name, ?string $label = null) Create a boolean property
 * @method static \Skillcraft\UiSchemaCraft\Schema\Property array(string $name, ?string $label = null) Create an array property
 * @method static \Skillcraft\UiSchemaCraft\Schema\Property object(string $name, ?string $label = null) Create an object property
 * 
 * @method static \Skillcraft\UiSchemaCraft\Schema\Property treeSelect(string $name, ?string $label = null) Create a hierarchical selection field with parent-child relationships
 * @method static \Skillcraft\UiSchemaCraft\Schema\Property multiSelect(string $name, ?string $label = null) Create a multiple selection field with chip display
 * @method static \Skillcraft\UiSchemaCraft\Schema\Property combobox(string $name, ?string $label = null) Create a hybrid dropdown/text input with autocomplete
 * 
 * @method static \Skillcraft\UiSchemaCraft\Schema\Property codeEditor(string $name, ?string $label = null) Create a code editor with syntax highlighting
 * @method static \Skillcraft\UiSchemaCraft\Schema\Property jsonEditor(string $name, ?string $label = null) Create a JSON editor with validation
 * @method static \Skillcraft\UiSchemaCraft\Schema\Property markdown(string $name, ?string $label = null) Create a markdown editor with preview
 * 
 * @method static \Skillcraft\UiSchemaCraft\Schema\Property imageUpload(string $name, ?string $label = null) Create an image upload field with preview
 * @method static \Skillcraft\UiSchemaCraft\Schema\Property mediaGallery(string $name, ?string $label = null) Create a media gallery with metadata editing
 * @method static \Skillcraft\UiSchemaCraft\Schema\Property avatar(string $name, ?string $label = null) Create an avatar upload with cropping
 * 
 * @method static \Skillcraft\UiSchemaCraft\Schema\Property currency(string $name, ?string $label = null) Create a currency input with formatting
 * @method static \Skillcraft\UiSchemaCraft\Schema\Property percentage(string $name, ?string $label = null) Create a percentage input with validation
 * @method static \Skillcraft\UiSchemaCraft\Schema\Property mask(string $name, ?string $label = null) Create a masked input for formatted text
 * 
 * @method static \Skillcraft\UiSchemaCraft\Schema\Property address(string $name, ?string $label = null) Create an address input with validation and autocomplete
 * @method static \Skillcraft\UiSchemaCraft\Schema\Property coordinates(string $name, ?string $label = null) Create a latitude/longitude input with validation
 * @method static \Skillcraft\UiSchemaCraft\Schema\Property map(string $name, ?string $label = null) Create an interactive map component
 * 
 * @method static \Skillcraft\UiSchemaCraft\Schema\Property time(string $name, ?string $label = null) Create a time picker with timezone support
 * @method static \Skillcraft\UiSchemaCraft\Schema\Property timeRange(string $name, ?string $label = null) Create a time range selector
 * @method static \Skillcraft\UiSchemaCraft\Schema\Property duration(string $name, ?string $label = null) Create a duration input with multiple formats
 * 
 * @method static \Skillcraft\UiSchemaCraft\Schema\Property chart(string $name, ?string $label = null) Create a chart configuration component
 * @method static \Skillcraft\UiSchemaCraft\Schema\Property table(string $name, ?string $label = null) Create an interactive table editor
 * @method static \Skillcraft\UiSchemaCraft\Schema\Property matrix(string $name, ?string $label = null) Create a matrix/grid input component
 * 
 * @method static \Skillcraft\UiSchemaCraft\Schema\Property dynamicForm(string $name, ?string $label = null) Create a dynamic form builder
 * @method static \Skillcraft\UiSchemaCraft\Schema\Property wizard(string $name, ?string $label = null) Create a multi-step form wizard
 * @method static \Skillcraft\UiSchemaCraft\Schema\Property conditionalForm(string $name, ?string $label = null) Create a form with conditional logic
 * 
 * @method static \Skillcraft\UiSchemaCraft\Schema\Property otp(string $name, ?string $label = null) Create an OTP input with validation
 * @method static \Skillcraft\UiSchemaCraft\Schema\Property captcha(string $name, ?string $label = null) Create a CAPTCHA verification component
 * @method static \Skillcraft\UiSchemaCraft\Schema\Property mfa(string $name, ?string $label = null) Create a multi-factor authentication component
 * 
 * @method static \Skillcraft\UiSchemaCraft\Schema\Property withBuilder(callable $callback) Configure the property using a builder callback
 * @method static \Skillcraft\UiSchemaCraft\Schema\Property description(?string $description) Set the property description
 * @method static \Skillcraft\UiSchemaCraft\Schema\Property default($value) Set the default value
 * @method static \Skillcraft\UiSchemaCraft\Schema\Property nullable(bool $nullable = true) Make the property nullable
 * @method static \Skillcraft\UiSchemaCraft\Schema\Property required(bool $required = true) Make the property required
 * @method static \Skillcraft\UiSchemaCraft\Schema\Property enum(array $values) Set allowed enum values
 * @method static array toArray() Convert the property to an array
 * @method static string getName() Get the property name
 * @method static string|null getDescription() Get the property description
 * @method static string getType() Get the property type
 * 
 * @see \Skillcraft\UiSchemaCraft\Schema\PropertyBuilder
 */
class PropertyBuilder extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'property-builder';
    }
}
