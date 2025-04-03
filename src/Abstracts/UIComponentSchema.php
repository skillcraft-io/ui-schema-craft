<?php

namespace Skillcraft\UiSchemaCraft\Abstracts;

use Skillcraft\SchemaValidation\Contracts\ValidatorInterface;
use Skillcraft\UiSchemaCraft\Exceptions\ValidationException;
use Skillcraft\UiSchemaCraft\Exceptions\ValidationSchemaNotDefinedException;
use Illuminate\Support\Str;

abstract class UIComponentSchema implements \Skillcraft\UiSchemaCraft\Contracts\UIComponentSchemaInterface
{
    protected string $version = '1.0.0';
    protected ?array $validationSchema = null;
    protected string $component = '';

    public function __construct(
        protected readonly ValidatorInterface $validator
    ) {}

    /**
     * Get component properties
     */
    abstract public function properties(): array;

    /**
     * Get component version
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * Convert component to array
     */
    public function toArray(): array
    {
        return [
            'type' => $this->getType(),
            'version' => $this->version,
            'component' => $this->getComponent(),
            'properties' => $this->properties()
        ];
    }

    /**
     * Get component type
     */
    public function getType(): string
    {
        // If type is explicitly set, use it
        if (isset($this->type)) {
            return $this->type;
        }

        // Otherwise, derive from class name
        return Str::kebab(
            str_replace('UIComponent', '', 
                Str::beforeLast(class_basename($this), 'Schema')
            )
        );
    }

    /**
     * Get component name
     */
    public function getComponent(): string
    {
        return $this->component;
    }

    /**
     * Validate component state
     * 
     * @param array $data Data to validate
     * @return array An array with keys: 'valid' (boolean) and 'errors' (MessageBag|null)
     */
    public function validate(array $data): array
    {
        if (!$this->getValidationSchema()) {
            return [
                'valid' => true,
                'errors' => null
            ];
        }

        $schema = $this->getValidationSchema();
        $rules = $schema ? $schema : [];
        $valid = $this->validator->validate($data, $rules);
        
        return [
            'valid' => $valid,
            'errors' => $valid ? null : new \Illuminate\Support\MessageBag(['validation' => ['Validation failed']])
        ];
    }

    /**
     * Get validation schema
     */
    protected function getValidationSchema(): ?array
    {
        return $this->validationSchema;
    }
}
