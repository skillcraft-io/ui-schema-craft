<?php

namespace Skillcraft\UiSchemaCraft\Schema\Traits;

use Skillcraft\UiSchemaCraft\Schema\Property;

trait ValidationTrait
{
    /**
     * Add a conditional validation rule.
     */
    public function when(string|array|\Closure $field, mixed $value = null, array|string $rules = []): static
    {
        if (!isset($this->attributes['conditionalRules'])) {
            $this->attributes['conditionalRules'] = [];
        }

        if ($field instanceof \Closure) {
            $this->attributes['conditionalRules'][] = [
                'type' => 'closure',
                'closure' => $field,
                'rules' => is_array($value) ? $value : [$value]
            ];
        } elseif (is_array($field)) {
            $this->attributes['conditionalRules'][] = [
                'type' => 'array',
                'field' => $field,
                'rules' => is_array($value) ? $value : [$value]
            ];
        } else {
            $this->attributes['conditionalRules'][] = [
                'type' => 'field',
                'field' => $field,
                'value' => $value,
                'rules' => is_array($rules) ? $rules : [$rules]
            ];
        }

        return $this;
    }

    /**
     * Add a pattern-based conditional validation rule.
     */
    public function whenMatches(string $field, string $pattern, array|string $rules): static
    {
        if (!isset($this->attributes['conditionalRules'])) {
            $this->attributes['conditionalRules'] = [];
        }

        $this->attributes['conditionalRules'][] = [
            'type' => 'pattern',
            'field' => $field,
            'value' => ['pattern' => $pattern],
            'rules' => is_array($rules) ? $rules : [$rules]
        ];

        return $this;
    }

    /**
     * Add a comparison-based conditional validation rule.
     */
    public function whenCompare(string $field, string $operator, mixed $value, array|string $rules): static
    {
        if (!isset($this->attributes['conditionalRules'])) {
            $this->attributes['conditionalRules'] = [];
        }

        $this->attributes['conditionalRules'][] = [
            'type' => 'comparison',
            'field' => $field,
            'value' => [
                'operator' => $operator,
                'value' => $value
            ],
            'rules' => is_array($rules) ? $rules : [$rules]
        ];

        return $this;
    }

    /**
     * Add a required with validation rule.
     */
    public function requiredWith(array $fields): static
    {
        if (!isset($this->attributes['conditionalRules'])) {
            $this->attributes['conditionalRules'] = [];
        }

        $this->attributes['conditionalRules'][] = [
            'type' => 'requiredWith',
            'field' => $fields,
            'rules' => ['required']
        ];

        return $this;
    }

    /**
     * Add a required without validation rule.
     */
    public function requiredWithout(array $fields): static
    {
        if (!isset($this->attributes['conditionalRules'])) {
            $this->attributes['conditionalRules'] = [];
        }

        $this->attributes['conditionalRules'][] = [
            'type' => 'requiredWithout',
            'field' => $fields,
            'rules' => ['required']
        ];

        return $this;
    }

    /**
     * Add a required if validation rule.
     */
    public function requiredIf(string $field, mixed $value): static
    {
        if (!isset($this->attributes['conditionalRules'])) {
            $this->attributes['conditionalRules'] = [];
        }

        $this->attributes['conditionalRules'][] = [
            'type' => 'field',
            'field' => $field,
            'value' => $value,
            'rules' => ['required']
        ];

        return $this;
    }

    /**
     * Add a prohibited if validation rule.
     */
    public function prohibitedIf(string $field, mixed $value): static
    {
        if (!isset($this->attributes['conditionalRules'])) {
            $this->attributes['conditionalRules'] = [];
        }

        $this->attributes['conditionalRules'][] = [
            'type' => 'field',
            'field' => $field,
            'value' => $value,
            'rules' => ['prohibited']
        ];

        return $this;
    }
}
