<?php

namespace Skillcraft\UiSchemaCraft\Tests\Unit\Schema\Traits;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversClass;
use Skillcraft\UiSchemaCraft\Tests\TestCase;
use Skillcraft\UiSchemaCraft\Schema\PropertyBuilder;
use Skillcraft\UiSchemaCraft\Schema\Traits\AuthenticationTrait;

#[CoversClass(AuthenticationTrait::class)]
class AuthenticationTraitTest extends TestCase
{
    protected PropertyBuilder $builder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->builder = new PropertyBuilder();
    }

    #[Test]
    public function it_creates_mfa_property()
    {
        $property = $this->builder->mfa('two_factor', 'Two Factor Authentication');
        $schema = $property->toArray();

        $this->assertEquals('two_factor', $schema['name']);
        $this->assertEquals('Two Factor Authentication', $schema['description']);
        $this->assertEquals('object', $schema['type']);
        
        // Check methods array
        $this->assertArrayHasKey('methods', $schema['properties']);
        $this->assertEquals('array', $schema['properties']['methods']['type']);
        $this->assertEquals('string', $schema['properties']['methods']['items']['type']);
        
        // Check TOTP configuration
        $this->assertArrayHasKey('totp', $schema['properties']);
        $this->assertEquals('object', $schema['properties']['totp']['type']);
        $this->assertArrayHasKey('enabled', $schema['properties']['totp']['properties']);
        $this->assertArrayHasKey('secret', $schema['properties']['totp']['properties']);
        $this->assertArrayHasKey('qrCode', $schema['properties']['totp']['properties']);
    }

    #[Test]
    public function it_creates_otp_property()
    {
        $property = $this->builder->otp('verification_code', 'Verification Code');
        $schema = $property->toArray();

        $this->assertEquals('verification_code', $schema['name']);
        $this->assertEquals('Verification Code', $schema['description']);
        $this->assertEquals('string', $schema['type']);
        $this->assertEquals('otp', $schema['format']);
    }

    #[Test]
    public function it_creates_captcha_property()
    {
        $property = $this->builder->captcha('security_check', 'Security Check');
        $schema = $property->toArray();

        $this->assertEquals('security_check', $schema['name']);
        $this->assertEquals('Security Check', $schema['description']);
        $this->assertEquals('object', $schema['type']);
        
        $this->assertArrayHasKey('token', $schema['properties']);
        $this->assertEquals('string', $schema['properties']['token']['type']);
        
        $this->assertArrayHasKey('type', $schema['properties']);
        $this->assertEquals('string', $schema['properties']['type']['type']);
    }

    #[Test]
    public function it_creates_role_transfer_property()
    {
        $property = $this->builder->roleTransfer('role_transfer', 'Role Transfer');
        $schema = $property->toArray();

        $this->assertEquals('role_transfer', $schema['name']);
        $this->assertEquals('Role Transfer', $schema['description']);
        $this->assertEquals('object', $schema['type']);
        
        // Check basic properties
        $this->assertArrayHasKey('selected', $schema['properties']);
        $this->assertArrayHasKey('source', $schema['properties']);
        $this->assertArrayHasKey('target', $schema['properties']);
        $this->assertArrayHasKey('titles', $schema['properties']);
        $this->assertArrayHasKey('operations', $schema['properties']);
        
        // Check array types
        $this->assertEquals('array', $schema['properties']['selected']['type']);
        $this->assertEquals('array', $schema['properties']['source']['type']);
        $this->assertEquals('array', $schema['properties']['target']['type']);
        $this->assertEquals('array', $schema['properties']['titles']['type']);
        $this->assertEquals('array', $schema['properties']['operations']['type']);
        
        // Check configuration options
        $this->assertArrayHasKey('searchable', $schema['properties']);
        $this->assertArrayHasKey('sortable', $schema['properties']);
        $this->assertEquals('boolean', $schema['properties']['searchable']['type']);
        $this->assertEquals('boolean', $schema['properties']['sortable']['type']);
        $this->assertTrue($schema['properties']['searchable']['default']);
        $this->assertTrue($schema['properties']['sortable']['default']);
    }
}
