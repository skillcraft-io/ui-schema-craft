<?php

namespace Skillcraft\UiSchemaCraft\Schema\Traits;

use Skillcraft\UiSchemaCraft\Schema\Property;

trait AuthenticationTrait
{
    /**
     * Create an MFA property.
     */
    public function mfa(string $name, ?string $description = null): Property
    {
        $property = new Property($name, 'object', $description);
        $property->addAttribute('properties', [
            'methods' => [
                'type' => 'array',
                'items' => ['type' => 'string']
            ],
            'totp' => [
                'type' => 'object',
                'properties' => [
                    'enabled' => ['type' => 'boolean'],
                    'secret' => ['type' => 'string'],
                    'qrCode' => ['type' => 'string']
                ]
            ]
        ]);
        return $property;
    }

    /**
     * Create an OTP property.
     */
    public function otp(string $name, ?string $description = null): Property
    {
        $property = new Property($name, 'string', $description);
        $property->addAttribute('format', 'otp');
        return $property;
    }

    /**
     * Create a captcha property.
     */
    public function captcha(string $name, ?string $description = null): Property
    {
        $property = new Property($name, 'object', $description);
        $property->addAttribute('properties', [
            'token' => ['type' => 'string'],
            'type' => ['type' => 'string']
        ]);
        return $property;
    }

    /**
     * Create a role transfer property.
     */
    public function roleTransfer(string $name, ?string $description = null): Property
    {
        $property = new Property($name, 'object', $description);
        $property->addAttribute('properties', [
            'selected' => ['type' => 'array'],
            'source' => ['type' => 'array'],
            'target' => ['type' => 'array'],
            'titles' => ['type' => 'array'],
            'operations' => ['type' => 'array'],
            'searchable' => ['type' => 'boolean', 'default' => true],
            'sortable' => ['type' => 'boolean', 'default' => true]
        ]);
        return $property;
    }
}
