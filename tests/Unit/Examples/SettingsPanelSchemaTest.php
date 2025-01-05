<?php

namespace Skillcraft\UiSchemaCraft\Tests\Unit\Examples;

use Skillcraft\UiSchemaCraft\Tests\TestCase;
use Skillcraft\UiSchemaCraft\Examples\SettingsPanelSchema;

class SettingsPanelSchemaTest extends TestCase
{
    private SettingsPanelSchema $schema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->schema = new SettingsPanelSchema();
    }

    /** @test */
    public function it_has_correct_component_type()
    {
        $schema = $this->schema->toArray();
        $this->assertEquals('settings-panel', $this->schema->getIdentifier());
        $this->assertEquals('panel-component', $schema['component']);
    }

    /** @test */
    public function it_has_all_required_fields()
    {
        $properties = $this->schema->properties();
        
        $this->assertArrayHasKey('notifications', $properties);
        $this->assertArrayHasKey('theme', $properties);
        $this->assertArrayHasKey('language', $properties);
        $this->assertArrayHasKey('autoSave', $properties);
    }

    /** @test */
    public function it_has_correct_default_values()
    {
        $properties = $this->schema->properties();
        
        $this->assertTrue($properties['notifications']['default']);
        $this->assertEquals('system', $properties['theme']['default']);
        $this->assertEquals('en', $properties['language']['default']);
        $this->assertTrue($properties['autoSave']['default']);
    }

    /** @test */
    public function it_validates_theme_values()
    {
        $properties = $this->schema->properties();
        
        $this->assertContains('in:light,dark,system', $properties['theme']['rules']);
    }

    /** @test */
    public function it_validates_language_values()
    {
        $properties = $this->schema->properties();
        
        $this->assertContains('in:en,es,fr,de', $properties['language']['rules']);
    }

    /** @test */
    public function it_has_boolean_fields()
    {
        $properties = $this->schema->properties();
        
        $this->assertEquals('boolean', $properties['notifications']['type']);
        $this->assertEquals('boolean', $properties['autoSave']['type']);
    }

    /** @test */
    public function it_provides_valid_example_data()
    {
        $exampleData = $this->schema->getExampleData();
        
        $this->assertIsBool($exampleData['notifications']);
        $this->assertContains($exampleData['theme'], ['light', 'dark', 'system']);
        $this->assertContains($exampleData['language'], ['en', 'es', 'fr', 'de']);
        $this->assertIsBool($exampleData['autoSave']);
    }
}
