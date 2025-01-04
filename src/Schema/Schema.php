<?php

namespace Skillcraft\UiSchemaCraft\Schema;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Closure;

class Schema
{
    protected array $properties = [];
    protected array $rules = [];
    protected array $messages = [];
    protected array $attributes = [];

    public function addProperty(Property|PropertyBuilder $property): self
    {
        $schema = $property instanceof PropertyBuilder ? $property->build() : [
            'name' => $property->getName(),
            'schema' => $property->toArray(),
            'rules' => $property->getRules(),
        ];

        $this->properties[$schema['name']] = $schema['schema'];
        
        if (!empty($schema['rules'])) {
            $this->rules[$schema['name']] = $schema['rules'];
        }

        if (!empty($schema['schema']['conditionalRules'])) {
            if (!isset($this->rules[$schema['name']])) {
                $this->rules[$schema['name']] = [];
            }

            foreach ($schema['schema']['conditionalRules'] as $rule) {
                if (isset($rule['type']) && $rule['type'] === 'closure') {
                    $this->rules[$schema['name']][] = Rule::when(
                        $rule['closure'],
                        is_array($rule['rules']) ? $rule['rules'] : explode('|', $rule['rules'])
                    );
                } elseif (is_array($rule['field'])) {
                    $this->rules[$schema['name']][] = Rule::when(
                        fn($input) => collect($rule['field'])->every(
                            fn($value, $field) => $input[$field] === $value
                        ),
                        is_array($rule['rules']) ? $rule['rules'] : explode('|', $rule['rules'])
                    );
                } elseif (isset($rule['value']['pattern'])) {
                    $this->rules[$schema['name']][] = Rule::when(
                        fn($input) => preg_match($rule['value']['pattern'], $input[$rule['field']]),
                        is_array($rule['rules']) ? $rule['rules'] : explode('|', $rule['rules'])
                    );
                } elseif (isset($rule['value']['operator'])) {
                    $this->rules[$schema['name']][] = Rule::when(
                        fn($input) => $this->compareValues($input[$rule['field']], $rule['value']['operator'], $rule['value']['value']),
                        is_array($rule['rules']) ? $rule['rules'] : explode('|', $rule['rules'])
                    );
                } else {
                    $this->rules[$schema['name']][] = Rule::when(
                        fn($input) => $input[$rule['field']] === $rule['value'],
                        is_array($rule['rules']) ? $rule['rules'] : explode('|', $rule['rules'])
                    );
                }
            }
        }

        return $this;
    }

    protected function compareValues($a, string $operator, $b): bool
    {
        return match($operator) {
            '=' => $a === $b,
            '!=' => $a !== $b,
            '>' => $a > $b,
            '>=' => $a >= $b,
            '<' => $a < $b,
            '<=' => $a <= $b,
            default => false,
        };
    }

    public function validate(array $data): array
    {
        $validator = Validator::make(
            $data,
            $this->rules,
            $this->messages,
            $this->attributes
        );

        return [
            'valid' => !$validator->fails(),
            'errors' => $validator->errors()->toArray(),
        ];
    }

    public function setMessages(array $messages): self
    {
        $this->messages = $messages;
        return $this;
    }

    public function setAttributes(array $attributes): self
    {
        $this->attributes = $attributes;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'type' => 'object',
            'properties' => $this->properties,
            'required' => array_keys(array_filter($this->properties, fn($prop) => ($prop['required'] ?? false) === true)),
        ];
    }

    public function getProperties(): array
    {
        return $this->properties;
    }

    public function getRules(): array
    {
        return $this->rules;
    }

    public function getMessages(): array
    {
        return $this->messages;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function withMessages(array $messages): self
    {
        $this->messages = array_merge($this->messages, $messages);
        return $this;
    }

    public function withAttributes(array $attributes): self
    {
        $this->attributes = array_merge($this->attributes, $attributes);
        return $this;
    }
}
