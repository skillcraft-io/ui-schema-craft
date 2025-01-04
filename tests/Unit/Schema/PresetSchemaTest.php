<?php

namespace Skillcraft\UiSchemaCraft\Tests\Unit\Schema;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversClass;
use Skillcraft\UiSchemaCraft\Schema\PresetSchema;
use Skillcraft\UiSchemaCraft\Schema\Property;
use Skillcraft\UiSchemaCraft\Tests\TestCase;

#[CoversClass(PresetSchema::class)]
class PresetSchemaTest extends TestCase
{
    #[Test]
    public function it_creates_button_schema()
    {
        $schema = PresetSchema::button('test_button');
        $this->assertInstanceOf(Property::class, $schema);
        
        $data = $schema->toArray();
        $this->assertEquals('test_button', $data['name']);
        $this->assertEquals('object', $data['type']);
        
        $properties = $data['properties'];
        
        // Test core properties
        $this->assertArrayHasKey('text', $properties);
        $this->assertEquals('', $properties['text']['default'] ?? '');
        
        $this->assertArrayHasKey('type', $properties);
        $this->assertEquals('button', $properties['type']['default'] ?? '');
        $this->assertEquals(['button', 'submit', 'reset'], $properties['type']['enum'] ?? []);
        
        $this->assertArrayHasKey('disabled', $properties);
        $this->assertFalse($properties['disabled']['default'] ?? false);
        
        $this->assertArrayHasKey('variant', $properties);
        $this->assertEquals('primary', $properties['variant']['default'] ?? '');
        $this->assertEquals(['primary', 'secondary', 'outline', 'text'], $properties['variant']['enum'] ?? []);
        
        $this->assertArrayHasKey('size', $properties);
        $this->assertEquals('md', $properties['size']['default'] ?? '');
        $this->assertEquals(['sm', 'md', 'lg'], $properties['size']['enum'] ?? []);
        
        // Test icon properties
        $this->assertArrayHasKey('iconLeft', $properties);
        $this->assertEquals('', $properties['iconLeft']['default'] ?? '');
        
        $this->assertArrayHasKey('iconRight', $properties);
        $this->assertEquals('', $properties['iconRight']['default'] ?? '');
        
        // Test UI customization
        $this->assertArrayHasKey('container', $properties);
        $this->assertArrayHasKey('spacing', $properties);
        $this->assertArrayHasKey('text', $properties);
        $this->assertArrayHasKey('states', $properties);
    }

    #[Test]
    public function it_creates_button_schema_with_defaults()
    {
        $defaults = [
            'text' => 'Click me',
            'type' => 'submit',
            'disabled' => true,
            'variant' => 'secondary',
            'size' => 'lg',
            'iconLeft' => 'fa-check',
            'iconRight' => 'fa-arrow-right'
        ];

        $schema = PresetSchema::button('test_button', $defaults);
        $data = $schema->toArray();

        $this->assertEquals('Click me', $data['default'] ?? '');
        $this->assertEquals('submit', $data['properties']['type']['default'] ?? '');
        $this->assertTrue($data['properties']['disabled']['default'] ?? false);
        $this->assertEquals('secondary', $data['properties']['variant']['default'] ?? '');
        $this->assertEquals('lg', $data['properties']['size']['default'] ?? '');
        $this->assertEquals('fa-check', $data['properties']['iconLeft']['default'] ?? '');
        $this->assertEquals('fa-arrow-right', $data['properties']['iconRight']['default'] ?? '');
    }

    #[Test]
    public function it_creates_input_schema()
    {
        $schema = PresetSchema::input('test_input');
        $this->assertInstanceOf(Property::class, $schema);
        
        $data = $schema->toArray();
        $this->assertEquals('test_input', $data['name']);
        $this->assertEquals('object', $data['type']);
        
        $properties = $data['properties'];
        
        // Test core properties
        $this->assertArrayHasKey('type', $properties);
        $this->assertEquals('text', $properties['type']['default'] ?? '');
        $this->assertEquals(['text', 'email', 'password', 'number', 'tel', 'url'], $properties['type']['enum'] ?? []);
        
        $this->assertArrayHasKey('placeholder', $properties);
        $this->assertEquals('', $properties['placeholder']['default'] ?? '');
        
        $this->assertArrayHasKey('required', $properties);
        $this->assertFalse($properties['required']['default'] ?? false);
        
        $this->assertArrayHasKey('disabled', $properties);
        $this->assertFalse($properties['disabled']['default'] ?? false);
        
        // Test validation properties
        $this->assertArrayHasKey('pattern', $properties);
        $this->assertEquals('', $properties['pattern']['default'] ?? '');
        
        $this->assertArrayHasKey('min', $properties);
        $this->assertEquals('', $properties['min']['default'] ?? '');
        
        $this->assertArrayHasKey('max', $properties);
        $this->assertEquals('', $properties['max']['default'] ?? '');
        
        // Test UI customization
        $this->assertArrayHasKey('container', $properties);
        $this->assertArrayHasKey('spacing', $properties);
        $this->assertArrayHasKey('text', $properties);
        $this->assertArrayHasKey('states', $properties);
    }

