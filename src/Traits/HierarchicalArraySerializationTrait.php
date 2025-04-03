<?php

namespace Skillcraft\UiSchemaCraft\Traits;

use Skillcraft\UiSchemaCraft\Interfaces\ComposableInterface;

/**
 * Trait for hierarchical array serialization
 * 
 * This trait provides a standardized implementation of toArray() that preserves
 * the hierarchical structure of properties, maintaining nested objects and arrays
 * as defined in the property definitions.
 * 
 * @package Skillcraft\UiSchemaCraft\Traits
 */
trait HierarchicalArraySerializationTrait
{
    /**
     * Property values storage
     * 
     * @var array
     */
    protected array $propertyValues = [];
    
    /**
     * Properties that should not be included in the serialized output
     * 
     * @var array
     */
    protected array $hiddenProperties = [
        'validator',
    ];
    
    /**
     * Convert the component to a hierarchical array preserving structure
     * 
     * @return array
     */
    public function toArray(): array
    {
        // Build base schema with core component info
        $schema = $this->buildBaseSchema();
        
        // Process properties and maintain their hierarchy
        $schema = $this->processPropertiesHierarchy($schema);
        
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
            'component' => $this->getComponent(),
        ];
    }
    
    /**
     * Process properties while maintaining their hierarchical structure
     * 
     * @param array $schema Current schema
     * @return array Updated schema with structured properties
     */
    protected function processPropertiesHierarchy(array $schema): array
    {
        $schema['properties'] = $this->transformPropertiesStructure($this->properties());
        return $schema;
    }
    
    /**
     * Transform property definitions into a hierarchical structure
     * 
     * @param array $properties Property definitions
     * @return array Hierarchical property structure
     */
    protected function transformPropertiesStructure(array $properties): array
    {
        $result = [];
        
        foreach ($properties as $key => $config) {
            // Skip hidden properties
            if (in_array($key, $this->hiddenProperties)) {
                continue;
            }
            
            // Handle object properties - these have nested properties
            if (isset($config['type']) && $config['type'] === 'object' && isset($config['properties'])) {
                $result[$key] = [
                    'type' => 'object',
                ];
                
                // Process nested properties recursively
                $result[$key] = array_merge(
                    $result[$key], 
                    $this->extractBasicConfig($config),
                    ['properties' => $this->transformPropertiesStructure($config['properties'])]
                );
            } 
            // Handle array properties with object items
            elseif (isset($config['type']) && $config['type'] === 'array' && 
                    isset($config['items']) && isset($config['items']['type']) && 
                    $config['items']['type'] === 'object') {
                
                $result[$key] = [
                    'type' => 'array',
                ];
                
                // Process items property
                $itemsConfig = $config['items'];
                $itemProperties = $itemsConfig['properties'] ?? [];
                
                $result[$key] = array_merge(
                    $result[$key],
                    $this->extractBasicConfig($config),
                    [
                        'items' => array_merge(
                            ['type' => $itemsConfig['type']],
                            isset($itemsConfig['properties']) 
                                ? ['properties' => $this->transformPropertiesStructure($itemProperties)]
                                : []
                        )
                    ]
                );
            } 
            // Basic property types
            else {
                $result[$key] = array_merge(
                    ['type' => $config['type'] ?? 'string'],
                    $this->extractBasicConfig($config)
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Extract basic configuration from a property definition
     * 
     * @param array $config Property configuration
     * @return array Basic configuration values
     */
    protected function extractBasicConfig(array $config): array
    {
        $basicConfig = [];
        
        // Extract common property attributes that should be included
        $attributes = ['default', 'description', 'example', 'format', 'enum', 'required'];
        
        foreach ($attributes as $attr) {
            if (isset($config[$attr])) {
                $basicConfig[$attr] = $config[$attr];
            }
        }
        
        // Copy any other properties from the original configuration that should be preserved
        $preserveKeys = ['title', 'minimum', 'maximum', 'pattern', 'minLength', 'maxLength'];
        foreach ($preserveKeys as $key) {
            if (isset($config[$key])) {
                $basicConfig[$key] = $config[$key];
            }
        }
        
        return $basicConfig;
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
        return $schema;
    }
    
    /**
     * Add child components to the schema if this is a composable component
     * 
     * @param array $schema Current schema
     * @return array Schema with children added
     */
    protected function addChildrenToSchema(array $schema): array
    {
        if ($this instanceof ComposableInterface && method_exists($this, 'getChildren')) {
            $children = $this->getChildren();
            if (!empty($children)) {
                $schema['children'] = [];
                
                foreach ($children as $key => $child) {
                    $schema['children'][$key] = $child->toArray();
                }
            }
        }
        
        return $schema;
    }
    
    /**
     * Get a property value with fallback to default
     * 
     * @param string $key Property key
     * @param mixed $default Default value if property not set
     * @return mixed Property value or default
     */
    protected function getProperty(string $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, $this->propertyValues)) {
            return $this->propertyValues[$key];
        }
        
        $properties = $this->properties();
        
        return $properties[$key]['default'] ?? $default;
    }
    
    /**
     * Set a property value
     * 
     * @param string $key Property key
     * @param mixed $value Property value
     * @return self
     */
    public function setProperty(string $key, mixed $value): self
    {
        $this->propertyValues[$key] = $value;
        return $this;
    }
}
