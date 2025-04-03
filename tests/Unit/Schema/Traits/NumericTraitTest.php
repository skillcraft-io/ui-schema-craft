<?php

namespace Skillcraft\UiSchemaCraft\Tests\Unit\Schema\Traits;

use Skillcraft\UiSchemaCraft\Tests\TestCase;
use Skillcraft\UiSchemaCraft\Schema\Property;
use Skillcraft\UiSchemaCraft\Schema\PropertyBuilder;
use Skillcraft\UiSchemaCraft\Schema\Traits\NumericTrait;

class NumericTraitTest extends TestCase
{
    /**
     * Test class that uses the NumericTrait
     */
    private $traitUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test class that uses the trait
        $this->traitUser = new class {
            use NumericTrait;
            
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

    public function testRangeProperty(): void
    {
        $propertyName = 'priceRange';
        $propertyLabel = 'Price Range';
        
        $property = $this->traitUser->range($propertyName, $propertyLabel);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('object', $property->getType());
        $this->assertEquals($propertyLabel, $property->getDescription());
    }
    
    public function testRangePropertyWithAutoLabel(): void
    {
        $propertyName = 'volume_control';
        
        $property = $this->traitUser->range($propertyName);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('object', $property->getType());
        $this->assertEquals('Volume Control', $property->getDescription());
    }
    
    public function testRangeHasCorrectStructure(): void
    {
        $propertyName = 'temperatureRange';
        $propertyLabel = 'Temperature Range';
        
        // Create a real property object with the proper builder
        $mockTraitUser = new class {
            use NumericTrait;
            
            public function object(string $name, ?string $description = null): Property
            {
                return new Property($name, 'object', $description);
            }
        };
        
        $property = $mockTraitUser->range($propertyName, $propertyLabel);
        $attributes = $property->toArray();
        
        // Check if builder added the required properties
        $this->assertArrayHasKey('properties', $attributes);
        if (isset($attributes['properties'])) {
            // Check main properties
            $expectedProps = ['value', 'min', 'max', 'step', 'showTicks', 'showValue'];
            foreach ($expectedProps as $prop) {
                $this->assertArrayHasKey($prop, $attributes['properties']);
            }
            
            // Check default values
            $this->assertEquals(0, $attributes['properties']['value']['default']);
            $this->assertEquals(0, $attributes['properties']['min']['default']);
            $this->assertEquals(100, $attributes['properties']['max']['default']);
            $this->assertEquals(1, $attributes['properties']['step']['default']);
            $this->assertEquals(false, $attributes['properties']['showTicks']['default']);
            $this->assertEquals(true, $attributes['properties']['showValue']['default']);
        }
    }
    
    public function testRatingProperty(): void
    {
        $propertyName = 'productRating';
        $propertyLabel = 'Product Rating';
        
        $property = $this->traitUser->rating($propertyName, $propertyLabel);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('object', $property->getType());
        $this->assertEquals($propertyLabel, $property->getDescription());
    }
    
    public function testRatingPropertyWithAutoLabel(): void
    {
        $propertyName = 'user_score';
        
        $property = $this->traitUser->rating($propertyName);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('object', $property->getType());
        $this->assertEquals('User Score', $property->getDescription());
    }
    
    public function testRatingHasCorrectStructure(): void
    {
        $propertyName = 'serviceRating';
        $propertyLabel = 'Service Rating';
        
        // Create a real property object with the proper builder
        $mockTraitUser = new class {
            use NumericTrait;
            
            public function object(string $name, ?string $description = null): Property
            {
                return new Property($name, 'object', $description);
            }
        };
        
        $property = $mockTraitUser->rating($propertyName, $propertyLabel);
        $attributes = $property->toArray();
        
        // Check if builder added the required properties
        $this->assertArrayHasKey('properties', $attributes);
        if (isset($attributes['properties'])) {
            // Check main properties
            $expectedProps = ['value', 'max', 'allowHalf', 'readonly', 'icon'];
            foreach ($expectedProps as $prop) {
                $this->assertArrayHasKey($prop, $attributes['properties']);
            }
            
            // Check default values
            $this->assertEquals(0, $attributes['properties']['value']['default']);
            $this->assertEquals(5, $attributes['properties']['max']['default']);
            $this->assertEquals(false, $attributes['properties']['allowHalf']['default']);
            $this->assertEquals(false, $attributes['properties']['readonly']['default']);
            
            // Check icon object structure
            $this->assertArrayHasKey('properties', $attributes['properties']['icon']);
            $iconProps = $attributes['properties']['icon']['properties'];
            
            $this->assertArrayHasKey('filled', $iconProps);
            $this->assertEquals('fas fa-star', $iconProps['filled']['default']);
            
            $this->assertArrayHasKey('empty', $iconProps);
            $this->assertEquals('far fa-star', $iconProps['empty']['default']);
            
            $this->assertArrayHasKey('color', $iconProps);
            $this->assertEquals('text-yellow-400', $iconProps['color']['default']);
        }
    }
    
    public function testCurrencyProperty(): void
    {
        $propertyName = 'productPrice';
        $propertyLabel = 'Product Price';
        
        $property = $this->traitUser->currency($propertyName, $propertyLabel);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('object', $property->getType());
        $this->assertEquals($propertyLabel, $property->getDescription());
    }
    
    public function testCurrencyPropertyWithAutoLabel(): void
    {
        $propertyName = 'item_cost';
        
        $property = $this->traitUser->currency($propertyName);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('object', $property->getType());
        $this->assertEquals('Item Cost', $property->getDescription());
    }
    
    public function testCurrencyHasCorrectStructure(): void
    {
        $propertyName = 'invoiceAmount';
        $propertyLabel = 'Invoice Amount';
        
        // Create a real property object with the proper builder
        $mockTraitUser = new class {
            use NumericTrait;
            
            public function object(string $name, ?string $description = null): Property
            {
                return new Property($name, 'object', $description);
            }
        };
        
        $property = $mockTraitUser->currency($propertyName, $propertyLabel);
        $attributes = $property->toArray();
        
        // Check if builder added the required properties
        $this->assertArrayHasKey('properties', $attributes);
        if (isset($attributes['properties'])) {
            // Check main properties
            $expectedProps = ['value', 'currency', 'locale', 'showSymbol'];
            foreach ($expectedProps as $prop) {
                $this->assertArrayHasKey($prop, $attributes['properties']);
            }
            
            // Check default values
            $this->assertEquals('USD', $attributes['properties']['currency']['default']);
            $this->assertEquals('en-US', $attributes['properties']['locale']['default']);
            $this->assertEquals(true, $attributes['properties']['showSymbol']['default']);
        }
    }
    
    public function testPercentageProperty(): void
    {
        $propertyName = 'discountRate';
        $propertyLabel = 'Discount Rate';
        
        $property = $this->traitUser->percentage($propertyName, $propertyLabel);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('object', $property->getType());
        $this->assertEquals($propertyLabel, $property->getDescription());
    }
    
    public function testPercentagePropertyWithAutoLabel(): void
    {
        $propertyName = 'completion_rate';
        
        $property = $this->traitUser->percentage($propertyName);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('object', $property->getType());
        $this->assertEquals('Completion Rate', $property->getDescription());
    }
    
    public function testPercentageHasCorrectStructure(): void
    {
        $propertyName = 'taxRate';
        $propertyLabel = 'Tax Rate';
        
        // Create a real property object with the proper builder
        $mockTraitUser = new class {
            use NumericTrait;
            
            public function object(string $name, ?string $description = null): Property
            {
                return new Property($name, 'object', $description);
            }
            
            // For testing the chained methods in percentage property
            public function number(string $name): object
            {
                return new class($name) {
                    private $name;
                    
                    public function __construct(string $name)
                    {
                        $this->name = $name;
                    }
                    
                    public function description(string $desc): self
                    {
                        return $this;
                    }
                    
                    public function addAttribute(string $key, $value): self
                    {
                        return $this;
                    }
                    
                    public function min(int $value): self
                    {
                        return $this;
                    }
                    
                    public function max(int $value): self
                    {
                        return $this;
                    }
                    
                    public function default($value): self
                    {
                        return $this;
                    }
                };
            }
            
            public function boolean(string $name): object
            {
                return new class($name) {
                    private $name;
                    
                    public function __construct(string $name)
                    {
                        $this->name = $name;
                    }
                    
                    public function description(string $desc): self
                    {
                        return $this;
                    }
                    
                    public function default($value): self
                    {
                        return $this;
                    }
                };
            }
        };
        
        $property = $mockTraitUser->percentage($propertyName, $propertyLabel);
        $attributes = $property->toArray();
        
        // Check if builder added the required properties
        $this->assertArrayHasKey('properties', $attributes);
        if (isset($attributes['properties'])) {
            // Check main properties
            $expectedProps = ['value', 'showSymbol', 'decimals'];
            foreach ($expectedProps as $prop) {
                $this->assertArrayHasKey($prop, $attributes['properties']);
            }
        }
    }
}
