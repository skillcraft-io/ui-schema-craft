<?php

namespace Skillcraft\ValidationCraft\Contracts;

interface ValidatorInterface
{
    /**
     * Validate data against a validation schema
     *
     * @param array $data
     * @param mixed $schema
     * @return array
     */
    public function validate(array $data, mixed $schema): array;
}
