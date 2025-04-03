<?php

namespace Skillcraft\ValidationCraft;

class ValidationSchema
{
    private array $rules;

    public function __construct(array $rules)
    {
        $this->rules = $rules;
    }

    public function getRules(): array
    {
        return $this->rules;
    }
}
