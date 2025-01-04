<?php

namespace Skillcraft\UiSchemaCraft\Tests\Unit\Schema\Traits;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversClass;
use Skillcraft\UiSchemaCraft\Tests\TestCase;
use Skillcraft\UiSchemaCraft\Schema\PropertyBuilder;
use Skillcraft\UiSchemaCraft\Schema\Traits\EditorTrait;

#[CoversClass(EditorTrait::class)]
class EditorTraitTest extends TestCase
{
    protected PropertyBuilder $builder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->builder = new PropertyBuilder();
    }

    #[Test]
    public function it_creates_code_editor_property()
    {
        $property = $this->builder->codeEditor('script', 'Custom Script');
        $schema = $property->toArray();

        $this->assertEquals('script', $schema['name']);
        $this->assertEquals('Custom Script', $schema['description']);
        $this->assertEquals('object', $schema['type']);
        
        // Check editor properties
        $this->assertArrayHasKey('language', $schema['properties']);
        $this->assertArrayHasKey('theme', $schema['properties']);
        $this->assertArrayHasKey('value', $schema['properties']);
        $this->assertArrayHasKey('readOnly', $schema['properties']);
        $this->assertArrayHasKey('minimap', $schema['properties']);
        $this->assertArrayHasKey('lineNumbers', $schema['properties']);
        $this->assertArrayHasKey('wordWrap', $schema['properties']);
        
        // Check default values
        $this->assertEquals('plaintext', $schema['properties']['language']['default']);
        $this->assertEquals('vs', $schema['properties']['theme']['default']);
        $this->assertArrayNotHasKey('default', $schema['properties']['value']);
        $this->assertFalse($schema['properties']['readOnly']['default']);
        $this->assertTrue($schema['properties']['minimap']['default']);
        $this->assertTrue($schema['properties']['lineNumbers']['default']);
        $this->assertFalse($schema['properties']['wordWrap']['default']);
    }

    #[Test]
    public function it_creates_json_editor_property()
    {
        $property = $this->builder->jsonEditor('config', 'Configuration');
        $schema = $property->toArray();

        $this->assertEquals('config', $schema['name']);
        $this->assertEquals('Configuration', $schema['description']);
        $this->assertEquals('object', $schema['type']);
        
        // Check editor properties
        $this->assertArrayHasKey('value', $schema['properties']);
        $this->assertArrayHasKey('mode', $schema['properties']);
        $this->assertArrayHasKey('schema', $schema['properties']);
        $this->assertArrayHasKey('readOnly', $schema['properties']);
        $this->assertArrayHasKey('indentation', $schema['properties']);
        
        // Check default values
        $this->assertEquals('object', $schema['properties']['value']['type']);
        $this->assertEquals('tree', $schema['properties']['mode']['default']);
        $this->assertEquals('object', $schema['properties']['schema']['type']);
        $this->assertFalse($schema['properties']['readOnly']['default']);
        $this->assertEquals(2, $schema['properties']['indentation']['default']);
        
        // Check mode options
        $this->assertEquals(['tree', 'code', 'form', 'text'], $schema['properties']['mode']['enum']);
    }

    #[Test]
    public function it_creates_markdown_editor_property()
    {
        $property = $this->builder->markdown('content', 'Article Content');
        $schema = $property->toArray();

        $this->assertEquals('content', $schema['name']);
        $this->assertEquals('Article Content', $schema['description']);
        $this->assertEquals('object', $schema['type']);
        
        // Check editor properties
        $this->assertArrayHasKey('value', $schema['properties']);
        $this->assertArrayHasKey('preview', $schema['properties']);
        $this->assertArrayHasKey('toolbar', $schema['properties']);
        
        // Check default values
        $this->assertEquals('string', $schema['properties']['value']['type']);
        $this->assertTrue($schema['properties']['preview']['default']);
        
        // Check toolbar properties
        $this->assertArrayHasKey('properties', $schema['properties']['toolbar']);
        $toolbar = $schema['properties']['toolbar']['properties'];
        
        $this->assertTrue($toolbar['bold']['default']);
        $this->assertTrue($toolbar['italic']['default']);
        $this->assertTrue($toolbar['heading']['default']);
        $this->assertTrue($toolbar['code']['default']);
        $this->assertTrue($toolbar['quote']['default']);
        $this->assertTrue($toolbar['link']['default']);
        $this->assertTrue($toolbar['image']['default']);
        $this->assertTrue($toolbar['list']['default']);
    }

    #[Test]
    public function it_creates_mask_property()
    {
        $property = $this->builder->mask('phone', 'Phone Number');
        $schema = $property->toArray();

        $this->assertEquals('phone', $schema['name']);
        $this->assertEquals('Phone Number', $schema['description']);
        $this->assertEquals('string', $schema['type']);
        $this->assertEquals('mask', $schema['format']);
    }
}