    #[Test]
    public function it_creates_input_schema_with_defaults()
    {
        $defaults = [
            'type' => 'email',
            'placeholder' => 'Enter email',
            'required' => true,
            'disabled' => true,
            'pattern' => '[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$',
            'min' => '5',
            'max' => '50'
        ];

        $schema = PresetSchema::input('test_input', $defaults);
        $data = $schema->toArray();
        $properties = $data['properties'];
        
        $this->assertEquals('email', $properties['type']['default'] ?? '');
        $this->assertEquals('Enter email', $properties['placeholder']['default'] ?? '');
        $this->assertTrue($properties['required']['default'] ?? false);
        $this->assertTrue($properties['disabled']['default'] ?? false);
        $this->assertEquals('[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$', $properties['pattern']['default'] ?? '');
        $this->assertEquals('5', $properties['min']['default'] ?? '');
        $this->assertEquals('50', $properties['max']['default'] ?? '');
    }

    #[Test]
    public function it_creates_card_schema()
    {
        $schema = PresetSchema::card('test_card');
        $this->assertInstanceOf(Property::class, $schema);
        
        $data = $schema->toArray();
        $this->assertEquals('test_card', $data['name']);
        $this->assertEquals('object', $data['type']);
        
        $properties = $data['properties'];
        
        // Test core properties
        $this->assertArrayHasKey('title', $properties);
        $this->assertEquals('', $properties['title']['default'] ?? '');
        
        $this->assertArrayHasKey('subtitle', $properties);
        $this->assertEquals('', $properties['subtitle']['default'] ?? '');
        
        $this->assertArrayHasKey('image', $properties);
        $this->assertEquals('', $properties['image']['default'] ?? '');
        
        // Test UI customization
        $this->assertArrayHasKey('container', $properties);
        $this->assertArrayHasKey('spacing', $properties);
        $this->assertArrayHasKey('title', $properties);
        $this->assertArrayHasKey('subtitle', $properties);
        $this->assertArrayHasKey('states', $properties);
    }

    #[Test]
    public function it_creates_card_schema_with_defaults()
    {
        $defaults = [
            'title' => 'Card Title',
            'subtitle' => 'Card Subtitle',
            'image' => 'https://example.com/image.jpg'
        ];

        $schema = PresetSchema::card('test_card', $defaults);
        $data = $schema->toArray();
        
        $this->assertEquals('Card Title', $data['default'] ?? '');
        $this->assertEquals('Card Subtitle', $data['properties']['subtitle']['default'] ?? '');
        $this->assertEquals('https://example.com/image.jpg', $data['properties']['image']['default'] ?? '');
    }

    #[Test]
    public function it_creates_badge_schema()
    {
        $schema = PresetSchema::badge('test_badge');
        $this->assertInstanceOf(Property::class, $schema);
        
        $data = $schema->toArray();
        $this->assertEquals('test_badge', $data['name']);
        $this->assertEquals('object', $data['type']);
        
        $properties = $data['properties'];
        
        // Test core properties
        $this->assertArrayHasKey('text', $properties);
        $this->assertEquals('', $properties['text']['default'] ?? '');
        
        $this->assertArrayHasKey('variant', $properties);
        $this->assertEquals('info', $properties['variant']['default'] ?? '');
        $this->assertEquals(['success', 'warning', 'error', 'info'], $properties['variant']['enum'] ?? []);
        
        // Test UI customization
        $this->assertArrayHasKey('container', $properties);
        $this->assertArrayHasKey('spacing', $properties);
        $this->assertArrayHasKey('text', $properties);
    }

