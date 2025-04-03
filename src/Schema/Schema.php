<?php

namespace Skillcraft\UiSchemaCraft\Schema;

use Illuminate\Support\Facades\Validator;
use Closure;
use Skillcraft\UiSchemaCraft\Exceptions\ValidationException;

class Schema
{
    /**
     * The schema properties.
     *
     * @var array
     */
    protected array $properties = [];

    /**
     * The schema validation rules.
     *
     * @var array
     */
    protected array $rules = [];

    /**
     * The schema validation messages.
     *
     * @var array
     */
    protected array $messages = [];

    /**
     * The schema validation custom attributes.
     *
     * @var array
     */
    protected array $attributes = [];

    /**
     * The schema required fields.
     *
     * @var array
     */
    protected array $requiredFields = [];

    /**
     * Add a property to the schema.
     *
     * @param Property $property
     * @return $this
     */
    public function addProperty(Property $property): self
    {
        // The buildRulesFromProperty method already calls getName() and stores the property
        // with the appropriate name in the rules and properties arrays
        $this->buildRulesFromProperty($property);
        
        return $this;
    }

    /**
     * Build validation rules from a property.
     *
     * @param Property $property
     * @return void
     */
    protected function buildRulesFromProperty(Property $property): void
    {
        // Call getName() only once to comply with mock expectations
        $name = $property->getName();
        $rules = $property->getRules();
        
        // Ensure the property has an entry in the rules array even if there are no base rules
        // This ensures conditional rules can work properly
        $this->rules[$name] = !empty($rules) ? $rules : [];
        
        // Store the property in the properties array
        $propertyArray = $property->toArray();
        $this->properties[$name] = $propertyArray;
        
        // If the property is required, add it to the required fields list
        if ($propertyArray['required'] ?? false) {
            $this->requiredFields[] = $name;
        }
    }

    /**
     * Get the schema properties.
     *
     * @return array
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * Add a validation rule.
     *
     * @param string $field
     * @param string|array $rules
     * @return $this
     */
    public function addRule(string $field, $rules): self
    {
        $this->rules[$field] = $rules;
        return $this;
    }

    /**
     * Add validation rules.
     *
     * @param array $rules
     * @return $this
     */
    public function addRules(array $rules): self
    {
        $this->rules = array_merge($this->rules, $rules);
        return $this;
    }

    /**
     * Get the validation rules.
     *
     * @return array
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * Add a validation message.
     *
     * @param string $field
     * @param string $rule
     * @param string $message
     * @return $this
     */
    public function addMessage(string $field, string $rule, string $message): self
    {
        $this->messages["$field.$rule"] = $message;
        return $this;
    }

    /**
     * Add custom validation messages.
     *
     * @param array $messages
     * @return $this
     */
    public function withMessages(array $messages): self
    {
        $this->messages = array_merge($this->messages, $messages);
        return $this;
    }

    /**
     * Set custom validation messages.
     *
     * @param array $messages
     * @return $this
     */
    public function setMessages(array $messages): self
    {
        $this->messages = $messages;
        return $this;
    }

    /**
     * Get the validation messages.
     *
     * @return array
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * Set custom attribute names for validation.
     *
     * @param array $attributes
     * @return $this
     */
    public function withAttributes(array $attributes): self
    {
        $this->attributes = array_merge($this->attributes, $attributes);
        return $this;
    }

    /**
     * Set validation attributes (for backward compatibility).
     *
     * @param array $attributes
     * @return $this
     */
    public function setAttributes(array $attributes): self
    {
        $this->attributes = $attributes;
        return $this;
    }

    /**
     * Get custom attribute names.
     *
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Validate data against the schema.
     *
     * @param array $data
     * @return array
     */
    public function validate(array $data): array
    {
        if (empty($this->rules) && empty($this->properties)) {
            return [
                'valid' => true,
                'errors' => [],
                'data' => $data
            ];
        }
        
        // Process conditional rules from properties
        $formattedRules = $this->processConditionalRules($data);
        
        // If no rules after processing, return valid
        if (empty($formattedRules)) {
            return [
                'valid' => true,
                'errors' => [],
                'data' => $data
            ];
        }
        
        // Convert rules arrays to pipe-delimited strings for Laravel validator
        $laravelRules = [];
        foreach ($formattedRules as $field => $rules) {
            $laravelRules[$field] = is_array($rules) ? implode('|', $rules) : $rules;
        }
        
        $validator = Validator::make(
            $data,
            $laravelRules,
            $this->messages,
            $this->attributes
        );
        
        if ($validator->fails()) {
            return [
                'valid' => false,
                'errors' => $validator->errors()->toArray(),
                'data' => $data
            ];
        }
        
        return [
            'valid' => true,
            'errors' => [],
            'data' => $validator->validated()
        ];
    }
    
