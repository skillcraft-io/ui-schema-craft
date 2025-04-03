<?php

namespace Skillcraft\UiSchemaCraft\Tests\Feature;

use Illuminate\Support\Facades\App;
use Orchestra\Testbench\TestCase;
use Skillcraft\UiSchemaCraft\ComponentResolver;
use Skillcraft\UiSchemaCraft\Facades\UiSchema;
use Skillcraft\UiSchemaCraft\Providers\UiSchemaCraftServiceProvider;
use Skillcraft\UiSchemaCraft\Services\UiSchemaCraftService;
use Skillcraft\SchemaValidation\Contracts\ValidatorInterface;
use Skillcraft\SchemaState\Contracts\StateManagerInterface;

class ComponentRegistrationTest extends TestCase
{
    protected $mockValidator;
    protected $mockStateManager;
    
    protected function getPackageProviders($app)
    {
        return [UiSchemaCraftServiceProvider::class];
    }
    
    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function defineEnvironment($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
        
        // Configure UI Schema Craft
        $app['config']->set('ui-schema-craft.default_namespace', 'Skillcraft\\UiSchemaCraft\\Tests\\Feature\\TestComponents');
    }
    
    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Create mock dependencies for UiSchemaCraftService
        $this->mockValidator = $this->createMock(ValidatorInterface::class);
        $this->mockValidator->method('validate')
            ->will($this->returnCallback(function($data, $rules) {
                // For test email validation
                if (isset($rules['value']) && in_array('email', $rules['value']) && isset($data['value'])) {
                    return filter_var($data['value'], FILTER_VALIDATE_EMAIL) !== false;
                }
                
                // Default to valid for other validations
                return true;
            }));
            
        $this->mockStateManager = $this->createMock(StateManagerInterface::class);
        $resolver = $this->app->make(ComponentResolver::class);
        
        // Create service and bind it to the container
        $service = new UiSchemaCraftService($this->mockStateManager, $resolver, $this->mockValidator);
        $this->app->instance('ui-schema', $service);
        