    #[Test]
    public function it_creates_badge_schema_with_defaults()
    {
        $defaults = [
            'text' => 'New',
            'variant' => 'success'
        ];

        $schema = PresetSchema::badge('test_badge', $defaults);
        $data = $schema->toArray();
        
        $this->assertEquals('New', $data['default'] ?? '');
        $this->assertEquals('success', $data['properties']['variant']['default'] ?? '');
    }

    #[Test]
    public function it_creates_alert_schema()
    {
        $schema = PresetSchema::alert('test_alert');
        $this->assertInstanceOf(Property::class, $schema);
        
        $data = $schema->toArray();
        $this->assertEquals('test_alert', $data['name']);
        $this->assertEquals('object', $data['type']);
        
        $properties = $data['properties'];
        
        // Test core properties
        $this->assertArrayHasKey('title', $properties);
        $this->assertEquals('', $properties['title']['default'] ?? '');
        
        $this->assertArrayHasKey('message', $properties);
        $this->assertEquals('', $properties['message']['default'] ?? '');
        
        $this->assertArrayHasKey('type', $properties);
        $this->assertEquals('info', $properties['type']['default'] ?? '');
        $this->assertEquals(['success', 'warning', 'error', 'info'], $properties['type']['enum'] ?? []);
        
        $this->assertArrayHasKey('dismissible', $properties);
        $this->assertTrue($properties['dismissible']['default'] ?? false);
        
        $this->assertArrayHasKey('icon', $properties);
        $this->assertEquals('fas fa-info-circle', $properties['icon']['default'] ?? '');
        
        // Test UI customization
        $this->assertArrayHasKey('container', $properties);
        $this->assertArrayHasKey('spacing', $properties);
        $this->assertArrayHasKey('title', $properties);
        $this->assertArrayHasKey('message', $properties);
    }

    #[Test]
    public function it_creates_alert_schema_with_defaults()
    {
        $defaults = [
            'title' => 'Alert Title',
            'message' => 'Alert Message',
            'type' => 'warning',
            'dismissible' => false,
            'icon' => 'fas fa-exclamation-triangle'
        ];

        $schema = PresetSchema::alert('test_alert', $defaults);
        $data = $schema->toArray();
        
        $this->assertEquals('Alert Title', $data['default'] ?? '');
        $this->assertEquals('Alert Message', $data['properties']['message']['default'] ?? '');
        $this->assertEquals('warning', $data['properties']['type']['default'] ?? '');
        $this->assertFalse($data['properties']['dismissible']['default'] ?? false);
        $this->assertEquals('fas fa-exclamation-triangle', $data['properties']['icon']['default'] ?? '');
    }

    #[Test]
    public function it_creates_modal_schema()
    {
        $schema = PresetSchema::modal('test_modal');
        $this->assertInstanceOf(Property::class, $schema);
        
        $data = $schema->toArray();
        $this->assertEquals('test_modal', $data['name']);
        $this->assertEquals('object', $data['type']);
        
        $properties = $data['properties'];
        
        // Test core properties
        $this->assertArrayHasKey('title', $properties);
        $this->assertEquals('', $properties['title']['default'] ?? '');
        
        $this->assertArrayHasKey('open', $properties);
        $this->assertFalse($properties['open']['default'] ?? false);
        
        $this->assertArrayHasKey('size', $properties);
        $this->assertEquals('md', $properties['size']['default'] ?? '');
        $this->assertEquals(['sm', 'md', 'lg', 'xl', 'full'], $properties['size']['enum'] ?? []);
        
        // Test UI customization
        $this->assertArrayHasKey('overlay', $properties);
        $this->assertArrayHasKey('container', $properties);
        $this->assertArrayHasKey('spacing', $properties);
        $this->assertArrayHasKey('title', $properties);
        $this->assertArrayHasKey('animation', $properties);
    }

    #[Test]
    public function it_creates_modal_schema_with_defaults()
    {
        $defaults = [
            'title' => 'Modal Title',
            'open' => true,
            'size' => 'lg'
        ];

        $schema = PresetSchema::modal('test_modal', $defaults);
        $data = $schema->toArray();

        $this->assertEquals('Modal Title', $data['default'] ?? '');
        $this->assertTrue($data['properties']['open']['default'] ?? false);
        $this->assertEquals('lg', $data['properties']['size']['default'] ?? '');
    }
}
