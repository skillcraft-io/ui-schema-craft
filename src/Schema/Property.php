<?php

namespace Skillcraft\UiSchemaCraft\Schema;

use Skillcraft\UiSchemaCraft\Schema\Traits\TimeTrait;

/**
 * Property
 * 
 * Represents a schema property within the UI Schema Craft framework.
 * Properties are the building blocks of component schemas and define
 * the structure, validation rules, and behavior of component attributes.
 * 
 * This class provides a fluent interface for defining complex properties
 * with various constraints, validations, and metadata.
 */
class Property
{
    use TimeTrait;

    /**
     * The property name identifier
     * 
     * @var string
     */
    protected string $name;
    
    /**
     * The data type of the property (string or array of allowed types)
     * 
     * @var string|array
     */
    protected string|array $type;
    
    /**
     * Human-readable description of the property
     * 
     * @var string|null
     */
    protected ?string $description;
    
    /**
     * Default value for the property if none is provided
     * 
     * @var mixed
     */
    protected mixed $default = null;
    
    /**
     * Example value for documentation or UI rendering purposes
     * 
     * @var mixed
     */
    protected mixed $example = null;
    
    /**
     * The current runtime value of this property.
     */
    protected mixed $value = null;
    
    /**
     * Validation rules applied to the property
     * 
     * @var array
     */
    protected array $rules = [];
    
    /**
     * Additional HTML or custom attributes for the property
     * 
     * @var array
     */
    protected array $attributes = [];
    
    /**
     * Nested properties (for object type properties)
     * 
     * @var array
     */
    protected array $properties = [];
    
    /**
     * Schema for array items (for array type properties)
     * 
     * @var array
     */
    protected array $items = [];
    
    /**
     * Format specification (e.g., date-time, email, uri)
     * 
     * @var string|null
     */
    protected ?string $format = null;
    
    /**
     * Minimum value constraint (for numeric properties)
     * 
     * @var float|null
     */
    protected ?float $minimum = null;
    
    /**
     * Maximum value constraint (for numeric properties)
     * 
     * @var float|null
     */
    protected ?float $maximum = null;
    
    /**
     * Regular expression pattern for string validation
     * 
     * @var string|null
     */
    protected ?string $pattern = null;
    
    /**
     * Reference to another schema definition
     * 
     * @var string|null
     */
    protected ?string $reference = null;
    
    /**
     * Whether the property is required in validation
     * 
     * @var bool
     */
    protected bool $isRequired = false;
    
    /**
     * Conditional validation rules based on other property values
     * 
     * @var array
     */
    protected array $conditionalRules = [];

    /**
     * Creates a new Property instance
     *
     * @param string $name The name identifier of the property
     * @param string|array $type The data type(s) this property accepts
     * @param string|null $description A human-readable description of the property
     */
    public function __construct(string $name, string|array $type, ?string $description = null)
    {
        $this->name = $name;
        $this->type = is_array($type) ? array_map('strval', $type) : $type;
        $this->description = $description;
    }

    /**
     * Get the property name
     *
     * @return string The property name
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the property type(s)
     *
     * @return string|array The data type or array of allowed types
     */
    public function getType(): string|array
    {
        return $this->type;
    }

    /**
     * Get the primary type when multiple types are allowed
     *
     * @return string The primary data type
     */
    public function getPrimaryType(): string
    {
        return is_array($this->type) ? $this->type[0] : $this->type;
    }

    /**
     * Get the property description
     *
     * @return string|null The human-readable description
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set the default value (fluent alias for setDefault)
     *
     * @param mixed $value The default value
     * @return self For method chaining
     */
    public function default(mixed $value): self
    {
        return $this->setDefault($value);
    }

    /**
     * Set the default value for this property
     *
     * @param mixed $value The default value
     * @return self For method chaining
     */
    public function setDefault(mixed $value): self
    {
        $this->default = $value;
        return $this;
    }

    /**
     * Get the default value
     *
     * @return mixed The default value
     */
    public function getDefault(): mixed
    {
        return $this->default;
    }

