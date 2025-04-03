<?php

namespace App\UiSchemas;

use Skillcraft\SchemaValidation\Contracts\ValidatorInterface;
use Skillcraft\UiSchemaCraft\Abstracts\UIComponentSchema;
use Skillcraft\UiSchemaCraft\Schema\PropertyBuilder;

class LoginSchema extends UIComponentSchema
{
    protected string $type = 'login';
    protected string $component = 'Login';
    protected string $version = '1.0.0';
    protected string $description = 'Login Page schema for authentication';
    
    /**
     * Define which properties should be considered top-level containers
     * This controls the hierarchical structure of the output
     */
    protected array $mainContainers = [
        'form_text',
        'form_config'
    ];
    
    /**
     * Optional validation schema - only used if validation is enabled
     */
    protected ?array $validationSchema = [
        'username' => 'required|string|min:3',
        'password' => 'required|string|min:8',
    ];

    /**
     * Constructor with optional validator support
     * 
     * @param ValidatorInterface|null $validator Optional validator instance
     */
    public function __construct(?ValidatorInterface $validator = null) 
    {
        parent::__construct($validator);
    }
    
    /**
     * Get example data for this component
     * 
     * @return array Example data
     */
    public function getExampleData(): array
    {
        return [
            'username' => 'john_doe',
            'password' => 'secure_password',
        ];
    }

    /**
     * Define the component properties schema
     *
     * @return array The component properties schema
     */
    public function properties(): array
    {
        $builder = new PropertyBuilder();
        
        // Form text configuration
        $builder->object('form_text')
            ->description('Login Page configuration')
            ->properties([
                $builder->string('title')
                    ->required()
                    ->default('Log In')
                    ->example('Sign In'),
                    
                $builder->string('faq_title')
                    ->required()
                    ->default('Frequently Asked Questions')
                    ->example('Common Questions'),
                    
                $builder->string('login_text')
                    ->required()
                    ->default("Don't have an account?")
                    ->example('Need to create an account?')
            ]);
            
        // Form configuration    
        $builder->object('form_config')
            ->description('Form Configuration')
            ->properties([
                $builder->string('endpoint')
                    ->required()
                    ->default('/action/login')
                    ->example('/api/auth/login'),
                    
                $builder->object('schema')
                    ->description('Form Field Schema')
                    ->properties([
                        // Email field
                        $builder->object('email')
                            ->description('Email Field')
                            ->properties([
                                $builder->string('type')
                                    ->required()
                                    ->default('text')
                                    ->example('text'),
                                
                                $builder->string('inputType')
                                    ->required()
                                    ->default('email')
                                    ->example('email'),
                                    
                                $builder->string('label')
                                    ->required()
                                    ->default('Email')
                                    ->example('Email Address'),
                                    
                                $builder->string('placeholder')
                                    ->required()
                                    ->default('Enter Your Email')
                                    ->example('username@example.com'),
                                    
                                $builder->boolean('floating')
                                    ->required()
                                    ->default(false)
                                    ->example(true),
                                    
                                $builder->array('rules')
                                    ->required()
                                    ->default(['required', 'email'])
                                    ->example(['required', 'email', 'max:255'])
                            ]),
                            
                        // Password field
                        $builder->object('password')
                            ->description('Password Field')
                            ->properties([
                                $builder->string('type')
                                    ->required()
                                    ->default('text')
                                    ->example('text'),
                                    
                                $builder->string('inputType')
                                    ->required()
                                    ->default('password')
                                    ->example('password'),
                                    
                                $builder->string('label')
                                    ->required()
                                    ->default('Password')
                                    ->example('Your Password'),
                                    
                                $builder->string('placeholder')
                                    ->required()
                                    ->default('Enter Your Password')
                                    ->example('Enter a secure password'),
                                    
                                $builder->boolean('floating')
                                    ->required()
                                    ->default(false)
                                    ->example(false),
                                    
                                $builder->array('rules')
                                    ->required()
                                    ->default(['required', 'min:8'])
                                    ->example(['required', 'min:8', 'max:64']),
                                    
                                $builder->object('columns')
                                    ->description('Layout Columns')
                                    ->properties([
                                        $builder->number('container')
                                            ->required()
                                            ->default(6)
                                            ->example(6)
                                    ])
                            ]),
                            
                        // Forgot password link
                        $builder->object('link')
                            ->description('Forgot Password Link')
                            ->properties([
                                $builder->string('type')
                                    ->required()
                                    ->default('static')
                                    ->example('static'),
                                    
                                $builder->string('content')
                                    ->required()
                                    ->default('Forgot Password')
                                    ->example('Forgot Your Password?'),
                                    
                                $builder->string('tag')
                                    ->required()
                                    ->default('a')
                                    ->example('a'),
                                    
                                $builder->string('href')
                                    ->required()
                                    ->default('/forgot-password')
                                    ->example('/auth/recover'),
                                    
                                $builder->string('align')
                                    ->required()
                                    ->default('right')
                                    ->example('right')
                            ]),
                            
                        // Submit button
                        $builder->object('primaryButton')
                            ->description('Submit Button')
                            ->properties([
                                $builder->string('type')
                                    ->required()
                                    ->default('button')
                                    ->example('button'),
                                    
                                $builder->string('buttonLabel')
                                    ->required()
                                    ->default('Log In')
                                    ->example('Sign In'),
                                    
                                $builder->string('size')
                                    ->required()
                                    ->default('lg')
                                    ->example('lg'),
                                    
                                $builder->boolean('submits')
                                    ->required()
                                    ->default(true)
                                    ->example(true),
                                    
                                $builder->boolean('full')
                                    ->required()
                                    ->default(true)
                                    ->example(true)
                            ])
                    ])
            ]);
            
        return $builder->toArray();
    }
    
    /**
     * Extend the schema with custom data
     * 
     * @param array $schema Base schema from the trait
     * @return array Extended schema with custom data
     */
    protected function extendSchema(array $schema): array
    {
        // Add any login-specific schema extensions here
        $schema['auth_mode'] = 'credentials';
        return $schema;
    }
}
