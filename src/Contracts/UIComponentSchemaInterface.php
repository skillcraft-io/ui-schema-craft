<?php

namespace Skillcraft\UiSchemaCraft\Contracts;

interface UIComponentSchemaInterface
{
    /**
     * Get component properties
     */
    public function properties(): array;

    /**
     * Get component version
     */
    public function getVersion(): string;

    /**
     * Convert component to array
     */
    public function toArray(): array;

    /**
     * Get component type
     */
    public function getType(): string;

    /**
     * Get component name
     */
    public function getComponent(): string;

    /**
     * Validate component data
     * 
     * @param array $data Data to validate
     * @return array An array with keys: 'valid' (boolean) and 'errors' (MessageBag|null)
     */
    public function validate(array $data): array;
}