    /**
     * Process conditional rules based on the provided data.
     *
     * @param array $data
     * @return array
     */
    protected function processConditionalRules(array $data): array
    {
        // Start with the base rules
        $processedRules = $this->rules;
        
        // Process conditional rules from each property
        foreach ($this->properties as $propertyName => $property) {
            if (!isset($property['conditionalRules']) || empty($property['conditionalRules'])) {
                continue;
            }
            
            foreach ($property['conditionalRules'] as $conditionalRule) {
                $shouldApply = false;
                
                // Process different types of conditional rules
                switch ($conditionalRule['type'] ?? 'field') {
                    case 'field':
                        // Field equals value check
                        $field = $conditionalRule['field'] ?? '';
                        $value = $conditionalRule['value'] ?? null;
                        $shouldApply = isset($data[$field]) && $data[$field] === $value;
                        break;
                        
                    case 'pattern':
                        // Field matches pattern check
                        $field = $conditionalRule['field'] ?? '';
                        $pattern = $conditionalRule['value']['pattern'] ?? '';
                        $shouldApply = isset($data[$field]) && preg_match($pattern, $data[$field]);
                        break;
                        
                    case 'comparison':
                        // Field comparison check
                        $field = $conditionalRule['field'] ?? '';
                        $operator = $conditionalRule['value']['operator'] ?? '=';
                        $compareValue = $conditionalRule['value']['value'] ?? null;
                        
                        if (isset($data[$field])) {
                            $fieldValue = $data[$field];
                            
                            switch ($operator) {
                                case '=':
                                case '==':
                                    $shouldApply = $fieldValue == $compareValue;
                                    break;
                                case '===':
                                    $shouldApply = $fieldValue === $compareValue;
                                    break;
                                case '!=':
                                case '<>':
                                    $shouldApply = $fieldValue != $compareValue;
                                    break;
                                case '!==':
                                    $shouldApply = $fieldValue !== $compareValue;
                                    break;
                                case '<':
                                    $shouldApply = $fieldValue < $compareValue;
                                    break;
                                case '<=':
                                    $shouldApply = $fieldValue <= $compareValue;
                                    break;
                                case '>':
                                    $shouldApply = $fieldValue > $compareValue;
                                    break;
                                case '>=':
                                    $shouldApply = $fieldValue >= $compareValue;
                                    break;
                                default:
                                    $shouldApply = false;
                            }
                        }
                        break;
                        
                    case 'array':
                        // Multiple fields check
                        $fields = $conditionalRule['field'] ?? [];
                        
                        // Handle empty array value case - always apply for empty fields array
                        if (empty($fields)) {
                            $shouldApply = true;
                            break;
                        }
                        
                        $allMatch = true;
                        foreach ($fields as $arrayField => $arrayValue) {
                            // Special handling for null values
                            if ($arrayValue === null) {
                                // Match if the field is not set or is null
                                if (isset($data[$arrayField]) && $data[$arrayField] !== null) {
                                    $allMatch = false;
                                    break;
                                }
                            } else if (!isset($data[$arrayField]) || $data[$arrayField] !== $arrayValue) {
                                $allMatch = false;
                                break;
                            }
                        }
                        
                        $shouldApply = $allMatch;
                        break;
                        
                    case 'closure':
                        // Handle closure type conditional rules
                        if (isset($conditionalRule['closure']) && $conditionalRule['closure'] instanceof Closure) {
                            $shouldApply = call_user_func($conditionalRule['closure'], $data);
                        }
                        break;
                }
                
                // Apply rules if condition is met
                if ($shouldApply && isset($conditionalRule['rules'])) {
                    // Initialize the property in processed rules if it doesn't exist
                    if (!isset($processedRules[$propertyName])) {
                        $processedRules[$propertyName] = [];
                    }
                    
                    $existingRules = $processedRules[$propertyName];
                    if (!is_array($existingRules)) {
                        $existingRules = explode('|', $existingRules);
                    }
                    
                    // Handle conditionalRule['rules'] as string or array
                    $rulesToAdd = $conditionalRule['rules'];
                    if (!is_array($rulesToAdd)) {
                        $rulesToAdd = explode('|', $rulesToAdd);
                    }
                    
                    $processedRules[$propertyName] = array_unique(array_merge(
                        $existingRules,
                        $rulesToAdd
                    ));
                }
            }
        }
        
        return $processedRules;
    }

    /**
     * Convert the schema to an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        $result = [
            'properties' => $this->properties,
            'type' => 'object'
        ];
        
        // Add required fields if any exist
        if (!empty($this->requiredFields)) {
            $result['required'] = array_values(array_unique($this->requiredFields));
        }
        
        return $result;
    }

    /**
     * Convert the schema to JSON.
     *
     * @param int $options
     * @return string
     */
    public function toJson(int $options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }
}
