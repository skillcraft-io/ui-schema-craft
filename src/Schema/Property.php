<?php

namespace Skillcraft\UiSchemaCraft\Schema;

use Skillcraft\UiSchemaCraft\Schema\Traits\TimeTrait;

class Property
{
    use TimeTrait;

    protected string $name;
    protected string|array $type;
    protected ?string $description;
    protected mixed $default = null;
    protected array $rules = [];
    protected array $attributes = [];
    protected array $properties = [];
    protected array $items = [];
    protected ?string $format = null;
    protected ?float $minimum = null;
    protected ?float $maximum = null;
    protected ?string $pattern = null;
    protected ?string $reference = null;
    protected bool $isRequired = false;
    protected array $conditionalRules = [];

    public function __construct(string $name, string|array $type, ?string $description = null)
    {
        $this->name = $name;
        $this->type = is_array($type) ? array_map('strval', $type) : $type;
        $this->description = $description;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string|array
    {
        return $this->type;
    }

    public function getPrimaryType(): string
    {
        return is_array($this->type) ? $this->type[0] : $this->type;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function default(mixed $value): self
    {
        return $this->setDefault($value);
    }

    public function setDefault(mixed $value): self
    {
        $this->default = $value;
        return $this;
    }

    public function getDefault(): mixed
    {
        return $this->default;
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
                if ($property instanceof Property) {
                    $array['properties'][$name] = $property->toArray();
                } else {
                    $array['properties'][$name] = $property;
                }
            }
        }

        if (!empty($this->items)) {
            $array['items'] = $this->items instanceof Property ? $this->items->toArray() : $this->items;
        }

        if (!empty($this->conditionalRules)) {
            $array['conditionalRules'] = $this->conditionalRules;
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
        if ($descriptionOrFormat === null || str_contains($descriptionOrFormat, ':') || str_contains($descriptionOrFormat, '-')) {
            // Handle as a format (old behavior)
            $property = new self($name, ['object', 'null']);
            $property->addAttribute('timeRange', true);
            $property->format($descriptionOrFormat ?? 'Y-m-d');
            return $property;
        } else {
            // Handle as a description (new behavior)
            return static::timeRangeStatic($name, $descriptionOrFormat);
        }
    }

    public static function dateRange(string $name, ?string $description = null): self
    {
        return static::dateRangeStatic($name, $description);
    }

    public static function time(string $name, ?string $description = null): self
    {
        return static::timeStatic($name, $description);
    }

    public static function duration(string $name, ?string $description = null): self
    {
        return static::durationStatic($name, $description);
    }
}
