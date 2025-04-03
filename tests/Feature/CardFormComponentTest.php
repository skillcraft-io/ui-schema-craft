<?php

namespace Skillcraft\UiSchemaCraft\Tests\Feature;

use Illuminate\Support\Facades\App;
use Orchestra\Testbench\TestCase;
use Skillcraft\UiSchemaCraft\ComponentResolver;
use Skillcraft\UiSchemaCraft\Facades\UiSchema;
use Skillcraft\UiSchemaCraft\Providers\UiSchemaCraftServiceProvider;
use Skillcraft\UiSchemaCraft\Services\UiSchemaCraftService;
use Skillcraft\UiSchemaCraft\Tests\Feature\TestComponents\CardFormComponent;
use Skillcraft\UiSchemaCraft\Tests\Feature\TestComponents\ButtonComponent;
use Skillcraft\UiSchemaCraft\Tests\Feature\TestComponents\FormInputComponent;
use Skillcraft\SchemaValidation\Contracts\ValidatorInterface;
use Skillcraft\SchemaState\Contracts\StateManagerInterface;

class CardFormComponentTest extends TestCase
{
    protected $mockValidator;
    protected $mockStateManager;
    
    /**
     * Get package providers.
     *
     * @param \Illuminate\Foundation\Application $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            UiSchemaCraftServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application $app
     * @return void
     */
    protected function defineEnvironment($app): void
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
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
                // Default to valid for test validations
                return true;
            }));
            
        $this->mockStateManager = $this->createMock(StateManagerInterface::class);
        $resolver = $this->app->make(ComponentResolver::class);
        
        // Create service and bind it to the container
        $service = new UiSchemaCraftService($this->mockStateManager, $resolver, $this->mockValidator);
        $this->app->instance('ui-schema', $service);
        
        // Register test components namespace
        $resolver->registerNamespace('Skillcraft\\UiSchemaCraft\\Tests\\Feature\\TestComponents');
        
        // Register specific test components
        $service->registerComponent(CardFormComponent::class);
        $service->registerComponent(ButtonComponent::class);
        $service->registerComponent(FormInputComponent::class);
    }
    
    /**
     * @test
     */
    public function it_registers_and_resolves_card_form_component()
    {
        // Create a component instance with mock validator to get its type
        $cardComponent = new CardFormComponent($this->mockValidator);
        $componentType = $cardComponent->getType();
        
        // Create component instance by type
        $component = UiSchema::createComponent($componentType);
        
        // Check that it's the right type
        $this->assertInstanceOf(CardFormComponent::class, $component);
        
        // Verify component type
        $this->assertEquals('card-form-component', $component->getType());
    }
    
    /**
     * @test
     */
    public function it_provides_correct_vueform_compatible_schema()
    {
        // Create component instance by type
        $cardType = (new CardFormComponent($this->mockValidator))->getType();
        $component = UiSchema::createComponent($cardType);
        
        // Configure a sample form
        $component->configureForm([
            'name' => [
                'type' => 'text',
                'label' => 'Full Name',
                'placeholder' => 'Enter your full name',
                'rules' => 'required'
            ],
            'email' => [
                'type' => 'email',
                'label' => 'Email Address',
                'placeholder' => 'your@email.com',
                'rules' => 'required|email'
            ],
            'message' => [
                'type' => 'textarea',
                'label' => 'Your Message',
                'placeholder' => 'Enter your message here',
                'rules' => 'required'
            ]
        ]);
        
        // Convert to array
        $schema = $component->toArray();
        
        // Verify core component properties
        $this->assertEquals('card-form-component', $schema['type']);
        $this->assertEquals('card-component', $schema['component']);
        
        // Verify formSchema structure is Vueform compatible
        $this->assertArrayHasKey('formSchema', $schema);
        $this->assertArrayHasKey('fields', $schema['formSchema']);
        $this->assertArrayHasKey('buttons', $schema['formSchema']);
        
        // Verify fields
        $this->assertArrayHasKey('name', $schema['formSchema']['fields']);
        $this->assertArrayHasKey('email', $schema['formSchema']['fields']);
        $this->assertArrayHasKey('message', $schema['formSchema']['fields']);
        
        // Verify field properties match Vueform expectations
        $nameField = $schema['formSchema']['fields']['name'];
        $this->assertEquals('text', $nameField['type']);
        $this->assertEquals('Full Name', $nameField['label']);
        $this->assertEquals('required', $nameField['rules']);
        
        // Verify buttons
        $this->assertArrayHasKey('submit', $schema['formSchema']['buttons']);
        $this->assertEquals('Submit', $schema['formSchema']['buttons']['submit']['label']);
        $this->assertEquals('primary', $schema['formSchema']['buttons']['submit']['color']);
    }
    
    /**
     * @test
     */
    public function it_handles_property_values_correctly()
    {
        // Create component instance by type
        $cardType = (new CardFormComponent($this->mockValidator))->getType();
        $component = UiSchema::createComponent($cardType);
        
        // Set card properties
        $component->setProperty('title', 'Contact Form');
        $component->setProperty('subtitle', 'Get in touch with our team');
        $component->setProperty('cardVariant', 'primary');
        $component->setProperty('shadow', false);
        
        // Convert to array
        $schema = $component->toArray();
        
        // Verify direct property values
        $this->assertEquals('Contact Form', $schema['title']);
        $this->assertEquals('Get in touch with our team', $schema['subtitle']);
        $this->assertEquals('primary', $schema['cardVariant']); // Respects the value that was set
        $this->assertEquals(false, $schema['shadow']);
        
        // Verify default values are preserved for properties not explicitly set
        $this->assertEquals(true, $schema['bordered']);
        $this->assertEquals('normal', $schema['padding']);
    }
    
    /**
     * @test
     */
    public function it_supports_component_composition_with_children()
    {
        // Resolve the components by their types
        $cardType = (new CardFormComponent($this->mockValidator))->getType();
        $buttonType = (new ButtonComponent($this->mockValidator))->getType();
        $inputType = (new FormInputComponent($this->mockValidator))->getType();
        
        $cardForm = UiSchema::createComponent($cardType);
        $button = UiSchema::createComponent($buttonType);
        $input = UiSchema::createComponent($inputType);
        
        // Configure the button
        $button->setProperty('text', 'Custom Button');
        $button->setProperty('variant', 'success');
        
        // Configure the input
        $input->setProperty('label', 'Custom Input');
        $input->setProperty('type', 'email');
        
        // Add children to the card
        $cardForm->addChild($button, 'footer');
        $cardForm->addChild($input, 'body');
        
        // Convert to array
        $schema = $cardForm->toArray();
        
        // Verify children are included
        $this->assertArrayHasKey('children', $schema);
        $this->assertCount(2, $schema['children']);
        
        // Verify children have the correct properties
        $this->assertEquals('button-component', $schema['children'][0]['type']);
        $this->assertEquals('Custom Button', $schema['children'][0]['text']);
        $this->assertEquals('success', $schema['children'][0]['variant']);
        
        $this->assertEquals('form-input-component', $schema['children'][1]['type']);
        $this->assertEquals('Custom Input', $schema['children'][1]['label']);
        $this->assertEquals('email', $schema['children'][1]['inputType']);
    }
    
    /**
     * @test
     */
    public function it_allows_adding_individual_fields_to_form_schema()
    {
        // Create component instance by type
        $cardType = (new CardFormComponent($this->mockValidator))->getType();
        $component = UiSchema::createComponent($cardType);
        
        // Start with an empty form
        $component->setProperty('formSchema', ['fields' => [], 'buttons' => []]);
        
        // Add fields one by one
        $component->addField('first_name', [
            'type' => 'text',
            'label' => 'First Name',
            'rules' => 'required'
        ]);
        
        $component->addField('last_name', [
            'type' => 'text',
            'label' => 'Last Name',
            'rules' => 'required'
        ]);
        
        $component->addField('subscribe', [
            'type' => 'checkbox',
            'label' => 'Subscribe to newsletter',
            'default' => true
        ]);
        
        // Convert to array
        $schema = $component->toArray();
        
        // Verify fields were added correctly
        $this->assertCount(3, $schema['formSchema']['fields']);
        $this->assertArrayHasKey('first_name', $schema['formSchema']['fields']);
        $this->assertArrayHasKey('last_name', $schema['formSchema']['fields']);
        $this->assertArrayHasKey('subscribe', $schema['formSchema']['fields']);
        
        // Verify field properties
        $this->assertEquals('text', $schema['formSchema']['fields']['first_name']['type']);
        $this->assertEquals('Last Name', $schema['formSchema']['fields']['last_name']['label']);
        $this->assertEquals(true, $schema['formSchema']['fields']['subscribe']['default']);
    }
}
