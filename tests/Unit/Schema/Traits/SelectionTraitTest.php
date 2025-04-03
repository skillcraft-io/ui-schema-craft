<?php

namespace Skillcraft\UiSchemaCraft\Tests\Unit\Schema\Traits;

use Skillcraft\UiSchemaCraft\Tests\TestCase;
use Skillcraft\UiSchemaCraft\Schema\Property;
use Skillcraft\UiSchemaCraft\Schema\Traits\SelectionTrait;

class SelectionTraitTest extends TestCase
{
    /**
     * Test class that uses the SelectionTrait
     */
    private $traitUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test class that uses the trait
        $this->traitUser = new class {
            use SelectionTrait;
        };
    }

    public function testMultiSelectProperty(): void
    {
        $propertyName = 'categories';
        $propertyLabel = 'Product Categories';
        
        $property = $this->traitUser->multiSelect($propertyName, $propertyLabel);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('array', $property->getType());
        $this->assertEquals($propertyLabel, $property->getDescription());
    }
    
    public function testMultiSelectPropertyWithAutoLabel(): void
    {
        $propertyName = 'selected_toppings';
        
        $property = $this->traitUser->multiSelect($propertyName);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('array', $property->getType());
        $this->assertEquals('Selected Toppings', $property->getDescription());
    }
    
    public function testMultiSelectHasCorrectStructure(): void
    {
        $propertyName = 'tags';
        $propertyLabel = 'Article Tags';
        
        $property = $this->traitUser->multiSelect($propertyName, $propertyLabel);
        $attributes = $property->toArray();
        
        // Check if items is defined and has the correct structure
        $this->assertArrayHasKey('items', $attributes);
        $this->assertEquals('string', $attributes['items']['type']);
        
        // Check if properties is defined
        $this->assertArrayHasKey('properties', $attributes);
        $properties = $attributes['properties'];
        
        // Check required properties
        $this->assertArrayHasKey('options', $properties);
        $this->assertArrayHasKey('searchable', $properties);
        $this->assertArrayHasKey('clearable', $properties);
        $this->assertArrayHasKey('maxItems', $properties);
        
        // Check default values
        $this->assertEquals(true, $properties['searchable']['default']);
        $this->assertEquals(true, $properties['clearable']['default']);
        
        // Check options structure
        $this->assertEquals('array', $properties['options']['type']);
        $this->assertArrayHasKey('items', $properties['options']);
        $this->assertArrayHasKey('properties', $properties['options']['items']);
        
        $optionProps = $properties['options']['items']['properties'];
        $this->assertArrayHasKey('value', $optionProps);
        $this->assertArrayHasKey('label', $optionProps);
        $this->assertArrayHasKey('disabled', $optionProps);
    }
    
    public function testTreeSelectProperty(): void
    {
        $propertyName = 'departments';
        $propertyLabel = 'Company Departments';
        
        $property = $this->traitUser->treeSelect($propertyName, $propertyLabel);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('object', $property->getType());
        $this->assertEquals($propertyLabel, $property->getDescription());
    }
    
    public function testTreeSelectPropertyWithAutoLabel(): void
    {
        $propertyName = 'file_structure';
        
        $property = $this->traitUser->treeSelect($propertyName);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('object', $property->getType());
        $this->assertEquals('File Structure', $property->getDescription());
    }
    
    public function testTreeSelectHasCorrectStructure(): void
    {
        $propertyName = 'categories';
        $propertyLabel = 'Product Categories';
        
        $property = $this->traitUser->treeSelect($propertyName, $propertyLabel);
        $attributes = $property->toArray();
        
        // Check if properties is defined
        $this->assertArrayHasKey('properties', $attributes);
        $properties = $attributes['properties'];
        
        // Check required properties
        $this->assertArrayHasKey('value', $properties);
        $this->assertArrayHasKey('options', $properties);
        $this->assertArrayHasKey('multiple', $properties);
        $this->assertArrayHasKey('checkable', $properties);
        $this->assertArrayHasKey('expandAll', $properties);
        
        // Check default values
        $this->assertEquals(false, $properties['multiple']['default']);
        $this->assertEquals(false, $properties['checkable']['default']);
        $this->assertEquals(false, $properties['expandAll']['default']);
        
        // Check value structure
        $this->assertEquals('array', $properties['value']['type']);
        
        // Check options structure
        $this->assertEquals('array', $properties['options']['type']);
        $this->assertArrayHasKey('items', $properties['options']);
        $this->assertArrayHasKey('properties', $properties['options']['items']);
        
        $optionProps = $properties['options']['items']['properties'];
        $this->assertArrayHasKey('value', $optionProps);
        $this->assertArrayHasKey('label', $optionProps);
        $this->assertArrayHasKey('children', $optionProps);
        $this->assertArrayHasKey('disabled', $optionProps);
    }
    
    public function testComboboxProperty(): void
    {
        $propertyName = 'country';
        $propertyLabel = 'Select Country';
        
        $property = $this->traitUser->combobox($propertyName, $propertyLabel);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('object', $property->getType());
        $this->assertEquals($propertyLabel, $property->getDescription());
    }
    
    public function testComboboxPropertyWithAutoLabel(): void
    {
        $propertyName = 'preferred_language';
        
        $property = $this->traitUser->combobox($propertyName);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('object', $property->getType());
        $this->assertEquals('Preferred Language', $property->getDescription());
    }
    
    public function testComboboxHasCorrectStructure(): void
    {
        $propertyName = 'state';
        $propertyLabel = 'Select State';
        
        $property = $this->traitUser->combobox($propertyName, $propertyLabel);
        $attributes = $property->toArray();
        
        // Check if properties is defined
        $this->assertArrayHasKey('properties', $attributes);
        $properties = $attributes['properties'];
        
        // Check required properties
        $this->assertArrayHasKey('value', $properties);
        $this->assertArrayHasKey('options', $properties);
        $this->assertArrayHasKey('allowCustom', $properties);
        $this->assertArrayHasKey('searchable', $properties);
        $this->assertArrayHasKey('clearable', $properties);
        
        // Check default values
        $this->assertEquals(false, $properties['allowCustom']['default']);
        $this->assertEquals(true, $properties['searchable']['default']);
        $this->assertEquals(true, $properties['clearable']['default']);
        
        // Check value structure
        $this->assertEquals('string', $properties['value']['type']);
        
        // Check options structure
        $this->assertEquals('array', $properties['options']['type']);
        $this->assertArrayHasKey('items', $properties['options']);
        $this->assertArrayHasKey('properties', $properties['options']['items']);
        
        $optionProps = $properties['options']['items']['properties'];
        $this->assertArrayHasKey('value', $optionProps);
        $this->assertArrayHasKey('label', $optionProps);
        $this->assertArrayHasKey('group', $optionProps);
    }
    
    public function testAutocompleteProperty(): void
    {
        $propertyName = 'city';
        $propertyLabel = 'Select City';
        
        $property = $this->traitUser->autocomplete($propertyName, $propertyLabel);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('object', $property->getType());
        $this->assertEquals($propertyLabel, $property->getDescription());
    }
    
    public function testAutocompletePropertyWithAutoLabel(): void
    {
        $propertyName = 'search_term';
        
        $property = $this->traitUser->autocomplete($propertyName);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('object', $property->getType());
        $this->assertEquals('Search Term', $property->getDescription());
    }
    
    public function testAutocompleteHasCorrectStructure(): void
    {
        $propertyName = 'airport';
        $propertyLabel = 'Select Airport';
        
        $property = $this->traitUser->autocomplete($propertyName, $propertyLabel);
        $attributes = $property->toArray();
        
        // Check if properties is defined
        $this->assertArrayHasKey('properties', $attributes);
        $properties = $attributes['properties'];
        
        // Check required properties
        $this->assertArrayHasKey('value', $properties);
        $this->assertArrayHasKey('suggestions', $properties);
        $this->assertArrayHasKey('minChars', $properties);
        $this->assertArrayHasKey('debounce', $properties);
        $this->assertArrayHasKey('highlightMatch', $properties);
        
        // Check default values
        $this->assertEquals(1, $properties['minChars']['default']);
        $this->assertEquals(300, $properties['debounce']['default']);
        $this->assertEquals(true, $properties['highlightMatch']['default']);
        
        // Check value structure
        $this->assertEquals('string', $properties['value']['type']);
        
        // Check suggestions structure
        $this->assertEquals('array', $properties['suggestions']['type']);
        $this->assertArrayHasKey('items', $properties['suggestions']);
        $this->assertArrayHasKey('properties', $properties['suggestions']['items']);
        
        $suggestionProps = $properties['suggestions']['items']['properties'];
        $this->assertArrayHasKey('value', $suggestionProps);
        $this->assertArrayHasKey('label', $suggestionProps);
        $this->assertArrayHasKey('description', $suggestionProps);
    }
}
