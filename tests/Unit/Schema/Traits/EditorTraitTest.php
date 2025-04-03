<?php

namespace Skillcraft\UiSchemaCraft\Tests\Unit\Schema\Traits;

use Skillcraft\UiSchemaCraft\Tests\TestCase;
use Skillcraft\UiSchemaCraft\Schema\Property;
use Skillcraft\UiSchemaCraft\Schema\Traits\EditorTrait;

class EditorTraitTest extends TestCase
{
    /**
     * Test class that uses the EditorTrait
     */
    private $traitUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test class that uses the trait
        $this->traitUser = new class {
            use EditorTrait;
        };
    }

    public function testCodeEditorProperty(): void
    {
        $propertyName = 'codeField';
        $propertyDescription = 'Code Editor Field';
        
        $property = $this->traitUser->codeEditor($propertyName, $propertyDescription);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('object', $property->getType());
        $this->assertEquals($propertyDescription, $property->getDescription());
        
        // Check that the expected attributes are set
        $attributes = $property->toArray();
        $this->assertArrayHasKey('properties', $attributes);
        
        // Verify specific properties are present
        $expectedProperties = [
            'language', 'theme', 'value', 'readOnly', 
            'minimap', 'lineNumbers', 'wordWrap'
        ];
        
        foreach ($expectedProperties as $expectedProperty) {
            $this->assertArrayHasKey($expectedProperty, $attributes['properties']);
        }
        
        // Check default values
        $this->assertEquals('plaintext', $attributes['properties']['language']['default']);
        $this->assertEquals('vs', $attributes['properties']['theme']['default']);
        $this->assertEquals(false, $attributes['properties']['readOnly']['default']);
        $this->assertEquals(true, $attributes['properties']['minimap']['default']);
        $this->assertEquals(true, $attributes['properties']['lineNumbers']['default']);
        $this->assertEquals(false, $attributes['properties']['wordWrap']['default']);
    }

    public function testMarkdownProperty(): void
    {
        $propertyName = 'markdownField';
        $propertyLabel = 'Markdown Editor Field';
        
        $property = $this->traitUser->markdown($propertyName, $propertyLabel);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('object', $property->getType());
        $this->assertEquals($propertyLabel, $property->getDescription());
        
        // Check that the expected attributes are set
        $attributes = $property->toArray();
        $this->assertArrayHasKey('properties', $attributes);
        
        // Verify specific properties
        $this->assertArrayHasKey('value', $attributes['properties']);
        $this->assertArrayHasKey('preview', $attributes['properties']);
        $this->assertArrayHasKey('toolbar', $attributes['properties']);
        
        // Check toolbar structure
        $this->assertEquals('object', $attributes['properties']['toolbar']['type']);
        $this->assertArrayHasKey('properties', $attributes['properties']['toolbar']);
        
        // Check toolbar buttons
        $toolbarButtons = [
            'bold', 'italic', 'heading', 'code', 
            'quote', 'link', 'image', 'list'
        ];
        
        foreach ($toolbarButtons as $button) {
            $this->assertArrayHasKey($button, $attributes['properties']['toolbar']['properties']);
            $this->assertEquals(true, $attributes['properties']['toolbar']['properties'][$button]['default']);
        }
        
        // Check preview default value
        $this->assertEquals(true, $attributes['properties']['preview']['default']);
    }

    public function testJsonEditorProperty(): void
    {
        $propertyName = 'jsonField';
        $propertyLabel = 'JSON Editor Field';
        
        $property = $this->traitUser->jsonEditor($propertyName, $propertyLabel);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('object', $property->getType());
        $this->assertEquals($propertyLabel, $property->getDescription());
        
        // Check that the expected attributes are set
        $attributes = $property->toArray();
        $this->assertArrayHasKey('properties', $attributes);
        
        // Verify specific properties
        $expectedProperties = ['value', 'mode', 'schema', 'readOnly', 'indentation'];
        foreach ($expectedProperties as $expectedProperty) {
            $this->assertArrayHasKey($expectedProperty, $attributes['properties']);
        }
        
        // Check mode enum values
        $this->assertArrayHasKey('enum', $attributes['properties']['mode']);
        $this->assertEquals(['tree', 'code', 'form', 'text'], $attributes['properties']['mode']['enum']);
        
        // Check default values
        $this->assertEquals('tree', $attributes['properties']['mode']['default']);
        $this->assertEquals(false, $attributes['properties']['readOnly']['default']);
        $this->assertEquals(2, $attributes['properties']['indentation']['default']);
    }

    public function testMaskProperty(): void
    {
        $propertyName = 'maskField';
        $propertyLabel = 'Masked Input Field';
        
        $property = $this->traitUser->mask($propertyName, $propertyLabel);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('string', $property->getType());
        $this->assertEquals($propertyLabel, $property->getDescription());
        
        // Check that format is set to mask
        $attributes = $property->toArray();
        $this->assertArrayHasKey('format', $attributes);
        $this->assertEquals('mask', $attributes['format']);
    }
}
