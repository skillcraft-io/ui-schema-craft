<?php

namespace Skillcraft\UiSchemaCraft\Schema\Traits;

trait ValidationTrait
{
    protected array $rules = [];
    protected array $messages = [];

    /**
     * Add a validation rule
     */
    public function rule(string $rule): self
    {
        $this->rules[] = $rule;
        return $this;
    }

    /**
     * Add multiple validation rules
     */
    public function rules(array $rules): self
    {
        $this->rules = array_merge($this->rules, $rules);
        return $this;
    }

    /**
     * Add a validation message
     */
    public function message(string $rule, string $message): self
    {
        $this->messages[$rule] = $message;
        return $this;
    }

    /**
     * Add multiple validation messages
     */
    public function messages(array $messages): self
    {
        $this->messages = array_merge($this->messages, $messages);
        return $this;
    }

    /**
     * Add a required rule
     */
    public function required(): self
    {
        return $this->rule('required');
    }

    /**
     * Add a nullable rule
     */
    public function nullable(): self
    {
        return $this->rule('nullable');
    }

    /**
     * Add a conditional validation rule
     */
    public function when(string|array|\Closure $field, mixed $value = null, array|string $rules = []): self
    {
        if ($field instanceof \Closure) {
            $this->rules[] = ['when' => $field, 'rules' => $rules];
        } else {
            $this->rules[] = ['when' => [$field => $value], 'rules' => $rules];
        }
        return $this;
    }

    /**
     * Add a required with validation rule
     */
    public function requiredWith(array $fields): self
    {
        return $this->rule('required_with:' . implode(',', $fields));
    }

    /**
     * Add a required without validation rule
     */
    public function requiredWithout(array $fields): self
    {
        return $this->rule('required_without:' . implode(',', $fields));
    }

    /**
     * Add a required if validation rule
     */
    public function requiredIf(string $field, mixed $value): self
    {
        return $this->rule("required_if:$field,$value");
    }

    /**
     * Add a prohibited if validation rule
     */
    public function prohibitedIf(string $field, mixed $value): self
    {
        return $this->rule("prohibited_if:$field,$value");
    }

    /**
     * Get all validation rules
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * Get all validation messages
     */
    public function getMessages(): array
    {
        return $this->messages;
    }
}
