<?php

namespace Skillcraft\UiSchemaCraft\Schema;

class Property
{
    protected string|array $type;
    protected string $name;
    protected mixed $default = null;
    protected array $attributes = [];

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function string(string $name): static
    {
        $instance = new static($name);
        $instance->type = 'string';
        return $instance;
    }

    public static function number(string $name): static
    {
        $instance = new static($name);
        $instance->type = 'number';
        return $instance;
    }

    public static function boolean(string $name): static
    {
        $instance = new static($name);
        $instance->type = 'boolean';
        return $instance;
    }

    public static function object(string $name): static
    {
        $instance = new static($name);
        $instance->type = 'object';
        return $instance;
    }

    public static function array(string $name): static
    {
        $instance = new static($name);
        $instance->type = 'array';
        return $instance;
    }

    public static function timeRange(string $name): static
    {
        $instance = new static($name);
        $instance->type = 'object';
        $instance->attributes['timeRange'] = true;
        return $instance;
    }

    public function default(mixed $value): static
    {
        $this->default = $value;
        return $this;
    }

    public function min(int|float $value): static
    {
        $this->attributes['minimum'] = $value;
        return $this;
    }

    public function max(int|float $value): static
    {
        $this->attributes['maximum'] = $value;
        return $this;
    }

    public function format(string $format): static
    {
        $this->attributes['format'] = $format;
        return $this;
    }

    public function enum(array $values): static
    {
        $this->attributes['enum'] = $values;
        return $this;
    }

    public function pattern(string $pattern): static
    {
        $this->attributes['pattern'] = $pattern;
        return $this;
    }

    public function required(bool $required = true): static
    {
        if ($required) {
            $this->attributes['required'] = true;
        } else {
            unset($this->attributes['required']);
        }
        return $this;
    }

    public function nullable(bool $nullable = true): static
    {
        if ($nullable && !is_array($this->type)) {
            $this->type = ['null', $this->type];
        } elseif (!$nullable && is_array($this->type)) {
            $this->type = array_filter($this->type, fn($t) => $t !== 'null');
            if (count($this->type) === 1) {
                $this->type = reset($this->type);
            }
        }
        return $this;
    }

    public function description(string $description): static
    {
        $this->attributes['description'] = $description;
        return $this;
    }

    public function items(array|Property $schema): static
    {
        if ($this->type !== 'array') {
            throw new \InvalidArgumentException('Items can only be set on array type properties');
        }

        $this->attributes['items'] = $schema instanceof Property ? $schema->toArray() : $schema;
        return $this;
    }

    public function properties(array|PropertyBuilder $properties): static
    {
        if ($this->type !== 'object') {
            throw new \InvalidArgumentException('Properties can only be set on object type properties');
        }

        if ($properties instanceof PropertyBuilder) {
            $this->attributes['properties'] = $properties->toArray();
        } else {
            $this->attributes['properties'] = $properties;
        }
        
        return $this;
    }

    public function withBuilder(callable $callback): static
    {
        if ($this->type !== 'object') {
            throw new \InvalidArgumentException('Builder can only be used with object type properties');
        }

        $builder = new PropertyBuilder();
        $callback($builder);
        $this->attributes['properties'] = $builder->toArray();
        
        return $this;
    }

    public function toArray(): array
    {
        $schema = [
            'type' => $this->type,
        ];

        if ($this->default !== null) {
            $schema['default'] = $this->default;
        }

        return array_merge($schema, $this->attributes);
    }

    public function getName(): string
    {
        return $this->name;
    }
}
