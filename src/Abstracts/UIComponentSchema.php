<?php

namespace Skillcraft\UiSchemaCraft\Abstracts;

use Skillcraft\UiSchemaCraft\Composition\ComposableTrait;
use Skillcraft\UiSchemaCraft\Validation\ValidationResult;
use Skillcraft\UiSchemaCraft\Schema\Property;

abstract class UIComponentSchema
{
    use ComposableTrait;

    protected string $type;
    protected string $component;
    protected string $version = '1.0.0';
    protected array $propertyValues = [];

    /**
     * Get example data for the component
     *
     * @return array
     */
    public function getExampleData(): array
    {
        $values = [];

        foreach ($this->properties() as $key => $property) {
            $values[$key] = data_get($property, 'example_data', null);
        }

        return array_merge($values, $this->propertyValues);
    }

    /**
     * Get component identifier
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->type;
    }

    /**
     * Get component version
     *
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * Convert component to array representation
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'component' => $this->component,
            'version' => $this->version,
            'properties' => $this->properties(),
            'children' => $this->getChildrenSchema(),
        ];
    }

    /**
     * Get property values
     *
     * @return array
     */
    public function getPropertyValues(): array
    {
        $values = [];
        foreach ($this->properties() as $property) {
            $values[$property->getName()] = $property->getDefault();
        }
        return array_merge($values, $this->propertyValues);
    }

    /**
     * Get property names
     *
     * @return array
     */
    public function getPropertyNames(): array
    {
        return array_map(function ($property) {
            return $property->getName();
        }, $this->properties());
    }

    /**
     * Set a property value
     *
     * @param string $name
     * @param mixed $value
     * @return self
     */
    public function setPropertyValue(string $name, mixed $value): self
    {
        if (in_array($name, $this->getPropertyNames())) {
            $this->propertyValues[$name] = $value;
        }
        return $this;
    }

    /**
     * Validate component data
     *
     * @param array $data
     * @return ValidationResult
     */
    public function validate(array $data): ValidationResult
    {
        $result = new ValidationResult();

        foreach ($this->properties() as $property) {
            $propertyName = $property->getName();
            $propertyArray = $property->toArray();
            
            // Check if property is required and missing
            if (!array_key_exists($propertyName, $data)) {
                if ($propertyArray['required'] ?? false) {
                    $result->addError($propertyName, "The {$propertyName} field is required.");
                }
                continue;
            }

            $value = $data[$propertyName];
            
            // Validate the property value (including null values)
            if (!$property->validate($value)) {
                $result->addError($propertyName, $property->getValidationMessage());
            }
        }

        // Validate nested children if present
        if (isset($data['children'])) {
            foreach ($data['children'] as $identifier => $childData) {
                if (isset($this->children[$identifier])) {
                    $childResult = $this->children[$identifier]->validate($childData);
                    if ($childResult->hasErrors()) {
                        foreach ($childResult->toArray()['errors'] as $field => $messages) {
                            foreach ($messages as $message) {
                                $result->addError($field, $message);
                            }
                        }
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Clone the component
     */
    public function __clone()
    {
        if ($this->children) {
            $children = [];
            foreach ($this->children as $key => $child) {
                $children[$key] = clone $child;
            }
            $this->children = $children;
        }
    }

    /**
     * Get component properties
     *
     * @return array<Property>
     */
    abstract public function properties(): array;
}
