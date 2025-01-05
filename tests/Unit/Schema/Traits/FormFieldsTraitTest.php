<?php

namespace Skillcraft\UiSchemaCraft\Tests\Unit\Schema\Traits;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversClass;
use Skillcraft\UiSchemaCraft\Tests\TestCase;
use Skillcraft\UiSchemaCraft\Schema\PropertyBuilder;
use Skillcraft\UiSchemaCraft\Schema\Traits\FormFieldsTrait;

#[CoversClass(FormFieldsTrait::class)]
class FormFieldsTraitTest extends TestCase
{
    protected PropertyBuilder $builder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->builder = new PropertyBuilder();
    }

    #[Test]
    public function it_creates_text_field_property()
    {
        $property = $this->builder->textField('username', 'Username', 'Enter username');
        $schema = $property->toArray();

        $this->assertEquals('username', $schema['name']);
        $this->assertEquals('Username', $schema['description']);
        $this->assertEquals('object', $schema['type']);
        
        // Check properties
        $this->assertArrayHasKey('value', $schema['properties']);
        $this->assertEquals('string', $schema['properties']['value']['type']);
        $this->assertEquals('Text field value', $schema['properties']['value']['description']);
        
        // Check placeholder
        $this->assertArrayHasKey('placeholder', $schema['properties']);
        $this->assertEquals('Enter username', $schema['properties']['placeholder']['default']);
        $this->assertEquals('Placeholder text', $schema['properties']['placeholder']['description']);
    }

    #[Test]
    public function it_creates_text_field_without_placeholder()
    {
        $property = $this->builder->textField('username', 'Username');
        $schema = $property->toArray();

        $this->assertEquals('username', $schema['name']);
        $this->assertEquals('Username', $schema['description']);
        $this->assertEquals('object', $schema['type']);
        
        // Check properties
        $this->assertArrayHasKey('value', $schema['properties']);
        $this->assertEquals('string', $schema['properties']['value']['type']);
        $this->assertEquals('Text field value', $schema['properties']['value']['description']);
        
        // No placeholder
        $this->assertArrayNotHasKey('placeholder', $schema['properties']);
    }

    #[Test]
    public function it_creates_email_field_property()
    {
        $property = $this->builder->email('user_email', 'Email Address');
        $schema = $property->toArray();

        $this->assertEquals('user_email', $schema['name']);
        $this->assertEquals('Email Address', $schema['description']);
        $this->assertEquals('string', $schema['type']);
        $this->assertEquals('email', $schema['format']);
    }

    #[Test]
    public function it_creates_email_field_with_default_label()
    {
        $property = $this->builder->email('user_email');
        $schema = $property->toArray();

        $this->assertEquals('user_email', $schema['name']);
        $this->assertEquals('Email Address', $schema['description']);
        $this->assertEquals('string', $schema['type']);
        $this->assertEquals('email', $schema['format']);
    }

    #[Test]
    public function it_creates_password_field_property()
    {
        $property = $this->builder->password('user_password', 'Password');
        $schema = $property->toArray();

        $this->assertEquals('user_password', $schema['name']);
        $this->assertEquals('Password', $schema['description']);
        $this->assertEquals('string', $schema['type']);
        $this->assertEquals('password', $schema['format']);
    }

    #[Test]
    public function it_creates_password_field_with_default_label()
    {
        $property = $this->builder->password('user_password');
        $schema = $property->toArray();

        $this->assertEquals('user_password', $schema['name']);
        $this->assertEquals('Password', $schema['description']);
        $this->assertEquals('string', $schema['type']);
        $this->assertEquals('password', $schema['format']);
    }

    #[Test]
    public function it_creates_phone_field_property()
    {
        $property = $this->builder->phone('contact_number', 'Phone Number');
        $schema = $property->toArray();

        $this->assertEquals('contact_number', $schema['name']);
        $this->assertEquals('Phone Number', $schema['description']);
        $this->assertEquals('string', $schema['type']);
        $this->assertEquals('phone', $schema['format']);
    }

    #[Test]
    public function it_creates_phone_field_with_default_label()
    {
        $property = $this->builder->phone('contact_number');
        $schema = $property->toArray();

        $this->assertEquals('contact_number', $schema['name']);
        $this->assertEquals('Contact Number', $schema['description']);
        $this->assertEquals('string', $schema['type']);
        $this->assertEquals('phone', $schema['format']);
    }

    #[Test]
    public function it_creates_url_field_property()
    {
        $property = $this->builder->url('website', 'Website URL');
        $schema = $property->toArray();

        $this->assertEquals('website', $schema['name']);
        $this->assertEquals('URL field', $schema['description']);
        $this->assertEquals('object', $schema['type']);
        
        // Check URL pattern
        $expectedPattern = '^https?:\/\/(?:www\.)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b(?:[-a-zA-Z0-9()@:%_\+.~#?&\/=]*)$';
        $this->assertEquals($expectedPattern, $schema['pattern']);
        
        // Check builder properties
        $this->assertArrayHasKey('requireHttps', $schema['properties']);
        $this->assertEquals('boolean', $schema['properties']['requireHttps']['type']);
        $this->assertFalse($schema['properties']['requireHttps']['default']);
    }

    #[Test]
    public function it_creates_color_field_property()
    {
        $property = $this->builder->color('theme_color', 'Theme Color');
        $schema = $property->toArray();

        $this->assertEquals('theme_color', $schema['name']);
        $this->assertEquals('Color picker field', $schema['description']);
        $this->assertEquals('object', $schema['type']);
        
        // Check color pattern
        $this->assertEquals('^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$', $schema['pattern']);
        
        // Check format options
        $this->assertArrayHasKey('format', $schema['properties']);
        $this->assertEquals(['hex', 'rgb', 'hsl'], $schema['properties']['format']['enum']);
        $this->assertEquals('hex', $schema['properties']['format']['default']);
        
        // Check alpha channel
        $this->assertArrayHasKey('alpha', $schema['properties']);
        $this->assertFalse($schema['properties']['alpha']['default']);
        
        // Check swatches
        $this->assertArrayHasKey('swatches', $schema['properties']);
        $this->assertTrue($schema['properties']['swatches']['properties']['enabled']['default']);
        $this->assertArrayHasKey('colors', $schema['properties']['swatches']['properties']);
    }

    #[Test]
    public function it_creates_file_field_property()
    {
        $property = $this->builder->file('document', 'Upload Document');
        $schema = $property->toArray();

        $this->assertEquals('document', $schema['name']);
        $this->assertEquals('Upload Document', $schema['description']);
        $this->assertEquals('object', $schema['type']);
        
        // Check file properties
        $this->assertArrayHasKey('name', $schema['properties']);
        $this->assertArrayHasKey('size', $schema['properties']);
        $this->assertArrayHasKey('type', $schema['properties']);
        $this->assertArrayHasKey('lastModified', $schema['properties']);
        $this->assertArrayHasKey('preview', $schema['properties']);
        $this->assertArrayHasKey('progress', $schema['properties']);
        
        // Check types
        $this->assertEquals('string', $schema['properties']['name']['type']);
        $this->assertEquals('number', $schema['properties']['size']['type']);
        $this->assertEquals('string', $schema['properties']['type']['type']);
        $this->assertEquals('date-time', $schema['properties']['lastModified']['format']);
        $this->assertEquals('string', $schema['properties']['preview']['type']);
        
        // Check progress constraints
        $this->assertEquals(0, $schema['properties']['progress']['minimum']);
        $this->assertEquals(100, $schema['properties']['progress']['maximum']);
    }

    #[Test]
    public function it_creates_rich_text_field_property()
    {
        $property = $this->builder->richText('content', 'Article Content');
        $schema = $property->toArray();

        $this->assertEquals('content', $schema['name']);
        $this->assertEquals('Article Content', $schema['description']);
        $this->assertEquals('object', $schema['type']);
        
        // Check content value
        $this->assertArrayHasKey('value', $schema['properties']);
        $this->assertEquals('string', $schema['properties']['value']['type']);
        $this->assertEquals('Rich text content', $schema['properties']['value']['description']);
        
        // Check toolbar configuration
        $this->assertArrayHasKey('toolbar', $schema['properties']);
        $toolbar = $schema['properties']['toolbar'];
        $this->assertTrue($toolbar['properties']['enabled']['default']);
        $this->assertArrayHasKey('items', $toolbar['properties']);
        $this->assertEquals(['top', 'bottom'], $toolbar['properties']['position']['enum']);
        $this->assertEquals('top', $toolbar['properties']['position']['default']);
        
        // Check plugins configuration
        $this->assertArrayHasKey('plugins', $schema['properties']);
        $plugins = $schema['properties']['plugins'];
        $this->assertTrue($plugins['properties']['enabled']['default']);
        $this->assertArrayHasKey('items', $plugins['properties']);
        
        // Check editor options
        $this->assertArrayHasKey('options', $schema['properties']);
        $options = $schema['properties']['options'];
        $this->assertArrayHasKey('height', $options['properties']);
        $this->assertArrayHasKey('placeholder', $options['properties']);
        $this->assertArrayHasKey('readonly', $options['properties']);
        $this->assertArrayHasKey('autofocus', $options['properties']);
        $this->assertFalse($options['properties']['readonly']['default']);
        $this->assertFalse($options['properties']['autofocus']['default']);
    }
}
