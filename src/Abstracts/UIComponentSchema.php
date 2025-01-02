<?php

namespace Skillcraft\UiSchemaCraft\Abstracts;

use Skillcraft\UiSchemaCraft\Composition\ComposableInterface;
use Skillcraft\UiSchemaCraft\Composition\ComposableTrait;
use Skillcraft\UiSchemaCraft\Validation\ValidationResult;

abstract class UIComponentSchema implements ComposableInterface
{
    use ComposableTrait;

    protected string $type;
    protected string $component;
    protected string $version = '1.0.0';

    /**
     * Get component properties schema
     *
     * @return array
     */
    abstract protected function properties(): array;

    /**
     * Get example data for the component
     *
     * @return array
     */
    abstract protected function getExampleData(): array;

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
     * Validate component data
     *
     * @param array $data
     * @return ValidationResult
     */
    public function validate(array $data): ValidationResult
    {
        $result = new ValidationResult();
        
        foreach ($this->properties() as $property) {
            if (!$property->validate($data[$property->getName()] ?? null)) {
                $result->addError($property->getName(), $property->getValidationMessage());
            }
        }

        // Validate children if present
        if (isset($data['children'])) {
            foreach ($this->getChildren() as $child) {
                if (isset($data['children'][$child->getIdentifier()])) {
                    $childResult = $child->validate($data['children'][$child->getIdentifier()]);
                    $result->merge($childResult);
                }
            }
        }

        return $result;
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
}
