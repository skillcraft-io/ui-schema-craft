<?php

namespace Skillcraft\UiSchemaCraft\Tests\Unit\Schema\Traits;

use Skillcraft\UiSchemaCraft\Tests\TestCase;
use Skillcraft\UiSchemaCraft\Schema\Property;
use Skillcraft\UiSchemaCraft\Schema\PropertyBuilder;
use Skillcraft\UiSchemaCraft\Schema\Traits\LayoutTrait;

class LayoutTraitTest extends TestCase
{
    /**
     * Test class that uses the LayoutTrait
     */
    private $traitUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test class that uses the trait
        $this->traitUser = new class {
            use LayoutTrait;
            
            public function new(): PropertyBuilder
            {
                return new PropertyBuilder();
            }
            
            // For testing callback functionality
            public function callTraitMethod(string $method, string $name, callable $callback, array $options = []): Property
            {
                return $this->$method($name, $callback, $options);
            }
        };
    }

    public function testGridProperty(): void
    {
        $propertyName = 'mainGrid';
        $propertyLabel = 'Main Grid Layout';
        
        $property = $this->traitUser->grid($propertyName, $propertyLabel);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('object', $property->getType());
        $this->assertEquals($propertyLabel, $property->getDescription());
        
        // Check properties structure
        $attributes = $property->toArray();
        $this->assertArrayHasKey('properties', $attributes);
        
        // Verify specific properties
        $expectedProperties = ['cols', 'gap', 'items', 'style'];
        foreach ($expectedProperties as $expectedProperty) {
            $this->assertArrayHasKey($expectedProperty, $attributes['properties']);
        }
        
        // Check default values
        $this->assertEquals('grid-cols-1 md:grid-cols-2 lg:grid-cols-3', $attributes['properties']['cols']['default']);
        $this->assertEquals('gap-4', $attributes['properties']['gap']['default']);
    }
    
    public function testGridPropertyWithOptions(): void
    {
        $propertyName = 'customGrid';
        $options = [
            'cols' => 'grid-cols-2',
            'gap' => 'gap-8'
        ];
        
        $property = $this->traitUser->grid($propertyName, null, $options);
        
        $attributes = $property->toArray();
        
        // Check custom options were applied
        $this->assertEquals('grid-cols-2', $attributes['properties']['cols']['default']);
        $this->assertEquals('gap-8', $attributes['properties']['gap']['default']);
    }
    
    public function testGridPropertyWithCallback(): void
    {
        $propertyName = 'callbackGrid';
        
        $property = $this->traitUser->callTraitMethod('grid', $propertyName, function($builder) {
            $builder->string('customField')
                ->description('A custom field');
        });
        
        $attributes = $property->toArray();
        
        // Check if callback added properties
        $this->assertArrayHasKey('customField', $attributes['properties']);
        $this->assertEquals('A custom field', $attributes['properties']['customField']['description']);
    }

    public function testFlexProperty(): void
    {
        $propertyName = 'mainFlex';
        $propertyLabel = 'Main Flex Layout';
        
        $property = $this->traitUser->flex($propertyName, $propertyLabel);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('object', $property->getType());
        $this->assertEquals($propertyLabel, $property->getDescription());
        
        // Check properties structure
        $attributes = $property->toArray();
        $this->assertArrayHasKey('properties', $attributes);
        
        // Verify specific properties
        $expectedProperties = ['justify', 'align', 'direction', 'wrap', 'spacing', 'items', 'style'];
        foreach ($expectedProperties as $expectedProperty) {
            $this->assertArrayHasKey($expectedProperty, $attributes['properties']);
        }
        
        // Check default values
        $this->assertEquals('start', $attributes['properties']['justify']['default']);
        $this->assertEquals('start', $attributes['properties']['align']['default']);
        $this->assertEquals('row', $attributes['properties']['direction']['default']);
        $this->assertEquals(false, $attributes['properties']['wrap']['default']);
        $this->assertEquals('space-x-4', $attributes['properties']['spacing']['default']);
    }
    
    public function testFlexPropertyWithOptions(): void
    {
        $propertyName = 'customFlex';
        $options = [
            'justify' => 'center',
            'align' => 'center',
            'direction' => 'column',
            'wrap' => true,
            'spacing' => 'space-y-8'
        ];
        
        $property = $this->traitUser->flex($propertyName, null, $options);
        
        $attributes = $property->toArray();
        
        // Check custom options were applied
        $this->assertEquals('center', $attributes['properties']['justify']['default']);
        $this->assertEquals('center', $attributes['properties']['align']['default']);
        $this->assertEquals('column', $attributes['properties']['direction']['default']);
        $this->assertEquals(true, $attributes['properties']['wrap']['default']);
        $this->assertEquals('space-y-8', $attributes['properties']['spacing']['default']);
    }
    
    public function testFlexPropertyWithCallback(): void
    {
        $propertyName = 'callbackFlex';
        
        $property = $this->traitUser->callTraitMethod('flex', $propertyName, function($builder) {
            $builder->string('customField')
                ->description('A custom field');
        });
        
        $attributes = $property->toArray();
        
        // Check if callback added properties
        $this->assertArrayHasKey('customField', $attributes['properties']);
        $this->assertEquals('A custom field', $attributes['properties']['customField']['description']);
    }

    public function testContainerProperty(): void
    {
        $propertyName = 'mainContainer';
        $propertyLabel = 'Main Container Layout';
        
        $property = $this->traitUser->container($propertyName, $propertyLabel);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('object', $property->getType());
        $this->assertEquals($propertyLabel, $property->getDescription());
        
        // Check properties structure
        $attributes = $property->toArray();
        $this->assertArrayHasKey('properties', $attributes);
        
        // Verify specific properties
        $expectedProperties = ['maxWidth', 'padding', 'margin', 'background', 'items', 'style'];
        foreach ($expectedProperties as $expectedProperty) {
            $this->assertArrayHasKey($expectedProperty, $attributes['properties']);
        }
        
        // Check default values
        $this->assertEquals('max-w-7xl', $attributes['properties']['maxWidth']['default']);
        $this->assertEquals('px-4 sm:px-6 lg:px-8', $attributes['properties']['padding']['default']);
        $this->assertEquals('mx-auto', $attributes['properties']['margin']['default']);
        $this->assertEquals('bg-white', $attributes['properties']['background']['default']);
    }
    
    public function testContainerPropertyWithOptions(): void
    {
        $propertyName = 'customContainer';
        $options = [
            'maxWidth' => 'max-w-full',
            'padding' => 'p-8',
            'margin' => 'm-4',
            'background' => 'bg-gray-100'
        ];
        
        $property = $this->traitUser->container($propertyName, null, $options);
        
        $attributes = $property->toArray();
        
        // Check custom options were applied
        $this->assertEquals('max-w-full', $attributes['properties']['maxWidth']['default']);
        $this->assertEquals('p-8', $attributes['properties']['padding']['default']);
        $this->assertEquals('m-4', $attributes['properties']['margin']['default']);
        $this->assertEquals('bg-gray-100', $attributes['properties']['background']['default']);
    }
    
    public function testContainerPropertyWithCallback(): void
    {
        $propertyName = 'callbackContainer';
        
        $property = $this->traitUser->callTraitMethod('container', $propertyName, function($builder) {
            $builder->string('customField')
                ->description('A custom field');
        });
        
        $attributes = $property->toArray();
        
        // Check if callback added properties
        $this->assertArrayHasKey('customField', $attributes['properties']);
        $this->assertEquals('A custom field', $attributes['properties']['customField']['description']);
    }

    public function testTabsProperty(): void
    {
        $propertyName = 'mainTabs';
        $propertyLabel = 'Main Tabs Layout';
        
        $property = $this->traitUser->tabs($propertyName, $propertyLabel);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('object', $property->getType());
        $this->assertEquals($propertyLabel, $property->getDescription());
        
        // Check properties structure
        $attributes = $property->toArray();
        $this->assertArrayHasKey('properties', $attributes);
        
        // Verify specific properties
        $expectedProperties = ['type', 'active', 'items', 'style'];
        foreach ($expectedProperties as $expectedProperty) {
            $this->assertArrayHasKey($expectedProperty, $attributes['properties']);
        }
        
        // Check default values
        $this->assertEquals('line', $attributes['properties']['type']['default']);
        $this->assertEquals('', $attributes['properties']['active']['default']);
        
        // Check items structure
        $this->assertArrayHasKey('properties', $attributes['properties']['items']['items']);
        $this->assertArrayHasKey('title', $attributes['properties']['items']['items']['properties']);
        $this->assertArrayHasKey('content', $attributes['properties']['items']['items']['properties']);
    }
    
    public function testTabsPropertyWithOptions(): void
    {
        $propertyName = 'customTabs';
        $options = [
            'type' => 'card',
            'active' => 'tab1'
        ];
        
        $property = $this->traitUser->tabs($propertyName, null, $options);
        
        $attributes = $property->toArray();
        
        // Check custom options were applied
        $this->assertEquals('card', $attributes['properties']['type']['default']);
        $this->assertEquals('tab1', $attributes['properties']['active']['default']);
    }
    
    public function testTabsPropertyWithCallback(): void
    {
        $propertyName = 'callbackTabs';
        
        $property = $this->traitUser->callTraitMethod('tabs', $propertyName, function($builder) {
            $builder->string('customField')
                ->description('A custom field');
        });
        
        $attributes = $property->toArray();
        
        // Check if callback added properties
        $this->assertArrayHasKey('customField', $attributes['properties']);
        $this->assertEquals('A custom field', $attributes['properties']['customField']['description']);
    }

    public function testStackProperty(): void
    {
        $propertyName = 'mainStack';
        $propertyLabel = 'Main Stack Layout';
        
        $property = $this->traitUser->stack($propertyName, $propertyLabel);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('object', $property->getType());
        $this->assertEquals($propertyLabel, $property->getDescription());
        
        // Check properties structure
        $attributes = $property->toArray();
        $this->assertArrayHasKey('properties', $attributes);
        
        // Verify specific properties
        $expectedProperties = ['align', 'direction', 'items', 'style'];
        foreach ($expectedProperties as $expectedProperty) {
            $this->assertArrayHasKey($expectedProperty, $attributes['properties']);
        }
        
        // Check default values
        $this->assertEquals('start', $attributes['properties']['align']['default']);
        $this->assertEquals('vertical', $attributes['properties']['direction']['default']);
        
        // Check spacing attribute
        $this->assertArrayHasKey('spacing', $attributes);
        $this->assertEquals('space-y-4', $attributes['spacing']);
    }
    
    public function testStackPropertyWithOptions(): void
    {
        $propertyName = 'customStack';
        $options = [
            'align' => 'center',
            'direction' => 'horizontal',
            'spacing' => 'space-x-8'
        ];
        
        $property = $this->traitUser->stack($propertyName, null, $options);
        
        $attributes = $property->toArray();
        
        // Check custom options were applied
        $this->assertEquals('center', $attributes['properties']['align']['default']);
        $this->assertEquals('horizontal', $attributes['properties']['direction']['default']);
        $this->assertEquals('space-x-8', $attributes['spacing']);
    }
    
    public function testStackPropertyWithCallback(): void
    {
        $propertyName = 'callbackStack';
        
        $property = $this->traitUser->callTraitMethod('stack', $propertyName, function($builder) {
            $builder->string('customField')
                ->description('A custom field');
        });
        
        $attributes = $property->toArray();
        
        // Check if callback added properties
        $this->assertArrayHasKey('customField', $attributes['properties']);
        $this->assertEquals('A custom field', $attributes['properties']['customField']['description']);
    }

    public function testSectionProperty(): void
    {
        $propertyName = 'mainSection';
        $propertyLabel = 'Main Section Layout';
        
        $property = $this->traitUser->section($propertyName, $propertyLabel);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('object', $property->getType());
        $this->assertEquals($propertyLabel, $property->getDescription());
        
        // Check properties structure
        $attributes = $property->toArray();
        $this->assertArrayHasKey('properties', $attributes);
        
        // Verify specific properties
        $expectedProperties = ['title', 'content', 'collapsible', 'items', 'style'];
        foreach ($expectedProperties as $expectedProperty) {
            $this->assertArrayHasKey($expectedProperty, $attributes['properties']);
        }
        
        // Check default values
        $this->assertEquals('', $attributes['properties']['title']['default']);
        $this->assertEquals(false, $attributes['properties']['collapsible']['default']);
        
        // Check class attribute
        $this->assertArrayHasKey('class', $attributes);
        $this->assertEquals('', $attributes['class']);
    }
    
    public function testSectionPropertyWithOptions(): void
    {
        $propertyName = 'customSection';
        $options = [
            'title' => 'Configuration',
            'collapsible' => true,
            'class' => 'border rounded p-4'
        ];
        
        $property = $this->traitUser->section($propertyName, null, $options);
        
        $attributes = $property->toArray();
        
        // Check custom options were applied
        $this->assertEquals('Configuration', $attributes['properties']['title']['default']);
        $this->assertEquals(true, $attributes['properties']['collapsible']['default']);
        $this->assertEquals('border rounded p-4', $attributes['class']);
    }
    
    public function testSectionPropertyWithCallback(): void
    {
        $propertyName = 'callbackSection';
        
        $property = $this->traitUser->callTraitMethod('section', $propertyName, function($builder) {
            $builder->string('customField')
                ->description('A custom field');
        });
        
        $attributes = $property->toArray();
        
        // Check if callback added properties
        $this->assertArrayHasKey('customField', $attributes['properties']);
        $this->assertEquals('A custom field', $attributes['properties']['customField']['description']);
    }
}
