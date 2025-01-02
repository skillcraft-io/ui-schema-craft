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

    public function addProperty(PropertyBuilder $property): self
    {
        $schema = $property->build();
        $this->properties[$schema['name']] = $schema;
        
        if (!empty($schema['rules'])) {
            $this->rules[$schema['name']] = $schema['rules'];
        }

        // Handle dependent rules
        if (!empty($schema['dependentRules'])) {
            foreach ($schema['dependentRules'] as $dependent) {
                $this->processRule($schema['name'], $dependent);
            }
        }

        return $this;
    }

    protected function processRule(string $field, array $rule): void
    {
        $type = $rule['type'] ?? 'field';
        $rules = $rule['rules'];
        $negate = $rule['negate'] ?? false;

        switch ($type) {
            case 'closure':
                $this->processClosureRule($field, $rule['closure'], $rules, $negate);
                break;

            case 'array':
                $this->processArrayRule($field, $rule['conditions'], $rules, $negate);
                break;

            case 'in_array':
                $this->processInArrayRule($field, $rule['field'], $rule['values'], $rules, $negate);
                break;

            case 'pattern':
                $this->processPatternRule($field, $rule['field'], $rule['pattern'], $rules, $negate);
                break;

            case 'compare':
                $this->processCompareRule($field, $rule['field'], $rule['operator'], $rule['value'], $rules, $negate);
                break;

            default:
                $this->processFieldRule($field, $rule['field'], $rule['value'], $rules, $negate);
                break;
        }
    }

    protected function processClosureRule(string $field, Closure $condition, array|string $rules, bool $negate): void
    {
        $this->rules[$field][] = Rule::when(
            function($input) use ($condition, $negate) {
                $result = $condition($input);
                return $negate ? !$result : $result;
            },
            $rules
        );
    }

    protected function processArrayRule(string $field, array $conditions, array|string $rules, bool $negate): void
    {
        $this->rules[$field][] = Rule::when(
            function($input) use ($conditions, $negate) {
                $matches = collect($conditions)->every(
                    fn($value, $field) => $input[$field] === $value
                );
                return $negate ? !$matches : $matches;
            },
            $rules
        );
    }

    protected function processInArrayRule(string $field, string $targetField, array $values, array|string $rules, bool $negate): void
    {
        $this->rules[$field][] = Rule::when(
            function($input) use ($targetField, $values, $negate) {
                $matches = in_array($input[$targetField], $values);
                return $negate ? !$matches : $matches;
            },
            $rules
        );
    }

    protected function processPatternRule(string $field, string $targetField, string $pattern, array|string $rules, bool $negate): void
    {
        $this->rules[$field][] = Rule::when(
            function($input) use ($targetField, $pattern, $negate) {
                $matches = preg_match($pattern, $input[$targetField]);
                return $negate ? !$matches : $matches;
            },
            $rules
        );
    }

    protected function processCompareRule(string $field, string $targetField, string $operator, mixed $value, array|string $rules, bool $negate): void
    {
        $this->rules[$field][] = Rule::when(
            function($input) use ($targetField, $operator, $value, $negate) {
                $fieldValue = $input[$targetField];
                $result = match ($operator) {
                    '=' => $fieldValue === $value,
                    '!=' => $fieldValue !== $value,
                    '>' => $fieldValue > $value,
                    '>=' => $fieldValue >= $value,
                    '<' => $fieldValue < $value,
                    '<=' => $fieldValue <= $value,
                    'contains' => str_contains($fieldValue, $value),
                    'starts_with' => str_starts_with($fieldValue, $value),
                    'ends_with' => str_ends_with($fieldValue, $value),
                    default => false
                };
                return $negate ? !$result : $result;
            },
            $rules
        );
    }

    protected function processFieldRule(string $field, string $targetField, mixed $value, array|string $rules, bool $negate): void
    {
        $this->rules[$field][] = Rule::when(
            function($input) use ($targetField, $value, $negate) {
                $matches = $input[$targetField] === $value;
                return $negate ? !$matches : $matches;
            },
            $rules
        );
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
            'errors' => $validator->errors()->toArray()
        ];
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
}
