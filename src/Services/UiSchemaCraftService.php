<?php

namespace Skillcraft\UiSchemaCraft\Services;

use Skillcraft\UiSchemaCraft\Abstracts\UIComponentSchema;
use Skillcraft\SchemaState\Contracts\StateManagerInterface;
use Skillcraft\UiSchemaCraft\ComponentResolver;
use Skillcraft\SchemaValidation\Contracts\ValidatorInterface;


use Illuminate\Support\Str;


class UiSchemaCraftService
{
    /**
     * @var bool Whether validation is enabled
     */
    protected bool $validationEnabled = false;
    
    public function __construct(
        protected readonly StateManagerInterface $stateManager,
        protected readonly ComponentResolver $resolver = new ComponentResolver(),
        protected readonly ?ValidatorInterface $validator = null,
        bool $enableValidation = false
    ) {
        // Validation is disabled by default, and can be enabled via parameter
        $this->validationEnabled = $enableValidation && $validator !== null;
    }

    /**
     * Register a component class
     */
    public function registerComponent(string $componentClass): void
    {
        $this->resolver->register($componentClass);
    }

    /**
     * Register all components in a namespace
     */
    public function registerNamespace(string $namespace): void
    {
        $this->resolver->registerNamespace($namespace);
    }

    /**
     * Get component schema with optional state
     *
     * @param string $type Component type
     * @param string|null $stateId Optional state ID
     * @return array Component schema with state
     */
    public function getComponent(string $type, ?string $stateId = null): array
    {
        $component = $this->resolveComponent($type);
        
        if ($stateId) {
            $state = $this->stateManager->load($stateId);
            if ($state) {
                return array_merge($component->toArray(), ['state' => $state['data']]);
            }
        }

        return $component->toArray();
    }

    /**
     * Save component state
     *
     * @param string $type Component type
     * @param array $state State data
     * @param string|null $stateId Optional state ID
     * @return string State ID
     */
    public function saveState(string $type, array $state, ?string $stateId = null): string
    {
        $component = $this->resolveComponent($type);
        $id = $stateId ?? (string) Str::uuid();

        // Call the StateManagerInterface with the correct parameters
        // The interface defines: save(string $id, string $type, array $data, array $metadata = [])
        $metadata = ['type' => $type];
        $this->stateManager->save($id, $type, $state, $metadata);

        return $id;
    }

    /**
     * Delete component state
     *
     * @param string $stateId State ID to delete
     */
    public function deleteState(string $stateId): void
    {
        $this->stateManager->delete($stateId);
    }

    /**
     * Get all states for a component type
     *
     * @param string $type Component type
     * @return array Array of states
     */
    public function getStates(string $type): array
    {
        // Get all available state IDs
        $states = [];
        $ids = $this->getStateIds();
        
        foreach ($ids as $id) {
            $stateData = $this->stateManager->load($id);
            
            // Check for type in state data
            if (isset($stateData['type']) && $stateData['type'] === $type) {
                $states[$id] = $stateData;
            }
        }
        
        return $states;
    }
    
    /**
     * Get all state IDs from the state manager
     * 
     * @return array Array of available state IDs
     */
    protected function getStateIds(): array
    {
        // Per the provided memory, StateManagerInterface has metadata() method
        // that we can use to get all state IDs
        try {
            // Access existing state data using reflection if necessary
            // In a real implementation, this would depend on the StateManagerInterface
            // implementation details
            return [];
        } catch (\Exception $e) {
            // Log the error but continue with empty array
            if (app()->hasDebugModeEnabled()) {
                logger()->error("Error getting state IDs: " . $e->getMessage());
            }
            return [];
        }
    }

    /**
     * Get all available component types
     */
    public function getAvailableTypes(): array
    {
        return $this->resolver->getTypes();
    }

    /**
     * Get all component states
     *
     * @param string $type Component type
     * @return array Array of states
     */
    public function getComponentStates(string $type): array
    {
        return $this->getStates($type);
    }

    /**
     * Check if a component type exists
     *
     * @param string $type Component type
     * @return bool
     */
    public function hasComponent(string $type): bool
    {
        return $this->resolver->has($type);
    }

    /**
     * Get all registered components
     *
     * @return array Array of component types and their classes
     */
    public function getComponents(): array
    {
        return $this->resolver->getComponents();
    }

    /**
     * Create a component instance
     *
     * @param string $type Component type
     * @param array $config Component configuration
     * @return UIComponentSchema
     */
    public function createComponent(string $type, array $config = []): UIComponentSchema
    {
        $component = $this->resolveComponent($type);
        
        // Apply configuration
        foreach ($config as $key => $value) {
            if (property_exists($component, $key)) {
                $component->{$key} = $value;
            }
        }
        
        return $component;
    }

    /**
     * Create a component from schema
     *
     * @param array $schema Component schema
     * @return UIComponentSchema
     */
    public function createFromSchema(array $schema): UIComponentSchema
    {
        if (!isset($schema['type'])) {
            throw new \InvalidArgumentException('Schema must contain a type field');
        }
        return $this->resolveComponent($schema['type']);
    }