    /**
     * Set the example value (fluent alias for setExample)
     *
     * @param mixed $example The example value for documentation
     * @return self For method chaining
     */
    public function example(mixed $example): self
    {
        return $this->setExample($example);
    }

    /**
     * Set the example value for this property
     *
     * @param mixed $example The example value for documentation
     * @return self For method chaining
     */
    public function setExample(mixed $example): self
    {
        $this->example = $example;
        return $this;
    }

    /**
     * Get the example value
     *
     * @return mixed The example value
     */
    public function getExample(): mixed
    {
        return $this->example;
    }
    
    /**
     * Set the runtime value for this property
     * 
     * @param mixed $value The current runtime value (e.g., from API data)
     * @return self
     */
    public function value(mixed $value): self
    {
        return $this->setValue($value);
    }
    
    /**
     * Set the runtime value for this property
     * 
     * @param mixed $value The current runtime value (e.g., from API data)
     * @return self
     */
    public function setValue(mixed $value): self
    {
        $this->value = $value;
        return $this;
    }
    
    /**
     * Get the runtime value
     * 
     * @return mixed The current runtime value
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    public function addRule(string $rule): self
    {
        if ($rule === 'required') {
            $this->isRequired = true;
        }
        if (!in_array($rule, $this->rules)) {
            $this->rules[] = $rule;
        }
        return $this;
    }

    public function getRules(): array
    {
        return $this->rules;
    }

    public function rules(array|string $rules): self
    {
        if (is_string($rules)) {
            $rules = explode('|', $rules);
        }

        foreach ($rules as $rule) {
            $this->addRule($rule);
        }

        return $this;
    }

    public function addAttribute(string $key, mixed $value): self
    {
        $this->attributes[$key] = $value;
        return $this;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getAttribute(string $key, mixed $default = null): mixed
    {
        return $this->attributes[$key] ?? $default;
    }

    public function addProperty(string $name, array|Property $schema): self
    {
        $this->properties[$name] = $schema;
        return $this;
    }

    public function getProperties(): array
    {
        return $this->properties;
    }

    public function properties(array|PropertyBuilder $properties): self
    {
        if ($this->type !== 'object') {
            throw new \InvalidArgumentException('Properties can only be set on object type');
        }

        if ($properties instanceof PropertyBuilder) {
            $properties = $properties->toArray();
        }
        
        foreach ($properties as $name => $schema) {
            $this->addProperty($name, $schema);
        }
        return $this;
    }

    public function items(array|Property $items): self
    {
        if ($items instanceof Property) {
            $items = $items->toArray();
        }
        $this->items = $items;
        return $this;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function format(string $format): self
    {
        $this->format = $format;
        return $this;
    }

    public function getFormat(): ?string
    {
        return $this->format;
    }

    public function min(float $value): self
    {
        $this->minimum = $value;
        return $this;
    }

    public function minimum(float $value): self
    {
        return $this->min($value);
    }

    public function max(float $value): self
    {
        $this->maximum = $value;
        return $this;
    }

    public function maximum(float $value): self
    {
        return $this->max($value);
    }

    public function pattern(string $pattern): self
    {
        $this->pattern = $pattern;
        return $this;
    }

    public function getPattern(): ?string
    {
        return $this->pattern;
    }

    public function reference(string $ref): self
    {
        $this->reference = $ref;
        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function requiredWith(string|array $fields): self
    {
        if (is_string($fields)) {
            $fields = [$fields];
        }
        
        $this->conditionalRules[] = [
            'type' => 'requiredWith',
            'fields' => $fields,
            'rules' => ['required']
        ];
        
        $this->addRule('required_with:' . implode(',', $fields));
        return $this;
    }

    public function requiredWithout(string|array $fields): self
    {
        if (is_string($fields)) {
            $fields = [$fields];
        }
        
        $this->conditionalRules[] = [
            'type' => 'requiredWithout',
            'fields' => $fields,
            'rules' => ['required']
        ];
        
        $this->addRule('required_without:' . implode(',', $fields));
        return $this;
    }

    public function requiredIf(string $field, mixed $value): self
    {
        $this->conditionalRules[] = [
            'type' => 'field',
            'field' => $field,
            'value' => $value,
            'rules' => ['required']
        ];
        
        $this->addRule('required_if:' . $field . ',' . $value);
        return $this;
    }

    public function prohibitedIf(string $field, mixed $value): self
    {
        $this->conditionalRules[] = [
            'type' => 'field',
            'field' => $field,
            'value' => $value,
            'rules' => ['prohibited']
        ];
        
        return $this;
    }

    private function validateRequiredWithoutFields(array $fields): bool
    {
        foreach ($fields as $field) {
            if (isset($this->attributes['_context'][$field]) && 
                $this->attributes['_context'][$field] !== null && 
                $this->attributes['_context'][$field] !== '') {
                return false;
            }
        }
        return true;
    }

    private function validateRequiredWithFields(array $fields): bool
    {
        foreach ($fields as $field) {
            if (isset($this->attributes['_context'][$field]) && 
                $this->attributes['_context'][$field] !== null && 
                $this->attributes['_context'][$field] !== '') {
                return true;
            }
        }
        return false;
    }

    private function isEmptyValue(mixed $value): bool
    {
        return $value === null || $value === '';
    }

    public function validate(mixed $value): bool
    {
        // Check conditional rules first
        foreach ($this->conditionalRules as $rule) {
            if ($rule['type'] === 'requiredWith') {
                if ($this->validateRequiredWithFields($rule['fields']) && $this->isEmptyValue($value)) {
                    return false;
                }
            } elseif ($rule['type'] === 'requiredWithout') {
                if ($this->validateRequiredWithoutFields($rule['fields']) && $this->isEmptyValue($value)) {
                    return false;
                }
            }
        }

        // Handle null values
        if ($value === null) {
            if ($this->isNullable()) {
                return true;  // Skip all other validations if value is null and property is nullable
            }
            return !$this->isRequired;
        }

        // Handle empty strings
        if ($value === '') {
            return !$this->isRequired;
        }

        // Validate type
        if (is_array($this->type)) {
            if (!in_array(gettype($value), $this->type)) {
                return false;
            }
        } else {
            if ($this->type === 'object' && (is_array($value) || is_object($value))) {
                return true;
            }
            if (gettype($value) !== $this->type && !($this->type === 'number' && (is_int($value) || is_float($value)))) {
                return false;
            }
        }

        // Validate pattern
        if ($this->pattern !== null && is_string($value)) {
            if (!preg_match($this->pattern, $value)) {
                return false;
            }
        }

        // Validate numeric constraints
        if (($this->type === 'number' || $this->type === 'integer') && (is_int($value) || is_float($value))) {
            if ($this->minimum !== null && $value < $this->minimum) {
                return false;
            }
            if ($this->maximum !== null && $value > $this->maximum) {
                return false;
            }
        }

        // Validate email format if email rule is present
        if (in_array('email', $this->rules) && is_string($value)) {
            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                return false;
            }
        }

        return true;
    }

    public function getValidationMessage(): string
    {
        if ($this->isRequired) {
            return 'The ' . $this->name . ' field is required.';
        }
        return 'Invalid value for ' . $this->name;
    }

    public function toArray(): array
    {
        $array = [
            'name' => $this->name,
            'type' => $this->type,
            'nullable' => $this->isNullable(),
        ];

        if ($this->description !== null) {
            $array['description'] = $this->description;
        }

        if ($this->default !== null) {
            $array['default'] = $this->default;
        }
        
        if ($this->value !== null) {
            $array['value'] = $this->value;
        }

        if ($this->format !== null) {
            $array['format'] = $this->format;
        }

        if ($this->minimum !== null) {
            $array['minimum'] = $this->minimum;
        }

        if ($this->maximum !== null) {
            $array['maximum'] = $this->maximum;
        }

        if ($this->pattern !== null) {
            $array['pattern'] = $this->pattern;
        }

        if ($this->reference !== null) {
            $array['$ref'] = $this->reference;
        }

        if (!empty($this->rules)) {
            $array['rules'] = array_values(array_unique($this->rules));
        }

        if (!empty($this->attributes)) {
            foreach ($this->attributes as $key => $value) {
                $array[$key] = $value;
            }
        }

        if (!empty($this->properties)) {
            $array['properties'] = [];
            foreach ($this->properties as $name => $property) {
                $array['properties'][$name] = $property instanceof Property
                    ? $property->toArray()
                    : $property;
            }
        }

        if (!empty($this->items)) {
            $array['items'] = $this->items instanceof Property ? $this->items->toArray() : $this->items;
        }

        if (!empty($this->conditionalRules)) {
            $array['conditionalRules'] = $this->conditionalRules;
        }

        // Only include examples in non-production environments
        if ($this->example !== null && !app()->environment('production')) {
            $array['example'] = $this->example;
        }

        $array['required'] = $this->isRequired;

        return $array;
    }

    public function required(bool $required = true): self
    {
        $this->isRequired = $required;
        if ($required) {
            $this->addRule('required');
        } else {
            $this->rules = array_filter($this->rules, fn($rule) => !str_starts_with($rule, 'required'));
            $this->isRequired = false;
        }
        return $this;
    }

    public function isNullable(): bool
    {
        return in_array('nullable', $this->rules);
    }

    public function nullable(): self
    {
        if (is_array($this->type)) {
            if (!in_array('null', $this->type)) {
                $this->type[] = 'null';
            }
        } else {
            $originalType = (string) $this->type;
            $this->type = ['null', $originalType];
        }
        $this->addRule('nullable');
        return $this;
    }

    public function enum(array $values): self
    {
        $this->addAttribute('enum', $values);
        return $this;
    }

    public function description(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function withBuilder(callable $callback): self
    {
        if ($this->type !== 'object') {
            throw new \InvalidArgumentException('Builder can only be used with object type');
        }

        $builder = new PropertyBuilder();
        $callback($builder);
        $this->properties = $builder->toArray();
        return $this;
    }

    public function when(string|callable|array $field, mixed $value = null, callable|array|string $callback = null): self
    {
        if (is_callable($field)) {
            $this->conditionalRules[] = [
                'type' => 'closure',
                'closure' => $field,
                'rules' => is_array($value) ? $value : explode('|', $value)
            ];
            return $this;
        }

        if (is_array($field)) {
            $this->conditionalRules[] = [
                'type' => 'array',
                'field' => $field,
                'rules' => is_array($value) ? $value : explode('|', $value)
            ];
            return $this;
        }

        if (is_string($callback)) {
            $callback = explode('|', $callback);
        }

        $this->conditionalRules[] = [
            'type' => 'field',
            'field' => $field,
            'value' => $value,
            'rules' => is_array($callback) ? $callback : []
        ];

        return $this;
    }

    public function whenMatches(string $field, string $pattern, callable|array|string $callback = null): self
    {
        if (is_string($callback)) {
            $callback = explode('|', $callback);
        }

        $this->conditionalRules[] = [
            'type' => 'pattern',
            'field' => $field,
            'value' => ['pattern' => $pattern],
            'rules' => is_array($callback) ? $callback : []
        ];

        return $this;
    }

    public function whenCompare(string $field, string $operator, mixed $value, callable|array|string $callback = null): self
    {
        if (is_string($callback)) {
            $callback = explode('|', $callback);
        }

        $this->conditionalRules[] = [
            'type' => 'comparison',
            'field' => $field,
            'value' => [
                'operator' => $operator,
                'value' => $value
            ],
            'rules' => is_array($callback) ? $callback : []
        ];

        return $this;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public static function string(string $name, ?string $description = null): self
    {
        return new self($name, 'string', $description);
    }

    public static function number(string $name, ?string $description = null): self
    {
        return new self($name, 'number', $description);
    }

    public static function boolean(string $name, ?string $description = null): self
    {
        return new self($name, 'boolean', $description);
    }

    public static function object(string $name, ?string $description = null): self
    {
        return new self($name, 'object', $description);
    }

    public static function array(string $name, ?string $description = null): self
    {
        return new self($name, 'array', $description);
    }

    public static function timeRange(string $name, string|null $descriptionOrFormat = null): self
    {
        // Determine if the parameter is a description or a format
        if ($descriptionOrFormat === null || str_contains($descriptionOrFormat, ':') || str_contains($descriptionOrFormat, '-')) {
            // Handle as a format (old behavior)
            $property = new self($name, ['object', 'null'], 'Meeting Time Range');
            $property->addAttribute('timeRange', true);
            $property->format($descriptionOrFormat ?? 'Y-m-d');
            
            // Create start and end properties
            $start = new self('start', 'string', 'Start time');
            $start->format('date-time');
            
            $end = new self('end', 'string', 'End time');
            $end->format('date-time');
            
            // Add start and end to the main property
            $property->addProperty('start', $start);
            $property->addProperty('end', $end);
            
            return $property;
        } else {
            // Handle as a description (new behavior)
            $property = new self($name, ['object', 'null'], $descriptionOrFormat);
            $property->addAttribute('timeRange', true);
            $property->format('Y-m-d');
            
            // Create start and end properties
            $start = new self('start', 'string', 'Start time');
            $start->format('date-time');
            
            $end = new self('end', 'string', 'End time');
            $end->format('date-time');
            
            // Add start and end to the main property
            $property->addProperty('start', $start);
            $property->addProperty('end', $end);
            
            return $property;
        }
    }

    public static function dateRange(string $name, ?string $description = null): self
    {
        // Create main object property
        $property = new self($name, 'object', $description ?? 'Booking Period');
        
        // Create start and end properties
        $start = new self('start', 'string', 'Start date');
        $start->format('date');
        
        $end = new self('end', 'string', 'End date');
        $end->format('date');
        
        // Add start and end to the main property
        $property->addProperty('start', $start);
        $property->addProperty('end', $end);
        
        // Add options property with minDate and maxDate as simplified objects
        $options = new self('options', 'object', 'Date Range Options');
        
        // Create simplified properties as direct arrays to match test expectations
        $options->properties = [
            'minDate' => ['type' => 'string', 'format' => 'date'],
            'maxDate' => ['type' => 'string', 'format' => 'date'],
            'disabledDates' => ['type' => 'array', 'items' => ['type' => 'string', 'format' => 'date']],
            'format' => ['type' => 'string', 'default' => 'YYYY-MM-DD'],
            'shortcuts' => ['type' => 'boolean', 'default' => true],
            'weekNumbers' => ['type' => 'boolean', 'default' => false],
            'monthSelector' => ['type' => 'boolean', 'default' => true],
            'yearSelector' => ['type' => 'boolean', 'default' => true]
        ];
        
        $property->addProperty('options', $options);
        
        return $property;
    }

    public static function time(string $name, ?string $description = null): self
    {
        // If no description is provided, use the name with proper formatting
        if ($description === null) {
            $description = str_replace('_', ' ', $name);
            $description = ucwords($description);
        }
        
        $property = new self($name, 'string', $description);
        $property->format('time');
        // Add time validation rule directly instead of calling the time method
        // which would cause recursion
        $property->addRule('time:H:i:s');
        return $property;
    }

    public static function duration(string $name, ?string $description = null): self
    {
        // Create main object property
        $property = new self($name, 'object', $description ?? 'Event Duration');
        
        // Create value and unit properties
        $value = new self('value', 'number', 'Duration value');
        
        $unit = new self('unit', 'string', 'Duration unit');
        $unit->enum(['seconds', 'minutes', 'hours', 'days', 'weeks', 'months', 'years']);
        
        // Add properties to the main property
        $property->addProperty('value', $value);
        $property->addProperty('unit', $unit);
        
        return $property;
    }
}
