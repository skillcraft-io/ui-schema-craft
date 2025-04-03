<?php

namespace Skillcraft\UiSchemaCraft\Traits;

use Skillcraft\UiSchemaCraft\Interfaces\ComposableInterface;

/**
 * Trait for simplified array serialization
 * 
 * This trait provides a standardized implementation of toArray() that outputs
 * a simplified array structure suitable for frontend frameworks like Vueform.
 * It combines schema definition with actual property values.
 * 
 * @package Skillcraft\UiSchemaCraft\Traits
 */
trait SimplifiedArraySerializationTrait
{
    /**
     * Property values storage
     * 
     * @var array
     */
    protected array $propertyValues = [];
    
    /**
     * Properties that should not be included in the simplified output
     * 
     * @var array
     */
    protected array $hiddenProperties = [
        'validator',
    ];
    
    /**
     * Convert the component to a simplified array
     * 
     * @return array
     */
    public function toArray(): array
    {
        // Build base schema with core component info
        $schema = $this->buildBaseSchema();
        
        // Add standard property values
        $schema = $this->addPropertiesToSchema($schema);
        
        // Allow component-specific customizations
        $schema = $this->extendSchema($schema);
        
        // Add child components if applicable
        $schema = $this->addChildrenToSchema($schema);
        
        return $schema;
    }
    
    /**
     * Build the base schema with core component information
     * 
     * @return array Base schema structure
     */
    protected function buildBaseSchema(): array
    {
        return [
            'type' => $this->getType(),
            'version' => $this->getVersion(),
            'component' => $this->component,
            
            // Include properties schema for reference/validation
            'properties' => $this->properties(),
        ];
    }
    
    /**
     * Add standard property values to the schema
     * 
     * @param array $schema Current schema
     * @return array Updated schema with properties
     */
    protected function addPropertiesToSchema(array $schema): array
    {
        $properties = $this->properties();
        foreach (array_keys($properties) as $key) {
            // Skip properties that shouldn't be directly exposed
            if (in_array($key, $this->hiddenProperties)) {
                continue;
            }
            
            $schema[$key] = $this->getProperty($key);
        }
        
        return $schema;
    }
    
    /**
     * Extension point for component-specific schema customizations
     * 
     * Components can override this method to add their own
     * specialized structure without overriding the entire toArray() method
     * 
     * @param array $schema Current schema
     * @return array Updated schema with component-specific extensions
     */
    protected function extendSchema(array $schema): array
    {
        // Base implementation does nothing
        return $schema;
    }
    
    /**
     * Add child components to the schema if component implements ComposableInterface
     * 
     * @param array $schema Current schema
     * @return array Updated schema with children
     */
    protected function addChildrenToSchema(array $schema): array
    {
        // Add child components if this component is composable
        if ($this instanceof ComposableInterface && method_exists($this, 'hasChildren') && $this->hasChildren()) {
            $schema['children'] = [];
            foreach ($this->getChildren() as $child) {
                $schema['children'][] = $child->toArray();
            }
        }
        
        return $schema;
    }
    
    /**
     * Set a property value
     *
     * @param string $key The property key
     * @param mixed $value The property value
     * @return self For method chaining
     */
    public function setProperty(string $key, $value): self
    {
        $this->propertyValues[$key] = $value;
        return $this;
    }
    
    /**
     * Get a property value with fallback to default
     *
     * @param string $key The property key
     * @param mixed $default Optional default value if not set
     * @return mixed The property value
     */
    public function getProperty(string $key, $default = null): mixed
    {
        // First check if we have a direct value set
        if (array_key_exists($key, $this->propertyValues)) {
            return $this->propertyValues[$key];
        }
        
        // Otherwise, get the default value from the schema definition
        $properties = $this->properties();
        if (isset($properties[$key]) && isset($properties[$key]['default'])) {
            return $properties[$key]['default'];
        }
        
        // Fall back to provided default
        return $default;
    }
}
