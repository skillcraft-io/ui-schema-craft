<?php

namespace Skillcraft\UiSchemaCraft\Tests\Unit\Examples;

use Skillcraft\UiSchemaCraft\Tests\TestCase;
use Skillcraft\UiSchemaCraft\Examples\ContactFormSchema;

class ContactFormSchemaTest extends TestCase
{
    private ContactFormSchema $schema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->schema = new ContactFormSchema();
    }

    /** @test */
    public function it_has_correct_component_type()
    {
        $this->assertEquals('contact-form', $this->schema->getIdentifier());
        $this->assertEquals('form-component', $this->schema->getComponent());
    }

    /** @test */
    public function it_has_all_required_fields()
    {
        $properties = $this->schema->properties();
        
        $this->assertArrayHasKey('name', $properties);
        $this->assertArrayHasKey('email', $properties);
        $this->assertArrayHasKey('subject', $properties);
        $this->assertArrayHasKey('message', $properties);
    }

    /** @test */
    public function it_has_correct_default_values()
    {
        $properties = $this->schema->properties();
        
        $this->assertEquals('', $properties['name']['default']);
        $this->assertEquals('', $properties['email']['default']);
        $this->assertEquals('General Inquiry', $properties['subject']['default']);
        $this->assertEquals('', $properties['message']['default']);
    }

    /** @test */
    public function it_validates_email_format()
    {
        $properties = $this->schema->properties();
        
        $this->assertContains('email', $properties['email']['rules']);
    }

    /** @test */
    public function it_enforces_max_length_constraints()
    {
        $properties = $this->schema->properties();
        
        $this->assertContains('max:100', $properties['name']['rules']);
        $this->assertContains('max:200', $properties['subject']['rules']);
        $this->assertContains('max:1000', $properties['message']['rules']);
    }

    /** @test */
    public function it_provides_valid_example_data()
    {
        $exampleData = $this->schema->getExampleData();
        
        $this->assertIsString($exampleData['name']);
        $this->assertNotEmpty($exampleData['name']);
        
        $this->assertIsString($exampleData['email']);
        $this->assertStringContainsString('@', $exampleData['email']);
        
        $this->assertIsString($exampleData['subject']);
        $this->assertNotEmpty($exampleData['subject']);
        
        $this->assertIsString($exampleData['message']);
        $this->assertNotEmpty($exampleData['message']);
    }
}