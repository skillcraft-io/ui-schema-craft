<?php

namespace Skillcraft\UiSchemaCraft\Tests\Unit\Schema\Traits;

use Skillcraft\UiSchemaCraft\Tests\TestCase;
use Skillcraft\UiSchemaCraft\Schema\Property;
use Skillcraft\UiSchemaCraft\Schema\Traits\AuthenticationTrait;

class AuthenticationTraitTest extends TestCase
{
    /**
     * Test class that uses the AuthenticationTrait
     */
    private $traitUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test class that uses the trait
        $this->traitUser = new class {
            use AuthenticationTrait;
            
            public array $properties = [];
        };
    }

    public function testMfaProperty(): void
    {
        $propertyName = 'testMfa';
        $propertyDescription = 'Test MFA';
        
        $property = $this->traitUser->mfa($propertyName, $propertyDescription);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('object', $property->getType());
        $this->assertEquals($propertyDescription, $property->getDescription());
        
        // Check that the expected attributes are set
        $attributes = $property->toArray();
        $this->assertArrayHasKey('properties', $attributes);
        
        // Verify specific MFA structure
        $this->assertArrayHasKey('methods', $attributes['properties']);
        $this->assertArrayHasKey('totp', $attributes['properties']);
        
        // Verify methods structure
        $this->assertEquals('array', $attributes['properties']['methods']['type']);
        $this->assertArrayHasKey('items', $attributes['properties']['methods']);
        
        // Verify totp structure
        $this->assertEquals('object', $attributes['properties']['totp']['type']);
        $this->assertArrayHasKey('properties', $attributes['properties']['totp']);
        $this->assertArrayHasKey('enabled', $attributes['properties']['totp']['properties']);
        $this->assertArrayHasKey('secret', $attributes['properties']['totp']['properties']);
        $this->assertArrayHasKey('qrCode', $attributes['properties']['totp']['properties']);
    }

    public function testOtpProperty(): void
    {
        $propertyName = 'testOtp';
        $propertyDescription = 'Test OTP';
        
        $property = $this->traitUser->otp($propertyName, $propertyDescription);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('string', $property->getType());
        $this->assertEquals($propertyDescription, $property->getDescription());
        
        // Check that format is set to otp
        $attributes = $property->toArray();
        $this->assertArrayHasKey('format', $attributes);
        $this->assertEquals('otp', $attributes['format']);
    }

    public function testCaptchaProperty(): void
    {
        $propertyName = 'testCaptcha';
        $propertyDescription = 'Test Captcha';
        
        $property = $this->traitUser->captcha($propertyName, $propertyDescription);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('object', $property->getType());
        $this->assertEquals($propertyDescription, $property->getDescription());
        
        // Check captcha structure
        $attributes = $property->toArray();
        $this->assertArrayHasKey('properties', $attributes);
        $this->assertArrayHasKey('token', $attributes['properties']);
        $this->assertArrayHasKey('type', $attributes['properties']);
        
        // Verify property types
        $this->assertEquals('string', $attributes['properties']['token']['type']);
        $this->assertEquals('string', $attributes['properties']['type']['type']);
    }

    public function testRoleTransferProperty(): void
    {
        $propertyName = 'testRoleTransfer';
        $propertyDescription = 'Test Role Transfer';
        
        $property = $this->traitUser->roleTransfer($propertyName, $propertyDescription);
        
        $this->assertInstanceOf(Property::class, $property);
        $this->assertEquals($propertyName, $property->getName());
        $this->assertEquals('object', $property->getType());
        $this->assertEquals($propertyDescription, $property->getDescription());
        
        // Check role transfer structure
        $attributes = $property->toArray();
        $this->assertArrayHasKey('properties', $attributes);
        
        // Verify all expected properties exist
        $expectedProperties = ['selected', 'source', 'target', 'titles', 'operations', 'searchable', 'sortable'];
        foreach ($expectedProperties as $expectedProperty) {
            $this->assertArrayHasKey($expectedProperty, $attributes['properties']);
        }
        
        // Verify boolean properties have default values
        $this->assertEquals('boolean', $attributes['properties']['searchable']['type']);
        $this->assertEquals(true, $attributes['properties']['searchable']['default']);
        $this->assertEquals('boolean', $attributes['properties']['sortable']['type']);
        $this->assertEquals(true, $attributes['properties']['sortable']['default']);
    }
}
