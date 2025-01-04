<?php

namespace Skillcraft\UiSchemaCraft\Tests\Unit\Schema\Traits;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversClass;
use Skillcraft\UiSchemaCraft\Tests\TestCase;
use Skillcraft\UiSchemaCraft\Schema\PropertyBuilder;
use Skillcraft\UiSchemaCraft\Schema\Traits\UtilityTrait;

#[CoversClass(UtilityTrait::class)]
class UtilityTraitTest extends TestCase
{
    protected PropertyBuilder $builder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->builder = new PropertyBuilder();
    }

    #[Test]
    public function it_creates_group_property()
    {
        $property = $this->builder->group('address', function ($builder) {
            $builder->string('street')->description('Street address');
            $builder->string('city')->description('City name');
            $builder->string('state')->description('State/Province');
            $builder->string('zip')->description('ZIP/Postal code');
        }, 'Address Information');

        $schema = $property->toArray();

        $this->assertEquals('address', $schema['name']);
        $this->assertEquals('Address Information', $schema['description']);
        $this->assertEquals('object', $schema['type']);
        
        // Check properties
        $properties = $schema['properties'];
        
        // Check street
        $this->assertArrayHasKey('street', $properties);
        $street = $properties['street'];
        $this->assertEquals('string', $street['type']);
        $this->assertEquals('Street address', $street['description']);
        
        // Check city
        $this->assertArrayHasKey('city', $properties);
        $city = $properties['city'];
        $this->assertEquals('string', $city['type']);
        $this->assertEquals('City name', $city['description']);
        
        // Check state
        $this->assertArrayHasKey('state', $properties);
        $state = $properties['state'];
        $this->assertEquals('string', $state['type']);
        $this->assertEquals('State/Province', $state['description']);
        
        // Check zip
        $this->assertArrayHasKey('zip', $properties);
        $zip = $properties['zip'];
        $this->assertEquals('string', $zip['type']);
        $this->assertEquals('ZIP/Postal code', $zip['description']);
    }

    #[Test]
    public function it_creates_group_with_default_label()
    {
        $property = $this->builder->group('shipping_details', function ($builder) {
            $builder->string('carrier');
            $builder->string('tracking_number');
        });

        $schema = $property->toArray();

        $this->assertEquals('shipping_details', $schema['name']);
        $this->assertEquals('Shipping Details', $schema['description']);
        $this->assertEquals('object', $schema['type']);
    }

    #[Test]
    public function it_creates_list_property()
    {
        $property = $this->builder->list('phone_numbers', function ($builder) {
            $builder->string('type')->description('Phone type');
            $builder->string('number')->description('Phone number');
            $builder->boolean('primary')->description('Is primary');
        }, 'Contact Numbers');

        $schema = $property->toArray();

        $this->assertEquals('phone_numbers', $schema['name']);
        $this->assertEquals('Contact Numbers', $schema['description']);
        $this->assertEquals('array', $schema['type']);
        
        // Check items
        $this->assertArrayHasKey('items', $schema);
        $items = $schema['items'];
        $this->assertEquals('object', $items['type']);
        
        // Check item properties
        $properties = $items['properties'];
        
        // Check type
        $this->assertArrayHasKey('type', $properties);
        $type = $properties['type'];
        $this->assertEquals('string', $type['type']);
        $this->assertEquals('Phone type', $type['description']);
        
        // Check number
        $this->assertArrayHasKey('number', $properties);
        $number = $properties['number'];
        $this->assertEquals('string', $number['type']);
        $this->assertEquals('Phone number', $number['description']);
        
        // Check primary
        $this->assertArrayHasKey('primary', $properties);
        $primary = $properties['primary'];
        $this->assertEquals('boolean', $primary['type']);
        $this->assertEquals('Is primary', $primary['description']);
    }

    #[Test]
    public function it_creates_list_with_default_label()
    {
        $property = $this->builder->list('email_addresses', function ($builder) {
            $builder->string('email');
            $builder->boolean('verified');
        });

        $schema = $property->toArray();

        $this->assertEquals('email_addresses', $schema['name']);
        $this->assertEquals('Email Addresses', $schema['description']);
        $this->assertEquals('array', $schema['type']);
    }

    #[Test]
    public function it_creates_conditional_property()
    {
        $conditions = [
            'if' => ['type' => ['value' => 'business']],
            'then' => ['required' => ['company', 'vat_number']]
        ];

        $property = $this->builder->conditional('business_info', $conditions, function ($builder) {
            $builder->string('company')->description('Company name');
            $builder->string('vat_number')->description('VAT number');
        }, 'Business Information');

        $schema = $property->toArray();

        $this->assertEquals('business_info', $schema['name']);
        $this->assertEquals('Business Information', $schema['description']);
        $this->assertEquals('object', $schema['type']);
        
        // Check conditions
        $this->assertArrayHasKey('conditions', $schema);
        $this->assertEquals($conditions, $schema['conditions']);
        
        // Check properties
        $properties = $schema['properties'];
        
        // Check company
        $this->assertArrayHasKey('company', $properties);
        $company = $properties['company'];
        $this->assertEquals('string', $company['type']);
        $this->assertEquals('Company name', $company['description']);
        
        // Check vat number
        $this->assertArrayHasKey('vat_number', $properties);
        $vat = $properties['vat_number'];
        $this->assertEquals('string', $vat['type']);
        $this->assertEquals('VAT number', $vat['description']);
    }

    #[Test]
    public function it_creates_conditional_with_default_label()
    {
        $conditions = [
            'if' => ['status' => ['value' => 'active']],
            'then' => ['required' => ['expiry_date']]
        ];

        $property = $this->builder->conditional('subscription_details', $conditions, function ($builder) {
            $builder->string('expiry_date');
        });

        $schema = $property->toArray();

        $this->assertEquals('subscription_details', $schema['name']);
        $this->assertEquals('Subscription Details', $schema['description']);
        $this->assertEquals('object', $schema['type']);
    }
}