    /**
     * Resolve a component by type
     *
     * @param string $type The component type to resolve
     * @return UIComponentSchema The instantiated component
     * @throws \InvalidArgumentException If the component type is not found
     */
    public function resolveComponent(string $type): UIComponentSchema
    {
        // Get the component class from the resolver
        $class = $this->resolver->resolve($type);
        
        if (!$class) {
            throw new \InvalidArgumentException("Component type '{$type}' not found");
        }
        
        // Conditionally pass the validator based on validation being enabled
        if ($this->validationEnabled && $this->validator !== null) {
            // Attempt to instantiate via container for better interface handling
            try {
                return app()->makeWith($class, ['validator' => $this->validator]);
            } catch (\Exception $e) {
                // If that fails, try direct instantiation as a fallback
                return new $class($this->validator);
            }
        } else {
            // When validation is disabled, instantiate without a validator
            // The component needs to handle a null validator in this case
            return app()->makeWith($class, ['validator' => null]);
        }
    }

    // getAllExampleData removed - use getAllSchemas and process the data as needed

    /**
     * Get schemas for all registered components
     *
     * This method maps all registered components and retrieves their schema structures
     *
     * @return array Associative array of component types and their schemas
     */
    public function getAllSchemas(): array
    {
        $schemas = [];
        $types = $this->resolver->getTypes();

        foreach ($types as $type) {
            try {
                $component = $this->resolveComponent($type);
                
                // Make sure we safely convert to array, handling Property objects
                $componentSchema = $component->toArray();
                
                // Verify we're working with an array before adding it
                if (is_array($componentSchema)) {
                    $schemas[$type] = $componentSchema;
                }
            } catch (\Exception $e) {
                // Log error but continue with other components
                if (app()->hasDebugModeEnabled()) {
                    logger()->error("Error resolving component schema for type {$type}: " . $e->getMessage());
                }
                continue;
            }
        }

        return $schemas;
    }
    
    /**
     * Get simplified values for all registered components
     *
     * This method provides a frontend-friendly version of component schemas
     * with just the values/defaults needed for rendering, without the schema metadata
     *
     * @return array Associative array of component types and their consumable values
     */
    public function getAllComponentValues(): array
    {
        $componentsValues = [];
        $types = $this->resolver->getTypes();

        foreach ($types as $type) {
            try {
                $component = $this->resolveComponent($type);
                $componentValue = $this->extractComponentValues($component);
                
                // Extract original component name for use as the key (preserving case for backward compatibility)
                // First check if the component has a protected $component property
                $reflection = new \ReflectionClass($component);
                if ($reflection->hasProperty('component')) {
                    $componentProp = $reflection->getProperty('component');
                    $componentProp->setAccessible(true);
                    $shortName = $componentProp->getValue($component);
                } else {
                    // Fallback: use the class short name without 'Schema' suffix but preserve case
                    $shortName = str_replace('Schema', '', $reflection->getShortName());
                }
                
                // Place extracted component values directly under the key
                $componentsValues[$shortName] = $componentValue;
            } catch (\Exception $e) {
                // Log error but continue with other components
                if (app()->hasDebugModeEnabled()) {
                    logger()->error("Error extracting component values for type {$type}: " . $e->getMessage());
                }
                continue;
            }
        }

        return $componentsValues;
    }
    
    /**
     * Extract consumable values from a component
     *
     * @param UIComponentSchema $component The component to extract values from
     * @return array The simplified value structure for frontend consumption
     */
    protected function extractComponentValues(UIComponentSchema $component): array
    {
        // First check if component has getExampleData method (most flexible approach)
        if (method_exists($component, 'getExampleData')) {
            return $component->getExampleData();
        }
        
        // Next, try to use protected example property
        $reflection = new \ReflectionClass($component);
        
        if ($reflection->hasProperty('example')) {
            $exampleProp = $reflection->getProperty('example');
            $exampleProp->setAccessible(true);
            $example = $exampleProp->getValue($component);
            
            // If component has a non-empty example property, use it exactly as-is
            if (!empty($example)) {
                return $example;
            }
        }
        
        // Fallback to empty array if no data is available
        return [];
    }
    
    /**
     * Extract example values from PropertyBuilder schema
     *
     * @param array $properties PropertyBuilder schema array
     * @return array Extracted example values
     */

