<?php

namespace Skillcraft\UiSchemaCraft\Adapters;

use Skillcraft\SchemaValidation\Contracts\ValidatorInterface as PackageValidatorInterface;
use Illuminate\Contracts\Container\Container;

/**
 * Adapter to handle validator interface namespace differences between apps and the package
 */
class ValidatorInterfaceAdapter
{
    /**
     * Container instance
     */
    protected Container $container;
    
    /**
     * Package validator instance
     */
    protected PackageValidatorInterface $packageValidator;
    
    /**
     * Constructor
     */
    public function __construct(Container $container, PackageValidatorInterface $packageValidator)
    {
        $this->container = $container;
        $this->packageValidator = $packageValidator;
    }
    
    /**
     * Adapt the validator to the expected interface for a component
     *
     * @param string $componentClass The component class to adapt for
     * @return object The appropriate validator implementation
     */
    public function adapt(string $componentClass): object
    {
        // Check the constructor's parameter type
        $reflection = new \ReflectionClass($componentClass);
        $constructor = $reflection->getConstructor();
        
        if (!$constructor) {
            // No constructor, return the package validator
            return $this->packageValidator;
        }
        
        $params = $constructor->getParameters();
        if (empty($params) || !$params[0]->hasType()) {
            // No typed parameter, return the package validator
            return $this->packageValidator;
        }
        
        $paramType = $params[0]->getType();
        if (!$paramType instanceof \ReflectionNamedType || $paramType->isBuiltin()) {
            // Not a class/interface type, return the package validator
            return $this->packageValidator;
        }
        
        $typeName = $paramType->getName();
        
        // Check if our package validator already satisfies the type
        if (is_a($this->packageValidator, $typeName)) {
            return $this->packageValidator;
        }
        
        // If it's a ValidatorInterface in a different namespace, try to find an adapter or wrapper
        if (str_ends_with($typeName, 'ValidatorInterface')) {
            // Log that we're encountering a mismatch
            if (class_exists(\Illuminate\Support\Facades\Log::class)) {
                \Illuminate\Support\Facades\Log::info("Adapting validator for {$componentClass}", [
                    'expected' => $typeName,
                    'actual' => get_class($this->packageValidator)
                ]);
            }
            
            // Try to resolve the requested validator from the container
            if ($this->container->bound($typeName)) {
                return $this->container->make($typeName);
            }
            
            // If not found, try to create a proxy that implements the interface
            $adapterClass = $this->createAdapterClass($typeName);
            return new $adapterClass($this->packageValidator);
        }
        
        // Default fallback
        return $this->packageValidator;
    }
    
    /**
     * Create a dynamic adapter class for the target interface
     *
     * @param string $targetInterface The interface to adapt to
     * @return string The adapter class name
     */
    protected function createAdapterClass(string $targetInterface): string
    {
        // Generate a unique class name
        $className = 'DynamicValidatorAdapter_' . md5($targetInterface);
        
        // If the class already exists, return it
        if (class_exists($className)) {
            return $className;
        }
        
        // Create a class definition that implements the target interface
        // and forwards all calls to our package validator
        $code = "
            class {$className} implements \\{$targetInterface}
            {
                private \Skillcraft\SchemaValidation\Contracts\ValidatorInterface \$validator;
                
                public function __construct(\Skillcraft\SchemaValidation\Contracts\ValidatorInterface \$validator)
                {
                    \$this->validator = \$validator;
                }
                
                public function __call(string \$method, array \$arguments)
                {
                    return \$this->validator->{\$method}(...\$arguments);
                }
                
                // Implement key methods from ValidatorInterface (these would need to be customized based on the actual interface)
                public function validate(array \$data, array \$schema): array
                {
                    return \$this->validator->validate(\$data, \$schema);
                }
                
                public function isValid(): bool
                {
                    return \$this->validator->isValid();
                }
                
                public function getErrors(): array
                {
                    return \$this->validator->getErrors();
                }
            }
        ";
        
        // Evaluate the code to define the class
        eval($code);
        
        return $className;
    }
}
