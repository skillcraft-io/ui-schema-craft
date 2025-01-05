<?php

namespace Skillcraft\UiSchemaCraft\Validation;

class ValidationResult
{
    protected array $errors = [];

    public function addError(string $field, string $message): void
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        $this->errors[$field][] = $message;
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    public function toArray(): array
    {
        return [
            'errors' => $this->errors,
        ];
    }

    public function merge(ValidationResult $other): void
    {
        foreach ($other->toArray()['errors'] as $field => $messages) {
            foreach ($messages as $message) {
                $this->addError($field, $message);
            }
        }
    }
}
