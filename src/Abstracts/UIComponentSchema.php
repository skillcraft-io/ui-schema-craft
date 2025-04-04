<?php

namespace Skillcraft\UiSchemaCraft\Abstracts;

use Skillcraft\SchemaValidation\Contracts\ValidatorInterface;
use Skillcraft\UiSchemaCraft\Exceptions\ValidationException;
use Skillcraft\UiSchemaCraft\Exceptions\ValidationSchemaNotDefinedException;
use Skillcraft\UiSchemaCraft\Traits\HierarchicalArraySerializationTrait;
use Illuminate\Support\Str;

abstract class UIComponentSchema implements \Skillcraft\UiSchemaCraft\Contracts\UIComponentSchemaInterface
{
    use HierarchicalArraySerializationTrait;
    protected string $version = '1.0.0';
    protected ?array $validationSchema = null;
    protected string $component = '';
    
    /**
     * Define the main container properties that should be included in the hierarchical output
     * Each component can override this to specify its top-level structure
     * 
     * @var array
     */
    protected array $mainContainers = [];

    /**
     * Constructor
     * 
     * @param ValidatorInterface|null $validator Optional validator instance
     */
    public function __construct(
        protected readonly ?ValidatorInterface $validator = null
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

    // toArray() implementation is now provided by HierarchicalArraySerializationTrait

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
        // Skip validation if validation is disabled (null validator) or no validation schema exists
        if ($this->validator === null || !$this->getValidationSchema()) {
            return [
                'valid' => true,
                'errors' => null
            ];
        }

        $schema = $this->getValidationSchema();
        $rules = $schema ? $schema : [];
        
        try {
            $valid = $this->validator->validate($data, $rules);
            
            return [
                'valid' => $valid,
                'errors' => $valid ? null : new \Illuminate\Support\MessageBag(['validation' => ['Validation failed']])
            ];
        } catch (\Throwable $e) {
            // Log validation error but allow the process to continue
            if (app()->hasDebugModeEnabled()) {
                logger()->error("Validation error in " . static::class . ": " . $e->getMessage());
            }
            
            // Gracefully handle validation errors by returning valid=true
            return [
                'valid' => true,
                'errors' => null
            ];
        }
    }
    
    // Example data methods removed - use schema data with defaults/examples as needed



    /**
     * Get validation schema
     */
    protected function getValidationSchema(): ?array
    {
        return $this->validationSchema;
    }
}
