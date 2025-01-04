<?php

namespace Skillcraft\UiSchemaCraft\Tests\Unit\Schema\Traits;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversClass;
use Skillcraft\UiSchemaCraft\Tests\TestCase;
use Skillcraft\UiSchemaCraft\Schema\PropertyBuilder;
use Skillcraft\UiSchemaCraft\Schema\Traits\MediaTrait;

#[CoversClass(MediaTrait::class)]
class MediaTraitTest extends TestCase
{
    protected PropertyBuilder $builder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->builder = new PropertyBuilder();
    }

    #[Test]
    public function it_creates_image_upload_property()
    {
        $property = $this->builder->imageUpload('product_image', 'Product Image');
        $schema = $property->toArray();

        $this->assertEquals('product_image', $schema['name']);
        $this->assertEquals('Product Image', $schema['description']);
        $this->assertEquals('object', $schema['type']);
        
        // Check basic properties
        $this->assertArrayHasKey('url', $schema['properties']);
        $this->assertArrayHasKey('thumbnail', $schema['properties']);
        $this->assertArrayHasKey('name', $schema['properties']);
        $this->assertArrayHasKey('size', $schema['properties']);
        $this->assertArrayHasKey('type', $schema['properties']);
        
        // Check dimensions object
        $this->assertArrayHasKey('dimensions', $schema['properties']);
        $dimensions = $schema['properties']['dimensions'];
        $this->assertEquals('object', $dimensions['type']);
        $this->assertArrayHasKey('width', $dimensions['properties']);
        $this->assertArrayHasKey('height', $dimensions['properties']);
        $this->assertEquals('number', $dimensions['properties']['width']['type']);
        $this->assertEquals('number', $dimensions['properties']['height']['type']);
        
        // Check upload constraints
        $this->assertArrayHasKey('maxSize', $schema['properties']);
        $this->assertEquals(5120, $schema['properties']['maxSize']['default']); // 5MB
        
        $this->assertArrayHasKey('allowedTypes', $schema['properties']);
        $allowedTypes = $schema['properties']['allowedTypes'];
        $this->assertEquals('array', $allowedTypes['type']);
        $this->assertEquals(['image/jpeg', 'image/png', 'image/gif'], $allowedTypes['default']);
        
        // Check image manipulation options
        $this->assertArrayHasKey('crop', $schema['properties']);
        $this->assertFalse($schema['properties']['crop']['default']);
        $this->assertArrayHasKey('aspectRatio', $schema['properties']);
    }

    #[Test]
    public function it_creates_image_upload_with_default_label()
    {
        $property = $this->builder->imageUpload('profile_photo');
        $schema = $property->toArray();

        $this->assertEquals('profile_photo', $schema['name']);
        $this->assertEquals('Profile Photo', $schema['description']);
        $this->assertEquals('object', $schema['type']);
    }

    #[Test]
    public function it_creates_media_gallery_property()
    {
        $property = $this->builder->mediaGallery('product_gallery', 'Product Gallery');
        $schema = $property->toArray();

        $this->assertEquals('product_gallery', $schema['name']);
        $this->assertEquals('Product Gallery', $schema['description']);
        $this->assertEquals('object', $schema['type']);
        
        // Check items array
        $this->assertArrayHasKey('items', $schema['properties']);
        $items = $schema['properties']['items'];
        $this->assertEquals('array', $items['type']);
        
        // Check gallery item properties
        $itemProperties = $items['items']['properties'];
        $this->assertArrayHasKey('url', $itemProperties);
        $this->assertArrayHasKey('thumbnail', $itemProperties);
        $this->assertArrayHasKey('type', $itemProperties);
        $this->assertArrayHasKey('title', $itemProperties);
        $this->assertArrayHasKey('description', $itemProperties);
        $this->assertArrayHasKey('metadata', $itemProperties);
        
        // Check property types
        $this->assertEquals('string', $itemProperties['url']['type']);
        $this->assertEquals('string', $itemProperties['thumbnail']['type']);
        $this->assertEquals('string', $itemProperties['type']['type']);
        $this->assertEquals('string', $itemProperties['title']['type']);
        $this->assertEquals('string', $itemProperties['description']['type']);
        $this->assertEquals('object', $itemProperties['metadata']['type']);
        
        // Check gallery options
        $this->assertArrayHasKey('layout', $schema['properties']);
        $layout = $schema['properties']['layout'];
        $this->assertEquals('string', $layout['type']);
        $this->assertEquals(['grid', 'list', 'masonry'], $layout['enum']);
        $this->assertEquals('grid', $layout['default']);
        
        $this->assertArrayHasKey('sortable', $schema['properties']);
        $this->assertTrue($schema['properties']['sortable']['default']);
        
        $this->assertArrayHasKey('maxItems', $schema['properties']);
        $this->assertEquals('number', $schema['properties']['maxItems']['type']);
    }

    #[Test]
    public function it_creates_media_gallery_with_default_label()
    {
        $property = $this->builder->mediaGallery('project_files');
        $schema = $property->toArray();

        $this->assertEquals('project_files', $schema['name']);
        $this->assertEquals('Project Files', $schema['description']);
        $this->assertEquals('object', $schema['type']);
    }

    #[Test]
    public function it_creates_avatar_property()
    {
        $property = $this->builder->avatar('user_avatar', 'User Avatar');
        $schema = $property->toArray();

        $this->assertEquals('user_avatar', $schema['name']);
        $this->assertEquals('User Avatar', $schema['description']);
        $this->assertEquals('object', $schema['type']);
        
        // Check basic properties
        $this->assertArrayHasKey('url', $schema['properties']);
        $this->assertArrayHasKey('initials', $schema['properties']);
        $this->assertArrayHasKey('color', $schema['properties']);
        
        // Check appearance options
        $this->assertArrayHasKey('size', $schema['properties']);
        $size = $schema['properties']['size'];
        $this->assertEquals('string', $size['type']);
        $this->assertEquals(['small', 'medium', 'large'], $size['enum']);
        $this->assertEquals('medium', $size['default']);
        
        $this->assertArrayHasKey('shape', $schema['properties']);
        $shape = $schema['properties']['shape'];
        $this->assertEquals('string', $shape['type']);
        $this->assertEquals(['circle', 'square'], $shape['enum']);
        $this->assertEquals('circle', $shape['default']);
        
        // Check status options
        $this->assertArrayHasKey('status', $schema['properties']);
        $status = $schema['properties']['status'];
        $this->assertEquals('string', $status['type']);
        $this->assertEquals(['online', 'offline', 'away', 'busy'], $status['enum']);
        
        // Check badge object
        $this->assertArrayHasKey('badge', $schema['properties']);
        $badge = $schema['properties']['badge'];
        $this->assertEquals('object', $badge['type']);
        $this->assertArrayHasKey('content', $badge['properties']);
        $this->assertArrayHasKey('color', $badge['properties']);
        $this->assertEquals('string', $badge['properties']['content']['type']);
        $this->assertEquals('string', $badge['properties']['color']['type']);
    }

    #[Test]
    public function it_creates_avatar_with_default_label()
    {
        $property = $this->builder->avatar('team_member');
        $schema = $property->toArray();

        $this->assertEquals('team_member', $schema['name']);
        $this->assertEquals('Team Member', $schema['description']);
        $this->assertEquals('object', $schema['type']);
    }
}
