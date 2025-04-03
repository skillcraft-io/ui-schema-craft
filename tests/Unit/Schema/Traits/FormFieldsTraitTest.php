<?php

namespace Skillcraft\UiSchemaCraft\Tests\Unit\Schema\Traits;

use Skillcraft\UiSchemaCraft\Tests\TestCase;
use Skillcraft\UiSchemaCraft\Schema\Property;
use Skillcraft\UiSchemaCraft\Schema\Traits\FormFieldsTrait;

class FormFieldsTraitTest extends TestCase
{
    /**
     * Test class that uses the FormFieldsTrait
     */
    private $traitUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test class that uses the trait
        $this->traitUser = new class {
            use FormFieldsTrait;
            
            public array $properties = [];
            
            // Methods required by the trait
            public function object(string $name, ?string $description = null): Property
            {
                return new Property($name, 'object', $description);
            }
            
            public function string(string $name, ?string $description = null): Property
            {
                return new Property($name, 'string', $description);
            }
        };
    }

    public function testTextFieldProperty(): void
    {
        $propertyName = 'userInput';
        $propertyLabel = 'User Input';
        $placeholder = 'Enter text here';
        
        $property = $this->traitUser->textField($propertyName, $propertyLabel, $placeholder);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('object', $property->getType());
        $this->assertEquals($propertyLabel, $property->getDescription());
        
        // Check that property was added to properties array
        $this->assertArrayHasKey($propertyName, $this->traitUser->properties);
        $this->assertSame($property, $this->traitUser->properties[$propertyName]);
        
        // Check structure
        $attributes = $property->toArray();
        $this->assertIsArray($attributes);
        
        // Builder should have created value and placeholder properties
        if (isset($attributes['properties'])) {
            $this->assertArrayHasKey('value', $attributes['properties']);
            $this->assertArrayHasKey('placeholder', $attributes['properties']);
            $this->assertEquals($placeholder, $attributes['properties']['placeholder']['default']);
        }
    }
    
    public function testTextFieldWithDefaultLabel(): void
    {
        $propertyName = 'user_name';
        
        $property = $this->traitUser->textField($propertyName);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('object', $property->getType());
        $this->assertEquals('User Name', $property->getDescription());
    }
    
    public function testEmailProperty(): void
    {
        $propertyName = 'userEmail';
        $propertyLabel = 'User Email';
        
        $property = $this->traitUser->email($propertyName, $propertyLabel);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('string', $property->getType());
        $this->assertEquals($propertyLabel, $property->getDescription());
        
        // Check format is set to email
        $attributes = $property->toArray();
        $this->assertArrayHasKey('format', $attributes);
        $this->assertEquals('email', $attributes['format']);
    }
    
    public function testEmailWithDefaultLabel(): void
    {
        $propertyName = 'contactEmail';
        
        $property = $this->traitUser->email($propertyName);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals('Email Address', $property->getDescription());
    }

    public function testPasswordProperty(): void
    {
        $propertyName = 'userPassword';
        $propertyLabel = 'User Password';
        
        $property = $this->traitUser->password($propertyName, $propertyLabel);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('string', $property->getType());
        $this->assertEquals($propertyLabel, $property->getDescription());
        
        // Check format is set to password
        $attributes = $property->toArray();
        $this->assertArrayHasKey('format', $attributes);
        $this->assertEquals('password', $attributes['format']);
    }
    
    public function testPasswordWithDefaultLabel(): void
    {
        $propertyName = 'userPass';
        
        $property = $this->traitUser->password($propertyName);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals('Password', $property->getDescription());
    }

    public function testPhoneProperty(): void
    {
        $propertyName = 'userPhone';
        $propertyLabel = 'User Phone';
        
        $property = $this->traitUser->phone($propertyName, $propertyLabel);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('string', $property->getType());
        $this->assertEquals($propertyLabel, $property->getDescription());
        
        // Check format is set to phone
        $attributes = $property->toArray();
        $this->assertArrayHasKey('format', $attributes);
        $this->assertEquals('phone', $attributes['format']);
    }
    
    public function testPhoneWithDefaultLabel(): void
    {
        $propertyName = 'mobileNumber';
        
        $property = $this->traitUser->phone($propertyName);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals('Contact Number', $property->getDescription());
    }

    public function testUrlProperty(): void
    {
        $propertyName = 'websiteUrl';
        $propertyLabel = 'Website URL';
        
        $property = $this->traitUser->url($propertyName, $propertyLabel);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('object', $property->getType());
        $this->assertEquals('URL field', $property->getDescription());
        
        // Check URL pattern exists
        $attributes = $property->toArray();
        $this->assertArrayHasKey('pattern', $attributes);
        $this->assertNotEmpty($attributes['pattern']);
        
        // Check requireHttps property is included
        if (isset($attributes['properties'])) {
            $this->assertArrayHasKey('requireHttps', $attributes['properties']);
            $this->assertEquals(false, $attributes['properties']['requireHttps']['default']);
        }
    }

    public function testColorProperty(): void
    {
        $propertyName = 'themeColor';
        $propertyLabel = 'Theme Color';
        
        $property = $this->traitUser->color($propertyName, $propertyLabel);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('object', $property->getType());
        $this->assertEquals('Color picker field', $property->getDescription());
        
        // Check color pattern exists
        $attributes = $property->toArray();
        $this->assertArrayHasKey('pattern', $attributes);
        $this->assertNotEmpty($attributes['pattern']);
        
        // Check color picker specific properties
        if (isset($attributes['properties'])) {
            $this->assertArrayHasKey('format', $attributes['properties']);
            $this->assertArrayHasKey('alpha', $attributes['properties']);
            $this->assertArrayHasKey('swatches', $attributes['properties']);
            
            $this->assertEquals('hex', $attributes['properties']['format']['default']);
            $this->assertEquals(false, $attributes['properties']['alpha']['default']);
            
            // Check swatches configuration
            $this->assertArrayHasKey('properties', $attributes['properties']['swatches']);
            $this->assertArrayHasKey('enabled', $attributes['properties']['swatches']['properties']);
            $this->assertEquals(true, $attributes['properties']['swatches']['properties']['enabled']['default']);
        }
    }

    public function testFileProperty(): void
    {
        $propertyName = 'userDocument';
        $propertyLabel = 'User Document';
        
        $property = $this->traitUser->file($propertyName, $propertyLabel);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('object', $property->getType());
        $this->assertEquals($propertyLabel, $property->getDescription());
        
        // Check file structure
        $attributes = $property->toArray();
        $this->assertArrayHasKey('properties', $attributes);
        
        $expectedProperties = ['name', 'size', 'type', 'lastModified', 'preview', 'progress'];
        foreach ($expectedProperties as $expectedProperty) {
            $this->assertArrayHasKey($expectedProperty, $attributes['properties']);
        }
        
        // Check types and formats
        $this->assertEquals('string', $attributes['properties']['name']['type']);
        $this->assertEquals('number', $attributes['properties']['size']['type']);
        $this->assertEquals('date-time', $attributes['properties']['lastModified']['format']);
        $this->assertEquals('number', $attributes['properties']['progress']['type']);
        $this->assertEquals(0, $attributes['properties']['progress']['minimum']);
        $this->assertEquals(100, $attributes['properties']['progress']['maximum']);
    }

    public function testRichTextProperty(): void
    {
        $propertyName = 'contentField';
        $propertyLabel = 'Content Field';
        
        $property = $this->traitUser->richText($propertyName, $propertyLabel);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('object', $property->getType());
        $this->assertEquals($propertyLabel, $property->getDescription());
        
        // Check rich text structure
        $attributes = $property->toArray();
        
        // Builder should have created these properties
        if (isset($attributes['properties'])) {
            $expectedProperties = ['value', 'toolbar', 'plugins', 'options'];
            foreach ($expectedProperties as $expectedProperty) {
                $this->assertArrayHasKey($expectedProperty, $attributes['properties']);
            }
            
            // Check toolbar configuration
            $this->assertArrayHasKey('properties', $attributes['properties']['toolbar']);
            $this->assertArrayHasKey('enabled', $attributes['properties']['toolbar']['properties']);
            $this->assertArrayHasKey('position', $attributes['properties']['toolbar']['properties']);
            $this->assertEquals(true, $attributes['properties']['toolbar']['properties']['enabled']['default']);
            $this->assertEquals('top', $attributes['properties']['toolbar']['properties']['position']['default']);
            
            // Check plugins configuration
            $this->assertArrayHasKey('properties', $attributes['properties']['plugins']);
            $this->assertArrayHasKey('enabled', $attributes['properties']['plugins']['properties']);
            $this->assertEquals(true, $attributes['properties']['plugins']['properties']['enabled']['default']);
            
            // Check options
            $this->assertArrayHasKey('properties', $attributes['properties']['options']);
            $this->assertArrayHasKey('readonly', $attributes['properties']['options']['properties']);
            $this->assertArrayHasKey('autofocus', $attributes['properties']['options']['properties']);
            $this->assertEquals(false, $attributes['properties']['options']['properties']['readonly']['default']);
            $this->assertEquals(false, $attributes['properties']['options']['properties']['autofocus']['default']);
        }
    }
    
    public function testRichTextWithDefaultLabel(): void
    {
        $propertyName = 'article_content';
        
        $property = $this->traitUser->richText($propertyName);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals('Article Content', $property->getDescription());
    }
}
