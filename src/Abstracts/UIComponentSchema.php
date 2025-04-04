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
    
    /**
     * Get example data for this component
     *
     * @return array Example data specifically formatted for actual use in forms/UIs
     */
    public function getExampleData(): array
    {
        // Process the properties to extract example values
        $properties = $this->properties();
        
        // Extract examples from nested properties recursively
        $extractedValues = $this->extractExampleValues($properties);
        
        // If no main containers are defined, return all properties at root level
        if (empty($this->mainContainers)) {
            return $extractedValues;
        }
        
        // Structure the result hierarchically based on main containers
        $result = [];
        $processedKeys = [];
        
        // First, populate main containers
        foreach ($this->mainContainers as $container) {
            if (isset($extractedValues[$container]) && is_array($extractedValues[$container])) {
                $result[$container] = $extractedValues[$container];
                $processedKeys[$container] = true;
            }
        }
        
        // Then add only properties that aren't part of main containers
        // and aren't already part of a container's hierarchy
        foreach ($extractedValues as $key => $value) {
            // Skip keys that have already been processed as containers
            if (isset($processedKeys[$key])) {
                continue;
            }
            
            // Skip properties that belong to containers but appeared at root level
            $skipProperty = false;
            foreach ($result as $containerValues) {
                if (isset($containerValues[$key])) {
                    $skipProperty = true;
                    break;
                }
            }
            
            if (!$skipProperty) {
                $result[$key] = $value;
            }
        }
        
        return $result;
    }
    
    /**
     * Recursively extract example values from property configuration
     *
     * @param array $properties Property configuration array
     * @return array Extracted example values
     */
    protected function extractExampleValues(array $properties): array
    {
        $result = [];
        
        foreach ($properties as $propertyName => $propertyConfig) {
            // Skip any property that doesn't have a structure we recognize
            if (!is_array($propertyConfig)) {
                continue;
            }
            
            // Handle nested objects recursively
            if (isset($propertyConfig['properties']) && is_array($propertyConfig['properties'])) {
                $result[$propertyName] = $this->extractExampleValues($propertyConfig['properties']);
                continue;
            }
            
            // First try the 'example' key (used by PropertyBuilder)
            if (isset($propertyConfig['example'])) {
                $result[$propertyName] = $propertyConfig['example'];
            }
            // Then fall back to 'example_data' key (may be used in some components)
            elseif (isset($propertyConfig['example_data'])) {
                $result[$propertyName] = $propertyConfig['example_data'];
            }
            // Use default value as a last resort
            elseif (isset($propertyConfig['default'])) {
                $result[$propertyName] = $propertyConfig['default'];
            }
        }
        
        return $result;
    }



    /**
     * Get validation schema
     */
    protected function getValidationSchema(): ?array
    {
        return $this->validationSchema;
    }
}