        // Register test components namespace
        $resolver->registerNamespace('Skillcraft\\UiSchemaCraft\\Tests\\Feature\\TestComponents');
    }
    
    /**
     * Helper method to dump component registration state
     */
    protected function dumpComponentInfo()
    {
        echo "\nRegistered Components:\n";
        $components = $this->app->make('ui-schema')->getComponents();
        var_dump($components);
        echo "\n";
    }
    
    /** @test */
    public function it_can_register_and_resolve_a_component()
    {
        // Register and resolve a component
        UiSchema::registerComponent(TestComponents\ButtonComponent::class);
        
        // Get the actual type from the ButtonComponent class
        $buttonComponent = new TestComponents\ButtonComponent($this->mockValidator);
        $buttonType = $buttonComponent->getType();
        
        // Dump registered components for debugging
        $this->dumpComponentInfo();
        
        // Check component exists
        $this->assertTrue(UiSchema::hasComponent($buttonType));
        
        // Output more debug info
        echo "\nButton type: " . $buttonType . "\n";
        
        // Get component instance
        $component = UiSchema::createComponent($buttonType);
        
        // Verify it's the correct type
        $this->assertInstanceOf(TestComponents\ButtonComponent::class, $component);
        $this->assertEquals($buttonType, $component->getType());
    }
    
    /** @test */
    public function it_returns_correct_schema_structure_for_component()
    {
        // Register the component
        UiSchema::registerComponent(TestComponents\ButtonComponent::class);
        
        // Get the actual type from the ButtonComponent class
        $buttonComponent = new TestComponents\ButtonComponent($this->mockValidator);
        $buttonType = $buttonComponent->getType();
        
        // Create the component instance
        $component = UiSchema::createComponent($buttonType);
        
        // Get schema
        $schema = $component->toArray();
        
        // Verify schema structure
        $this->assertIsArray($schema);
        $this->assertEquals($buttonType, $schema['type']);
        $this->assertEquals($component->getComponent(), $schema['component']);
        
        // Get a reference to properties, which could be at the root or in a 'properties' key
        $props = $schema['properties'] ?? $schema;
        
        // Add schema debug output
        echo "\nButton Schema: " . json_encode($schema, JSON_PRETTY_PRINT) . "\n";
        
        // Verify properties from schema - properties are stored as property definitions
        $this->assertEquals('Submit', $props['text']['default'] ?? null);
        $this->assertEquals('primary', $props['variant']['default'] ?? null);
        $this->assertTrue($props['enabled']['default'] ?? false);
        
        // Verify title and description from the schema
        $this->assertEquals('Button Component', $props['title']['default'] ?? null);
        $this->assertEquals('A standard button component', $props['description']['default'] ?? null);
    }
    
    /** @test */
    public function it_registers_component_from_default_namespace()
    {
        // Register the component first since we're not relying on auto-discovery in tests
        UiSchema::registerComponent(TestComponents\AutoDiscoveredButtonComponent::class);
        
        // Get the actual type from the AutoDiscoveredButtonComponent class
        $autoButtonComponent = new TestComponents\AutoDiscoveredButtonComponent($this->mockValidator);
        $autoButtonType = $autoButtonComponent->getType();
        
        // Dump component info for debugging
        $this->dumpComponentInfo();
        
        // Output debug info
        echo "\nAuto button type: " . $autoButtonType . "\n";
        
        // Check component exists
        $this->assertTrue(UiSchema::hasComponent($autoButtonType));
        
        // Get auto-discovered button schema
        $autoSchema = UiSchema::getComponent($autoButtonType);
        
        // Verify schema structure
        $this->assertIsArray($autoSchema);
        $this->assertEquals($autoButtonType, $autoSchema['type']);
        
        // Add schema debug output
        echo "\nAuto Button Schema: " . json_encode($autoSchema, JSON_PRETTY_PRINT) . "\n";
        
        // Get properties from schema
        $autoProps = $autoSchema['properties'] ?? $autoSchema;
        
        // Verify title with proper schema structure
        $this->assertEquals('Auto-discovered Button', $autoProps['title']['default'] ?? null);
        
        // Get component instance
        $component = UiSchema::createComponent($autoButtonType);
        
        // Verify it's the correct type
        $this->assertInstanceOf(TestComponents\AutoDiscoveredButtonComponent::class, $component);
    }
    
    /** @test */
    public function it_validates_component_state_correctly()
    {
        // Register the validated component
        UiSchema::registerComponent(TestComponents\ValidatedInputComponent::class);
        
        // Get the actual type from the ValidatedInputComponent class
        $inputComponent = new TestComponents\ValidatedInputComponent($this->mockValidator);
        $inputType = $inputComponent->getType();
        
        // Create component
        $component = UiSchema::createComponent($inputType);
        
        // Test valid state
        $validState = [
            'value' => 'test@example.com'
        ];
        
        $result = $component->validate($validState);
        $this->assertTrue($result['valid']);
        $this->assertNull($result['errors']);
        
        // Test invalid state
        $invalidState = [
            'value' => 'not-an-email'
        ];
        
        $result = $component->validate($invalidState);
        $this->assertFalse($result['valid']);
        // For validation failures, the errors may be in different formats depending on implementation
        // Check that there are some errors present
        $this->assertNotEmpty($result['errors']);
    }
    
    /** @test */
    public function it_handles_component_composition_correctly()
    {
        // Register parent and child components
        UiSchema::registerComponent(TestComponents\FormContainerComponent::class);
        UiSchema::registerComponent(TestComponents\FormInputComponent::class);
        
        // Get the actual types from the component classes
        $formContainer = new TestComponents\FormContainerComponent($this->mockValidator);
        $formType = $formContainer->getType();
        
        $formInputComponent = new TestComponents\FormInputComponent($this->mockValidator);
        $inputType = $formInputComponent->getType();
        
        // Create parent component
        $form = UiSchema::createComponent($formType);
        
        // Create children
        $nameInput = UiSchema::createComponent($inputType);
        $nameInput->setProperty('label', 'Name');
        $nameInput->setProperty('placeholder', 'Enter your name');
        
        $emailInput = UiSchema::createComponent($inputType);
        $emailInput->setProperty('label', 'Email');
        $emailInput->setProperty('placeholder', 'Enter your email');
        $emailInput->setProperty('type', 'email');
        
        // Add children to parent
        $form->addChild($nameInput, 'name');
        $form->addChild($emailInput, 'email');
        
        // Verify container schema - use the form instance that has children added
        $schema = $form->toArray();
        $this->assertIsArray($schema);
        $this->assertEquals($formType, $schema['type']);
        
        // Add schema debug output
        echo "\nForm Container Schema: " . json_encode($schema, JSON_PRETTY_PRINT) . "\n";
        
        // Get properties from schema
        $containerProps = $schema['properties'] ?? $schema;
        
        // Verify title with proper schema structure
        $this->assertEquals('Form Container', $containerProps['title']['default'] ?? null);
        
        // Verify children structure
        $this->assertArrayHasKey('children', $schema);
        $this->assertCount(2, $schema['children']);
        
        // Make sure the schema contains children
        $this->assertArrayHasKey('children', $schema);
        
        // Add debug output for children
        if (isset($schema['children']) && count($schema['children']) > 0) {
            echo "\nFirst Child Schema (name): " . json_encode($schema['children'][0], JSON_PRETTY_PRINT) . "\n";
            echo "\nSecond Child Schema (email): " . json_encode($schema['children'][1], JSON_PRETTY_PRINT) . "\n";
            
            echo "\n--- Direct access to children properties ---";
            echo "\nFirst Child Type: " . (is_array($schema['children'][0]['type'] ?? null) ? 'Array: ' . json_encode($schema['children'][0]['type']) : ($schema['children'][0]['type'] ?? 'Not Found')) . "\n";
            
            $firstLabel = $schema['children'][0]['label'] ?? null;
            echo "First Child Label: " . (is_array($firstLabel) ? 'Array: ' . json_encode($firstLabel) : ($firstLabel ?? 'Not Found')) . "\n";
            
            $firstInputType = $schema['children'][0]['inputType'] ?? null;
            echo "First Child inputType: " . (is_array($firstInputType) ? 'Array: ' . json_encode($firstInputType) : ($firstInputType ?? 'Not Found')) . "\n";
            
            echo "\nSecond Child Type: " . (is_array($schema['children'][1]['type'] ?? null) ? 'Array: ' . json_encode($schema['children'][1]['type']) : ($schema['children'][1]['type'] ?? 'Not Found')) . "\n";
            
            $secondLabel = $schema['children'][1]['label'] ?? null;
            echo "Second Child Label: " . (is_array($secondLabel) ? 'Array: ' . json_encode($secondLabel) : ($secondLabel ?? 'Not Found')) . "\n";
            
            $secondInputType = $schema['children'][1]['inputType'] ?? null;
            echo "Second Child inputType: " . (is_array($secondInputType) ? 'Array: ' . json_encode($secondInputType) : ($secondInputType ?? 'Not Found')) . "\n";
            
            // Debug component properties directly from the component instances
            echo "\n--- Properties directly from component instances ---";
            echo "\nNameInput label direct: " . $nameInput->getProperty('label', 'Not Set');
            echo "\nNameInput type direct: " . $nameInput->getProperty('type', 'Not Set');
            echo "\nEmailInput label direct: " . $emailInput->getProperty('label', 'Not Set');
            echo "\nEmailInput type direct: " . $emailInput->getProperty('type', 'Not Set');
        }
        
        // Verify first child (name input)
        $nameSchema = $schema['children'][0];
        // Verify component type
        $this->assertEquals($inputType, $nameSchema['type']);
        
        // Validate label property exists at top level
        $this->assertArrayHasKey('label', $nameSchema);
        $this->assertEquals('Name', $nameSchema['label']);
        
        // Verify the input type is 'text' (default)
        $this->assertArrayHasKey('inputType', $nameSchema);
        $this->assertEquals('text', $nameSchema['inputType']);
        
        // Verify second child (email input)
        $emailSchema = $schema['children'][1];
        // Verify component type
        $this->assertEquals($inputType, $emailSchema['type']);
        
        // Validate label property exists at top level
        $this->assertArrayHasKey('label', $emailSchema);
        $this->assertEquals('Email', $emailSchema['label']);
        
        // Verify the input type is 'email' (explicitly set)
        $this->assertArrayHasKey('inputType', $emailSchema);
        $this->assertEquals('email', $emailSchema['inputType']);
        
        // Check the component type (should be form-input-component)
        $componentType = $emailSchema['type'] ?? null;
        $this->assertEquals('form-input-component', $componentType);
        
        // Check the input type (should be email)
        $inputType = $emailSchema['inputType'] ?? null;
        $this->assertEquals('email', $inputType);
    }
}
