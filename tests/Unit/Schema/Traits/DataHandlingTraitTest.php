<?php

namespace Skillcraft\UiSchemaCraft\Tests\Unit\Schema\Traits;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversClass;
use Skillcraft\UiSchemaCraft\Tests\TestCase;
use Skillcraft\UiSchemaCraft\Schema\PropertyBuilder;
use Skillcraft\UiSchemaCraft\Schema\Traits\DataHandlingTrait;

#[CoversClass(DataHandlingTrait::class)]
class DataHandlingTraitTest extends TestCase
{
    protected PropertyBuilder $builder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->builder = new PropertyBuilder();
    }

    #[Test]
    public function it_creates_id_property()
    {
        $property = $this->builder->id();
        $schema = $property->toArray();

        $this->assertEquals('id', $schema['name']);
        $this->assertEquals('string', $schema['type']);
        $this->assertEquals('Unique identifier', $schema['description']);
        $this->assertTrue($schema['required']);
    }

    #[Test]
    public function it_creates_custom_id_property()
    {
        $property = $this->builder->id('user_id');
        $schema = $property->toArray();

        $this->assertEquals('user_id', $schema['name']);
        $this->assertEquals('string', $schema['type']);
        $this->assertEquals('Unique identifier', $schema['description']);
        $this->assertTrue($schema['required']);
    }

    #[Test]
    public function it_creates_foreign_key_property()
    {
        $property = $this->builder->foreignKey('user_id', 'users');
        $schema = $property->toArray();

        $this->assertEquals('user_id', $schema['name']);
        $this->assertEquals('string', $schema['type']);
        $this->assertEquals('Foreign key reference to users', $schema['description']);
        $this->assertTrue($schema['required']);
    }

    #[Test]
    public function it_creates_timestamp_property()
    {
        $property = $this->builder->timestamp('created_at');
        $schema = $property->toArray();

        $this->assertEquals('created_at', $schema['name']);
        $this->assertEquals('string', $schema['type']);
        $this->assertEquals('date-time', $schema['format']);
        $this->assertEquals('Timestamp field', $schema['description']);
    }

    #[Test]
    public function it_creates_slug_property()
    {
        $property = $this->builder->slug();
        $schema = $property->toArray();

        $this->assertEquals('slug', $schema['name']);
        $this->assertEquals('string', $schema['type']);
        $this->assertEquals('^[a-z0-9]+(?:-[a-z0-9]+)*$', $schema['pattern']);
        $this->assertEquals('URL-friendly slug', $schema['description']);
    }

    #[Test]
    public function it_creates_custom_slug_property()
    {
        $property = $this->builder->slug('post_slug');
        $schema = $property->toArray();

        $this->assertEquals('post_slug', $schema['name']);
        $this->assertEquals('string', $schema['type']);
        $this->assertEquals('^[a-z0-9]+(?:-[a-z0-9]+)*$', $schema['pattern']);
        $this->assertEquals('URL-friendly slug', $schema['description']);
    }

    #[Test]
    public function it_creates_json_property()
    {
        $property = $this->builder->json('metadata');
        $schema = $property->toArray();

        $this->assertEquals('metadata', $schema['name']);
        $this->assertEquals('object', $schema['type']);
        $this->assertEquals('json', $schema['format']);
        $this->assertEquals('JSON data field', $schema['description']);
    }

    #[Test]
    public function it_creates_table_property()
    {
        $property = $this->builder->table('users_table', 'Users Table');
        $schema = $property->toArray();

        $this->assertEquals('users_table', $schema['name']);
        $this->assertEquals('Users Table', $schema['description']);
        $this->assertEquals('object', $schema['type']);
        
        // Check table components
        $this->assertArrayHasKey('columns', $schema['properties']);
        $this->assertArrayHasKey('data', $schema['properties']);
        $this->assertArrayHasKey('pagination', $schema['properties']);
        
        // Check property types
        $this->assertEquals('array', $schema['properties']['columns']['type']);
        $this->assertEquals('array', $schema['properties']['data']['type']);
        $this->assertEquals('object', $schema['properties']['pagination']['type']);
    }

    #[Test]
    public function it_creates_dynamic_form_property()
    {
        $property = $this->builder->dynamicForm('custom_form', 'Custom Form');
        $schema = $property->toArray();

        $this->assertEquals('custom_form', $schema['name']);
        $this->assertEquals('Custom Form', $schema['description']);
        $this->assertEquals('object', $schema['type']);
        
        // Check form components
        $this->assertArrayHasKey('fields', $schema['properties']);
        $this->assertArrayHasKey('data', $schema['properties']);
        
        // Check property types
        $this->assertEquals('array', $schema['properties']['fields']['type']);
        $this->assertEquals('object', $schema['properties']['data']['type']);
    }

    #[Test]
    public function it_creates_matrix_property()
    {
        $property = $this->builder->matrix('data_matrix', 'Data Matrix');
        $schema = $property->toArray();

        $this->assertEquals('data_matrix', $schema['name']);
        $this->assertEquals('Data Matrix', $schema['description']);
        $this->assertEquals('object', $schema['type']);
        
        // Check matrix components
        $this->assertArrayHasKey('rows', $schema['properties']);
        $this->assertArrayHasKey('columns', $schema['properties']);
        $this->assertArrayHasKey('cells', $schema['properties']);
        
        // Check property types
        $this->assertEquals('array', $schema['properties']['rows']['type']);
        $this->assertEquals('array', $schema['properties']['columns']['type']);
        $this->assertEquals('array', $schema['properties']['cells']['type']);
    }

    #[Test]
    public function it_creates_transfer_property()
    {
        $property = $this->builder->transfer('payment', 'Payment Transfer');
        $schema = $property->toArray();

        $this->assertEquals('payment', $schema['name']);
        $this->assertEquals('Payment Transfer', $schema['description']);
        $this->assertEquals('object', $schema['type']);
        
        // Check transfer components
        $this->assertArrayHasKey('source', $schema['properties']);
        $this->assertArrayHasKey('destination', $schema['properties']);
        $this->assertArrayHasKey('amount', $schema['properties']);
        $this->assertArrayHasKey('currency', $schema['properties']);
        $this->assertArrayHasKey('status', $schema['properties']);
        $this->assertArrayHasKey('timestamp', $schema['properties']);
        
        // Check property types
        $this->assertEquals('string', $schema['properties']['source']['type']);
        $this->assertEquals('string', $schema['properties']['destination']['type']);
        $this->assertEquals('number', $schema['properties']['amount']['type']);
        $this->assertEquals('string', $schema['properties']['currency']['type']);
        $this->assertEquals('string', $schema['properties']['status']['type']);
        $this->assertEquals('string', $schema['properties']['timestamp']['type']);
        
        // Check specific validations
        $this->assertEquals(['pending', 'completed', 'failed'], $schema['properties']['status']['enum']);
        $this->assertEquals('date-time', $schema['properties']['timestamp']['format']);
    }
}
