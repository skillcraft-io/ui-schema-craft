<?php

namespace Skillcraft\UiSchemaCraft\Providers;

use Illuminate\Support\ServiceProvider;
use Skillcraft\SchemaValidation\Contracts\ValidatorInterface;
use Skillcraft\SchemaValidation\Validation\CompositeValidator;

/**
 * This service provider ensures all dependencies for UiSchemaCraft are properly bound
 */
class FixValidatorBindingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register the ValidatorInterface if it's not already bound
        if (!$this->app->bound(ValidatorInterface::class)) {
            $this->app->singleton(ValidatorInterface::class, function ($app) {
                // Use the CompositeValidator with a wrapper to implement the interface
                $compositeValidator = new CompositeValidator();
                
                // Create a wrapper that implements the interface and delegates to CompositeValidator
                return new class($compositeValidator) implements ValidatorInterface {
                    private $validator;
                    
                    public function __construct(CompositeValidator $validator) 
                    {
                        $this->validator = $validator;
                    }
                    
                    public function validate($data, array $rules): bool 
                    {
                        return $this->validator->validate($data, $rules);
                    }
                    
                    public function isValid(): bool
                    {
                        return true; // Implement as needed
                    }
                    
                    public function getErrors(): array
                    {
                        return []; // Implement as needed
                    }
                };
            });
        }
        
        // Bind for any App\ComponentSchemas\ValidatorInterface usage
        $this->app->bind('App\\ComponentSchemas\\ValidatorInterface', ValidatorInterface::class);
    }
}
