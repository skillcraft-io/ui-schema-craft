<?php

namespace Skillcraft\UiSchemaCraft\Traits;

use Skillcraft\UiSchemaCraft\Interfaces\ComposableInterface;
use Skillcraft\UiSchemaCraft\Schema\Property;
use Skillcraft\UiSchemaCraft\Schema\PropertyBuilder;

/**
 * Trait for hierarchical array serialization
 * 
 * This trait provides a standardized implementation of toArray() that preserves
 * the hierarchical structure of properties, maintaining nested objects and arrays.
 * It keeps container properties organized and eliminates duplication of nested properties.
 * 
 * Optimized for UI rendering and form generation with clean, organized structure.
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
    protected array $hiddenProperties = [
        'validator',
    ];
    
    /**
     * Property values storage
     * 
     * @var array
     */
    protected array $propertyValues = [];
    
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
        // Get component properties
        $properties = $this->properties();
        
        // Extract top-level objects for hierarchy
        $hierarchicalProperties = $this->extractHierarchicalStructure($properties);
        
        // Only use hierarchical properties in the output
        $schema['properties'] = $hierarchicalProperties;
        
        return $schema;
    }
    
    /**
     * Extract only hierarchical properties (objects that contain other properties)
     * 
     * @param array $properties All component properties
     * @return array Hierarchical properties only
     */
    protected function extractHierarchicalStructure(array $properties): array
    {
        $hierarchical = [];
        
        // First check if component has defined main containers
        if (!empty($this->mainContainers)) {
            // Only include the explicitly defined main containers
            foreach ($this->mainContainers as $containerKey) {
                if (isset($properties[$containerKey]) && !in_array($containerKey, $this->hiddenProperties)) {
                    $hierarchical[$containerKey] = $properties[$containerKey];
                }
            }
            
            // If mainContainers is defined but none were found, fall back to auto-detection
            if (empty($hierarchical)) {
                return $this->autoDetectContainers($properties);
            }
            
            return $this->transformPropertiesStructure($hierarchical);
        }
        
        // No explicit mainContainers defined, auto-detect container objects
        return $this->autoDetectContainers($properties);
    }
    
    /**
     * Auto-detect container objects in properties
     * 
     * @param array $properties Component properties
     * @return array Hierarchical properties
     */
    protected function autoDetectContainers(array $properties): array
    {
        $hierarchical = [];
        $alreadyProcessed = [];
        
        // First pass: identify all objects with nested properties
        foreach ($properties as $key => $config) {
            // Skip hidden properties
            if (in_array($key, $this->hiddenProperties)) {
                continue;
            }
            
            // Check if this is an object with nested properties
            $isObjectWithProperties = 
                (is_array($config) && isset($config['type']) && $config['type'] === 'object' && isset($config['properties'])) ||
                ($config instanceof Property && $config->getType() === 'object' && !empty($config->getProperties()));
                
            if ($isObjectWithProperties) {
                $hierarchical[$key] = $config;
                $alreadyProcessed[] = $key;
                
                // Add nested properties to the processed list to avoid duplication
                $nestedProps = is_array($config) ? ($config['properties'] ?? []) : $config->getProperties();
                $this->trackNestedProperties($nestedProps, $alreadyProcessed);
            }
        }
        
        // Remove any properties that are already included inside other containers
        foreach ($alreadyProcessed as $processedKey) {
            if (isset($hierarchical[$processedKey]) && in_array($processedKey, $alreadyProcessed, true)) {
                unset($hierarchical[$processedKey]);
            }
        }
        
        // If still no hierarchical properties found, use the first property as fallback
        if (empty($hierarchical) && !empty($properties)) {
            // Get first property as fallback
            $firstKey = array_key_first($properties);
            $hierarchical[$firstKey] = $properties[$firstKey];
        }
        
        return $this->transformPropertiesStructure($hierarchical);
    }
    
    /**
     * Track nested property names to avoid duplication
     * 
     * @param array $properties Nested properties to track
     * @param array &$processed Reference to array of processed property names
     * @return void
     */
    protected function trackNestedProperties(array $properties, array &$processed): void
    {
        foreach ($properties as $key => $prop) {
            $processed[] = $key;
            
            // Recursively track nested properties
            if (is_array($prop) && isset($prop['properties'])) {
                $this->trackNestedProperties($prop['properties'], $processed);
            } elseif ($prop instanceof Property && !empty($prop->getProperties())) {
                $this->trackNestedProperties($prop->getProperties(), $processed);
            }
        }
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
            
            // Handle Property objects directly
            if ($config instanceof Property) {
                $result[$key] = $this->convertPropertyToArray($config);
                continue;
            }
            
            // Handle object properties - these have nested properties
            if (isset($config['type']) && $config['type'] === 'object' && isset($config['properties'])) {
                $result[$key] = [
                    'type' => 'object',
                ];
                
                // Process nested properties recursively
                $nestedProperties = $this->transformPropertiesStructure($config['properties']);
                
                $result[$key] = array_merge(
                    $result[$key], 
                    $this->extractBasicConfig($config),
                    ['properties' => $nestedProperties]
                );
            } 
            // Handle array properties with object items
            elseif (isset($config['type']) && $config['type'] === 'array' && 
                    isset($config['items'])) {
                    
                $result[$key] = [
                    'type' => 'array',
                ];
                
                // Process items - handle Property objects too
                $items = $config['items'];
                
                // Handle Property objects in items
                if ($items instanceof Property) {
                    $items = $this->convertPropertyToArray($items);
                }
                
                // For object items with properties
                if (is_array($items) && isset($items['type']) && $items['type'] === 'object' && isset($items['properties'])) {
                    $itemProperties = $items['properties'];
                    
                    // Process nested properties in items
                    $nestedItemProperties = $this->transformPropertiesStructure($itemProperties);
                    
                    $items = array_merge(
                        ['type' => $items['type']],
                        $this->extractBasicConfig($items),
                        ['properties' => $nestedItemProperties]
                    );
                }
                
                $result[$key] = array_merge(
                    $result[$key],
                    $this->extractBasicConfig($config),
                    ['items' => $items]
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
     * Convert a Property object to array and ensure nested properties are converted too
     *
     * @param Property $property Property object to convert
     * @return array Fully converted property array
     */
    protected function convertPropertyToArray(Property $property): array
    {
        $data = $property->toArray();
        
        // We don't need the name in the result since it's used as the array key
        unset($data['name']);
        
        // Recursively process nested properties
        if (isset($data['properties']) && is_array($data['properties'])) {
            $data['properties'] = $this->transformPropertiesStructure($data['properties']);
        }
        
        // Handle items that might contain Property objects
        if (isset($data['items'])) {
            if ($data['items'] instanceof Property) {
                $data['items'] = $this->convertPropertyToArray($data['items']);
            } elseif (is_array($data['items']) && isset($data['items']['properties'])) {
                $data['items']['properties'] = $this->transformPropertiesStructure($data['items']['properties']);
            }
        }
        
        return $data;
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