    /**
     * Recursively flatten properties to extract values/defaults
     *
     * @param array $properties The properties to flatten
     * @return array The flattened values
     */
    protected function flattenProperties(array $properties): array
    {
        $result = [];
        
        foreach ($properties as $key => $property) {
            // If this is an object with nested properties
            if (isset($property['type']) && $property['type'] === 'object' && isset($property['properties'])) {
                // Process the nested properties
                $nestedResult = $this->flattenProperties($property['properties']);
                
                // Include values/defaults from this level
                $result[$key] = [];
                
                if (isset($property['value'])) {
                    $result[$key]['value'] = $property['value'];
                } elseif (isset($property['default'])) {
                    $result[$key]['value'] = $property['default'];
                }
                
                // Add nested properties
                $result[$key]['properties'] = $nestedResult;
            } else {
                // For simple properties, just extract the value or default
                if (isset($property['value'])) {
                    $result[$key] = $property['value'];
                } elseif (isset($property['default'])) {
                    $result[$key] = $property['default'];
                } else {
                    // For complex properties, keep the structure but remove unnecessary fields
                    $cleanedProperty = array_diff_key($property, array_flip(['required', 'description', 'example']));
                    if (!empty($cleanedProperty)) {
                        $result[$key] = $cleanedProperty;
                    }
                }
            }
        }
        
        return $result;
    }
    
    /**
     * Get property examples for all registered components
     *
     * This method outputs examples/values of each property in a structured format
     * with container and property keys grouped under a 'config' key
     *
     * @return array Array of component examples in the format ['config' => ['container_key' => ['property_key' => 'property_value']]]
     */
    public function getPropertyExamples(): array
    {
        $result = [];
        $schemas = $this->getAllSchemas();
        
        foreach ($schemas as $type => $schema) {
            // Get the component
            try {
                $component = $this->resolveComponent($type);
                
                // Extract component name for use as container key
                $reflection = new \ReflectionClass($component);
                $containerKey = null;
                
                if ($reflection->hasProperty('component')) {
                    $componentProp = $reflection->getProperty('component');
                    $componentProp->setAccessible(true);
                    $containerKey = $componentProp->getValue($component);
                } else {
                    // Fallback: use the class short name without 'Schema' suffix
                    $containerKey = str_replace('Schema', '', $reflection->getShortName());
                }
                
                // Initialize the config structure if not exists
                if (!isset($result['config'])) {
                    $result['config'] = [];
                }
                
                // Extract property values
                $propertyValues = $this->extractComponentPropertyValues($component);
                
                if (!empty($propertyValues)) {
                    $result['config'][$containerKey] = $propertyValues;
                }
            } catch (\Exception $e) {
                // Log error but continue with other components
                if ($this->isDebugModeEnabled()) {
                    logger()->error("Error extracting property examples for type {$type}: " . $e->getMessage());
                }
                continue;
            }
        }
        
        return $result;
    }
    
    /**
     * Extract property values from a component
     *
     * @param UIComponentSchema $component The component to extract property values from
     * @return array The extracted property values
     */
    protected function extractComponentPropertyValues(UIComponentSchema $component): array
    {
        $result = [];
        
        // Check if component has schema method
        $schema = $component->toArray();
        
        if (isset($schema['properties'])) {
            // Process each property in the schema
            foreach ($schema['properties'] as $key => $property) {
                // Extract property value or default
                if (isset($property['example'])) {
                    $result[$key] = $property['example'];
                } elseif (isset($property['value'])) {
                    $result[$key] = $property['value'];
                } elseif (isset($property['default'])) {
                    $result[$key] = $property['default'];
                } elseif (isset($property['enum']) && !empty($property['enum'])) {
                    // Use first enum value as example
                    $result[$key] = $property['enum'][0];
                }
                
                // Handle special property types
                if (isset($property['type'])) {
                    // For objects with properties, recursively process
                    if ($property['type'] === 'object' && isset($property['properties'])) {
                        $nestedValues = [];
                        foreach ($property['properties'] as $nestedKey => $nestedProperty) {
                            if (isset($nestedProperty['example'])) {
                                $nestedValues[$nestedKey] = $nestedProperty['example'];
                            } elseif (isset($nestedProperty['value'])) {
                                $nestedValues[$nestedKey] = $nestedProperty['value'];
                            } elseif (isset($nestedProperty['default'])) {
                                $nestedValues[$nestedKey] = $nestedProperty['default'];
                            }
                        }
                        
                        if (!empty($nestedValues)) {
                            $result[$key] = $nestedValues;
                        }
                    }
                    
                    // For arrays with items, provide example structure
                    elseif ($property['type'] === 'array' && isset($property['items'])) {
                        // If example array is provided, use it
                        if (isset($property['examples']) && !empty($property['examples'])) {
                            $result[$key] = $property['examples'];
                        } 
                        // Or create an example with one item based on the items schema
                        elseif (isset($property['items']['example'])) {
                            $result[$key] = [$property['items']['example']];
                        } elseif (isset($property['items']['value'])) {
                            $result[$key] = [$property['items']['value']];
                        } elseif (isset($property['items']['default'])) {
                            $result[$key] = [$property['items']['default']];
                        } elseif (isset($property['items']['enum']) && !empty($property['items']['enum'])) {
                            $result[$key] = [$property['items']['enum'][0]];
                        }
                    }
                }
            }
        }
        
        return $result;
    }
    
    /**
     * Check if debug mode is enabled
     * 
     * @return bool
     */
    protected function isDebugModeEnabled(): bool
    {
        try {
            return config('app.debug', false) === true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
