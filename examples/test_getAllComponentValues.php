<?php

/**
 * Simple test script to demonstrate getAllComponentValues format
 */

// Mock component classes with example properties
class LoginSchema {
    protected array $example = [
        'config' => [
            'form_text' => [
                'title' => 'Log In',
                'login_text' => "Don't have an account?",
            ],
            'form_config' => [
                'endpoint' => "/action/login",
                'schema' => [
                    'email' => [
                        'type' => 'text',
                        'inputType' => 'email',
                    ],
                ]
            ]
        ]
    ];
    
    public function toArray() { return []; }
}

class RegisterSchema {
    protected array $example = [
        'config' => [
            'form_text' => [
                'title' => 'Register Account',
                'register_text' => "Already have an account?",
            ],
            'form_config' => [
                'endpoint' => "/action/register",
                'schema' => [
                    'name' => [
                        'type' => 'text',
                        'inputType' => 'text',
                    ],
                ]
            ]
        ]
    ];
    
    public function toArray() { return []; }
}

// Mock resolver class
class MockResolver {
    private $components = [];
    
    public function registerComponent($type, $component) {
        $this->components[$type] = $component;
    }
    
    public function getTypes() {
        return array_keys($this->components);
    }
    
    public function resolve($type) {
        return $this->components[$type] ?? null;
    }
}

// Mock service with getAllComponentValues implementation
class MockUiSchemaCraftService {
    private $resolver;
    
    public function __construct($resolver) {
        $this->resolver = $resolver;
    }
    
    public function resolveComponent($type) {
        return $this->resolver->resolve($type);
    }
    
    protected function extractComponentValues($component) {
        // Same implementation as in UiSchemaCraftService
        $reflection = new \ReflectionClass($component);
        
        if ($reflection->hasProperty('example')) {
            $exampleProp = $reflection->getProperty('example');
            $exampleProp->setAccessible(true);
            $example = $exampleProp->getValue($component);
            
            if (!empty($example)) {
                return $example;
            }
        }
        
        return [];
    }
    
    public function getAllComponentValues() {
        $componentsValues = [];
        $types = $this->resolver->getTypes();

        foreach ($types as $type) {
            try {
                $component = $this->resolveComponent($type);
                $componentValue = $this->extractComponentValues($component);
                
                // Extract proper name from the class for use as the key (login, register, etc.)
                $reflection = new \ReflectionClass($component);
                $shortName = strtolower(str_replace('Schema', '', $reflection->getShortName()));
                
                // Place extracted component values directly under the key
                $componentsValues[$shortName] = $componentValue;
            } catch (\Exception $e) {
                echo "Error: " . $e->getMessage() . "\n";
                continue;
            }
        }

        return $componentsValues;
    }
}

// Set up test environment
$resolver = new MockResolver();
$resolver->registerComponent('login', new LoginSchema());
$resolver->registerComponent('register', new RegisterSchema());

$service = new MockUiSchemaCraftService($resolver);

// Get all component values
$allValues = $service->getAllComponentValues();

// Show the output
echo "===== getAllComponentValues() OUTPUT =====\n";
echo json_encode($allValues, JSON_PRETTY_PRINT) . "\n\n";

// Demonstrate accessing specific components
echo "===== ACCESSING LOGIN COMPONENT =====\n";
$loginValues = $allValues['login'] ?? 'Not found';
echo json_encode($loginValues, JSON_PRETTY_PRINT) . "\n\n";

echo "===== ACCESSING FORM TEXT TITLE =====\n";
$loginTitle = $allValues['login']['config']['form_text']['title'] ?? 'Not found';
echo $loginTitle . "\n";
