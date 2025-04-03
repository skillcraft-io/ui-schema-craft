<?php

namespace Skillcraft\UiSchemaCraft\Tests\Unit\Schema\Traits;

use Skillcraft\UiSchemaCraft\Tests\TestCase;
use Skillcraft\UiSchemaCraft\Schema\Property;
use Skillcraft\UiSchemaCraft\Schema\PropertyBuilder;
use Skillcraft\UiSchemaCraft\Schema\Traits\MediaTrait;

class MediaTraitTest extends TestCase
{
    /**
     * Test class that uses the MediaTrait
     */
    private $traitUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test class that uses the trait
        $this->traitUser = new class {
            use MediaTrait;
            
            public function object(string $name, ?string $description = null): Property
            {
                return new Property($name, 'object', $description);
            }
            
            public function withBuilder(callable $callback): mixed
            {
                $builder = new PropertyBuilder();
                $callback($builder);
                return $this;
            }
        };
    }

    public function testImageUploadProperty(): void
    {
        $propertyName = 'productImage';
        $propertyLabel = 'Product Image';
        
        $property = $this->traitUser->imageUpload($propertyName, $propertyLabel);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('object', $property->getType());
        $this->assertEquals($propertyLabel, $property->getDescription());
    }
    
    public function testImageUploadPropertyWithAutoLabel(): void
    {
        $propertyName = 'profile_picture';
        
        $property = $this->traitUser->imageUpload($propertyName);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('object', $property->getType());
        $this->assertEquals('Profile Picture', $property->getDescription());
    }
    
    public function testImageUploadHasCorrectStructure(): void
    {
        $propertyName = 'avatarUpload';
        $propertyLabel = 'Avatar Upload';
        
        // Create a real property object with the proper builder
        $mockTraitUser = new class {
            use MediaTrait;
            
            public function object(string $name, ?string $description = null): Property
            {
                return new Property($name, 'object', $description);
            }
        };
        
        $property = $mockTraitUser->imageUpload($propertyName, $propertyLabel);
        $attributes = $property->toArray();
        
        // Check if builder added the required properties
        $this->assertArrayHasKey('properties', $attributes);
        if (isset($attributes['properties'])) {
            // Check basic image properties
            $expectedBasicProps = ['url', 'thumbnail', 'name', 'size', 'type'];
            foreach ($expectedBasicProps as $prop) {
                $this->assertArrayHasKey($prop, $attributes['properties']);
            }
            
            // Check dimensions object
            $this->assertArrayHasKey('dimensions', $attributes['properties']);
            $this->assertEquals('object', $attributes['properties']['dimensions']['type']);
            $this->assertArrayHasKey('properties', $attributes['properties']['dimensions']);
            $dimensionProps = $attributes['properties']['dimensions']['properties'];
            $this->assertArrayHasKey('width', $dimensionProps);
            $this->assertArrayHasKey('height', $dimensionProps);
            
            // Check maxSize property with default
            $this->assertArrayHasKey('maxSize', $attributes['properties']);
            $this->assertEquals('number', $attributes['properties']['maxSize']['type']);
            $this->assertEquals(5120, $attributes['properties']['maxSize']['default']);
            
            // Check allowedTypes array
            $this->assertArrayHasKey('allowedTypes', $attributes['properties']);
            $this->assertEquals('array', $attributes['properties']['allowedTypes']['type']);
            $this->assertEquals(['image/jpeg', 'image/png', 'image/gif'], 
                $attributes['properties']['allowedTypes']['default']);
            
            // Check crop boolean
            $this->assertArrayHasKey('crop', $attributes['properties']);
            $this->assertEquals('boolean', $attributes['properties']['crop']['type']);
            $this->assertEquals(false, $attributes['properties']['crop']['default']);
            
            // Check aspectRatio property
            $this->assertArrayHasKey('aspectRatio', $attributes['properties']);
            $this->assertEquals('number', $attributes['properties']['aspectRatio']['type']);
        }
    }

    public function testMediaGalleryProperty(): void
    {
        $propertyName = 'productGallery';
        $propertyLabel = 'Product Gallery';
        
        $property = $this->traitUser->mediaGallery($propertyName, $propertyLabel);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('object', $property->getType());
        $this->assertEquals($propertyLabel, $property->getDescription());
    }
    
    public function testMediaGalleryPropertyWithAutoLabel(): void
    {
        $propertyName = 'photo_gallery';
        
        $property = $this->traitUser->mediaGallery($propertyName);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('object', $property->getType());
        $this->assertEquals('Photo Gallery', $property->getDescription());
    }
    
    public function testMediaGalleryHasCorrectStructure(): void
    {
        $propertyName = 'projectGallery';
        $propertyLabel = 'Project Gallery';
        
        // Create a real property object with the proper builder
        $mockTraitUser = new class {
            use MediaTrait;
            
            public function object(string $name, ?string $description = null): Property
            {
                return new Property($name, 'object', $description);
            }
        };
        
        $property = $mockTraitUser->mediaGallery($propertyName, $propertyLabel);
        $attributes = $property->toArray();
        
        // Check if builder added the required properties
        $this->assertArrayHasKey('properties', $attributes);
        if (isset($attributes['properties'])) {
            // Check items array
            $this->assertArrayHasKey('items', $attributes['properties']);
            $this->assertEquals('array', $attributes['properties']['items']['type']);
            
            // Check item structure
            $this->assertArrayHasKey('items', $attributes['properties']['items']);
            $this->assertEquals('object', $attributes['properties']['items']['items']['type']);
            $this->assertArrayHasKey('properties', $attributes['properties']['items']['items']);
            
            // Check item properties
            $itemProps = $attributes['properties']['items']['items']['properties'];
            $expectedItemProps = ['url', 'thumbnail', 'type', 'title', 'description', 'metadata'];
            foreach ($expectedItemProps as $prop) {
                $this->assertArrayHasKey($prop, $itemProps);
            }
            
            // Check layout property
            $this->assertArrayHasKey('layout', $attributes['properties']);
            $this->assertEquals('string', $attributes['properties']['layout']['type']);
            $this->assertEquals(['grid', 'list', 'masonry'], $attributes['properties']['layout']['enum']);
            $this->assertEquals('grid', $attributes['properties']['layout']['default']);
            
            // Check sortable property
            $this->assertArrayHasKey('sortable', $attributes['properties']);
            $this->assertEquals('boolean', $attributes['properties']['sortable']['type']);
            $this->assertEquals(true, $attributes['properties']['sortable']['default']);
            
            // Check maxItems property
            $this->assertArrayHasKey('maxItems', $attributes['properties']);
            $this->assertEquals('number', $attributes['properties']['maxItems']['type']);
        }
    }

    public function testAvatarProperty(): void
    {
        $propertyName = 'userAvatar';
        $propertyLabel = 'User Avatar';
        
        $property = $this->traitUser->avatar($propertyName, $propertyLabel);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('object', $property->getType());
        $this->assertEquals($propertyLabel, $property->getDescription());
    }
    
    public function testAvatarPropertyWithAutoLabel(): void
    {
        $propertyName = 'profile_avatar';
        
        $property = $this->traitUser->avatar($propertyName);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('object', $property->getType());
        $this->assertEquals('Profile Avatar', $property->getDescription());
    }
    
    public function testAvatarHasCorrectStructure(): void
    {
        $propertyName = 'teamMemberAvatar';
        $propertyLabel = 'Team Member Avatar';
        
        // Create a real property object with the proper builder
        $mockTraitUser = new class {
            use MediaTrait;
            
            public function object(string $name, ?string $description = null): Property
            {
                return new Property($name, 'object', $description);
            }
        };
        
        $property = $mockTraitUser->avatar($propertyName, $propertyLabel);
        $attributes = $property->toArray();
        
        // Check if builder added the required properties
        $this->assertArrayHasKey('properties', $attributes);
        if (isset($attributes['properties'])) {
            // Check basic avatar properties
            $expectedBasicProps = ['url', 'initials', 'color'];
            foreach ($expectedBasicProps as $prop) {
                $this->assertArrayHasKey($prop, $attributes['properties']);
            }
            
            // Check size property
            $this->assertArrayHasKey('size', $attributes['properties']);
            $this->assertEquals('string', $attributes['properties']['size']['type']);
            $this->assertEquals(['small', 'medium', 'large'], $attributes['properties']['size']['enum']);
            $this->assertEquals('medium', $attributes['properties']['size']['default']);
            
            // Check shape property
            $this->assertArrayHasKey('shape', $attributes['properties']);
            $this->assertEquals('string', $attributes['properties']['shape']['type']);
            $this->assertEquals(['circle', 'square'], $attributes['properties']['shape']['enum']);
            $this->assertEquals('circle', $attributes['properties']['shape']['default']);
            
            // Check status property
            $this->assertArrayHasKey('status', $attributes['properties']);
            $this->assertEquals('string', $attributes['properties']['status']['type']);
            $this->assertEquals(['online', 'offline', 'away', 'busy'], $attributes['properties']['status']['enum']);
            
            // Check badge object
            $this->assertArrayHasKey('badge', $attributes['properties']);
            $this->assertEquals('object', $attributes['properties']['badge']['type']);
            $this->assertArrayHasKey('properties', $attributes['properties']['badge']);
            $badgeProps = $attributes['properties']['badge']['properties'];
            $this->assertArrayHasKey('content', $badgeProps);
            $this->assertArrayHasKey('color', $badgeProps);
        }
    }
}
