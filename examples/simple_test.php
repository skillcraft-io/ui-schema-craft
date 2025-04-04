<?php

/**
 * Simple test script to demonstrate extractComponentValues
 * This script simulates the component and example structure
 * without relying on autoloading
 */

// Mock component class with example property
class MockLoginSchema {
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
                        'rules' => ['required', 'email'],
                    ],
                    'password' => [
                        'type' => 'text',
                        'inputType' => 'password',
                        'rules' => ['required'],
                    ]
                ]
            ]
        ]
    ];
    
    // Just a mock method to demonstrate
    public function toArray() {
        return [];
    }
}

// Mock service with extract method
class MockService {
    // This is the actual implementation we're testing
    protected function extractComponentValues($component): array
    {
        // Use reflection to access protected example property
        $reflection = new \ReflectionClass($component);
        
        if ($reflection->hasProperty('example')) {
            $exampleProp = $reflection->getProperty('example');
            $exampleProp->setAccessible(true);
            $example = $exampleProp->getValue($component);
            
            // If component has an example property, use it exactly as-is
            if (!empty($example)) {
                return $example;
            }
        }
        
        // If no example available, return empty array
        return [];
    }
    
    // Make it public for testing
    public function testExtract($component) {
        return $this->extractComponentValues($component);
    }
}

// Create test instances
$component = new MockLoginSchema();
$service = new MockService();

// Get original example data using reflection
$reflection = new ReflectionClass($component);
$exampleProp = $reflection->getProperty('example');
$exampleProp->setAccessible(true);
$expectedData = $exampleProp->getValue($component);

// Get extracted data using our method
$extractedData = $service->testExtract($component);

// Display results
echo "===== ORIGINAL EXAMPLE DATA =====\n";
echo json_encode($expectedData, JSON_PRETTY_PRINT) . "\n\n";

echo "===== EXTRACTED DATA =====\n";
echo json_encode($extractedData, JSON_PRETTY_PRINT) . "\n\n";

echo "DO THEY MATCH? " . ($expectedData === $extractedData ? "✅ YES" : "❌ NO") . "\n";
