<?php

namespace Skillcraft\UiSchemaCraft\Tests\Unit\Schema\Traits;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversClass;
use Skillcraft\UiSchemaCraft\Tests\TestCase;
use Skillcraft\UiSchemaCraft\Schema\PropertyBuilder;
use Skillcraft\UiSchemaCraft\Schema\Traits\SelectionTrait;

#[CoversClass(SelectionTrait::class)]
class SelectionTraitTest extends TestCase
{
    protected PropertyBuilder $builder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->builder = new PropertyBuilder();
    }

    #[Test]
    public function it_creates_multi_select_property()
    {
        $property = $this->builder->multiSelect('languages', 'Programming Languages');
        $schema = $property->toArray();

        $this->assertEquals('languages', $schema['name']);
        $this->assertEquals('Programming Languages', $schema['description']);
        $this->assertEquals('array', $schema['type']);
        
        // Check items type
        $items = $schema['items'];
        $this->assertEquals('string', $items['type']);
        
        // Check properties
        $properties = $schema['properties'];
        
        // Check options array
        $this->assertArrayHasKey('options', $properties);
        $options = $properties['options'];
        $this->assertEquals('array', $options['type']);
        $this->assertEquals('Available options', $options['description']);
        
        // Check option item structure
        $optionItems = $options['items'];
        $this->assertEquals('object', $optionItems['type']);
        $this->assertArrayHasKey('value', $optionItems['properties']);
        $this->assertArrayHasKey('label', $optionItems['properties']);
        $this->assertArrayHasKey('disabled', $optionItems['properties']);
        $this->assertEquals('string', $optionItems['properties']['value']['type']);
        $this->assertEquals('string', $optionItems['properties']['label']['type']);
        $this->assertEquals('boolean', $optionItems['properties']['disabled']['type']);
        
        // Check configuration options
        $this->assertArrayHasKey('searchable', $properties);
        $this->assertTrue($properties['searchable']['default']);
        $this->assertEquals('Enable search functionality', $properties['searchable']['description']);
        
        $this->assertArrayHasKey('clearable', $properties);
        $this->assertTrue($properties['clearable']['default']);
        $this->assertEquals('Allow clearing selection', $properties['clearable']['description']);
        
        $this->assertArrayHasKey('maxItems', $properties);
        $this->assertEquals('number', $properties['maxItems']['type']);
        $this->assertEquals('Maximum number of items that can be selected', $properties['maxItems']['description']);
    }

    #[Test]
    public function it_creates_multi_select_with_default_label()
    {
        $property = $this->builder->multiSelect('selected_categories');
        $schema = $property->toArray();

        $this->assertEquals('selected_categories', $schema['name']);
        $this->assertEquals('Selected Categories', $schema['description']);
        $this->assertEquals('array', $schema['type']);
    }

    #[Test]
    public function it_creates_tree_select_property()
    {
        $property = $this->builder->treeSelect('departments', 'Company Departments');
        $schema = $property->toArray();

        $this->assertEquals('departments', $schema['name']);
        $this->assertEquals('Company Departments', $schema['description']);
        $this->assertEquals('object', $schema['type']);
        
        // Check properties
        $properties = $schema['properties'];
        
        // Check value array
        $this->assertArrayHasKey('value', $properties);
        $value = $properties['value'];
        $this->assertEquals('array', $value['type']);
        $this->assertEquals('Selected values', $value['description']);
        
        // Check options array
        $this->assertArrayHasKey('options', $properties);
        $options = $properties['options'];
        $this->assertEquals('array', $options['type']);
        $this->assertEquals('Tree structure options', $options['description']);
        
        // Check option item structure
        $optionItems = $options['items'];
        $this->assertEquals('object', $optionItems['type']);
        $this->assertArrayHasKey('value', $optionItems['properties']);
        $this->assertArrayHasKey('label', $optionItems['properties']);
        $this->assertArrayHasKey('children', $optionItems['properties']);
        $this->assertArrayHasKey('disabled', $optionItems['properties']);
        $this->assertEquals('string', $optionItems['properties']['value']['type']);
        $this->assertEquals('string', $optionItems['properties']['label']['type']);
        $this->assertEquals('array', $optionItems['properties']['children']['type']);
        $this->assertEquals('boolean', $optionItems['properties']['disabled']['type']);
        
        // Check configuration options
        $this->assertArrayHasKey('multiple', $properties);
        $this->assertFalse($properties['multiple']['default']);
        $this->assertEquals('Allow multiple selections', $properties['multiple']['description']);
        
        $this->assertArrayHasKey('checkable', $properties);
        $this->assertFalse($properties['checkable']['default']);
        $this->assertEquals('Show checkboxes', $properties['checkable']['description']);
        
        $this->assertArrayHasKey('expandAll', $properties);
        $this->assertFalse($properties['expandAll']['default']);
        $this->assertEquals('Expand all nodes by default', $properties['expandAll']['description']);
    }

    #[Test]
    public function it_creates_tree_select_with_default_label()
    {
        $property = $this->builder->treeSelect('menu_structure');
        $schema = $property->toArray();

        $this->assertEquals('menu_structure', $schema['name']);
        $this->assertEquals('Menu Structure', $schema['description']);
        $this->assertEquals('object', $schema['type']);
    }

    #[Test]
    public function it_creates_combobox_property()
    {
        $property = $this->builder->combobox('country', 'Select Country');
        $schema = $property->toArray();

        $this->assertEquals('country', $schema['name']);
        $this->assertEquals('Select Country', $schema['description']);
        $this->assertEquals('object', $schema['type']);
        
        // Check properties
        $properties = $schema['properties'];
        
        // Check value
        $this->assertArrayHasKey('value', $properties);
        $value = $properties['value'];
        $this->assertEquals('string', $value['type']);
        $this->assertEquals('Selected value', $value['description']);
        
        // Check options array
        $this->assertArrayHasKey('options', $properties);
        $options = $properties['options'];
        $this->assertEquals('array', $options['type']);
        $this->assertEquals('Available options', $options['description']);
        
        // Check option item structure
        $optionItems = $options['items'];
        $this->assertEquals('object', $optionItems['type']);
        $this->assertArrayHasKey('value', $optionItems['properties']);
        $this->assertArrayHasKey('label', $optionItems['properties']);
        $this->assertArrayHasKey('group', $optionItems['properties']);
        $this->assertEquals('string', $optionItems['properties']['value']['type']);
        $this->assertEquals('string', $optionItems['properties']['label']['type']);
        $this->assertEquals('string', $optionItems['properties']['group']['type']);
        
        // Check configuration options
        $this->assertArrayHasKey('allowCustom', $properties);
        $this->assertFalse($properties['allowCustom']['default']);
        $this->assertEquals('Allow custom values', $properties['allowCustom']['description']);
        
        $this->assertArrayHasKey('searchable', $properties);
        $this->assertTrue($properties['searchable']['default']);
        $this->assertEquals('Enable search functionality', $properties['searchable']['description']);
        
        $this->assertArrayHasKey('clearable', $properties);
        $this->assertTrue($properties['clearable']['default']);
        $this->assertEquals('Allow clearing selection', $properties['clearable']['description']);
    }

    #[Test]
    public function it_creates_combobox_with_default_label()
    {
        $property = $this->builder->combobox('preferred_language');
        $schema = $property->toArray();

        $this->assertEquals('preferred_language', $schema['name']);
        $this->assertEquals('Preferred Language', $schema['description']);
        $this->assertEquals('object', $schema['type']);
    }

    #[Test]
    public function it_creates_autocomplete_property()
    {
        $property = $this->builder->autocomplete('city', 'Select City');
        $schema = $property->toArray();

        $this->assertEquals('city', $schema['name']);
        $this->assertEquals('Select City', $schema['description']);
        $this->assertEquals('object', $schema['type']);
        
        // Check properties
        $properties = $schema['properties'];
        
        // Check value
        $this->assertArrayHasKey('value', $properties);
        $value = $properties['value'];
        $this->assertEquals('string', $value['type']);
        $this->assertEquals('Selected value', $value['description']);
        
        // Check suggestions array
        $this->assertArrayHasKey('suggestions', $properties);
        $suggestions = $properties['suggestions'];
        $this->assertEquals('array', $suggestions['type']);
        $this->assertEquals('Suggestion items', $suggestions['description']);
        
        // Check suggestion item structure
        $suggestionItems = $suggestions['items'];
        $this->assertEquals('object', $suggestionItems['type']);
        $this->assertArrayHasKey('value', $suggestionItems['properties']);
        $this->assertArrayHasKey('label', $suggestionItems['properties']);
        $this->assertArrayHasKey('description', $suggestionItems['properties']);
        $this->assertEquals('string', $suggestionItems['properties']['value']['type']);
        $this->assertEquals('string', $suggestionItems['properties']['label']['type']);
        $this->assertEquals('string', $suggestionItems['properties']['description']['type']);
        
        // Check configuration options
        $this->assertArrayHasKey('minChars', $properties);
        $this->assertEquals('number', $properties['minChars']['type']);
        $this->assertEquals(1, $properties['minChars']['default']);
        $this->assertEquals('Minimum characters before showing suggestions', $properties['minChars']['description']);
        
        $this->assertArrayHasKey('debounce', $properties);
        $this->assertEquals('number', $properties['debounce']['type']);
        $this->assertEquals(300, $properties['debounce']['default']);
        $this->assertEquals('Delay before searching', $properties['debounce']['description']);
        
        $this->assertArrayHasKey('highlightMatch', $properties);
        $this->assertTrue($properties['highlightMatch']['default']);
        $this->assertEquals('Highlight matching text', $properties['highlightMatch']['description']);
    }

    #[Test]
    public function it_creates_autocomplete_with_default_label()
    {
        $property = $this->builder->autocomplete('search_location');
        $schema = $property->toArray();

        $this->assertEquals('search_location', $schema['name']);
        $this->assertEquals('Search Location', $schema['description']);
        $this->assertEquals('object', $schema['type']);
    }
}
