<?php

namespace Skillcraft\UiSchemaCraft\Examples;

use Skillcraft\UiSchemaCraft\Abstracts\UIComponentSchema;
use Skillcraft\UiSchemaCraft\Facades\PropertyBuilder;

class UserProfileSchema extends UIComponentSchema
{
    protected string $component = 'UserProfile';

    protected function properties(): array
    {
        return [
            // Basic Information
            PropertyBuilder::object('basic_info')
                ->description('Basic user information')
                ->properties([
                    PropertyBuilder::string('first_name')
                        ->description('First name')
                        ->required(),

                    PropertyBuilder::string('last_name')
                        ->description('Last name')
                        ->required(),

                    PropertyBuilder::string('email')
                        ->description('Email address')
                        ->required(),

                    PropertyBuilder::string('phone')
                        ->description('Phone number')
                        ->nullable(),
                ])
                ->required(),

            // Profile Picture
            PropertyBuilder::object('avatar')
                ->description('Profile picture settings')
                ->properties([
                    PropertyBuilder::imageUpload('image')
                        ->description('Profile image')
                        ->nullable(),

                    PropertyBuilder::boolean('use_gravatar')
                        ->description('Use Gravatar')
                        ->default(true),
                ])
                ->required(),

            // Security
            PropertyBuilder::object('security')
                ->description('Security settings')
                ->properties([
                    PropertyBuilder::boolean('two_factor')
                        ->description('Enable two-factor authentication')
                        ->default(false),

                    PropertyBuilder::mfa('mfa_settings')
                        ->description('MFA configuration')
                        ->nullable(),

                    PropertyBuilder::array('trusted_devices')
                        ->description('List of trusted devices')
                        ->nullable(),
                ])
                ->required(),

            // Preferences
            PropertyBuilder::object('preferences')
                ->description('User preferences')
                ->properties([
                    PropertyBuilder::string('language')
                        ->description('Interface language')
                        ->enum(['en', 'es', 'fr', 'de'])
                        ->default('en'),

                    PropertyBuilder::string('timezone')
                        ->description('User timezone')
                        ->required(),

                    PropertyBuilder::object('notifications')
                        ->description('Notification preferences')
                        ->properties([
                            PropertyBuilder::boolean('email_notifications')->default(true),
                            PropertyBuilder::boolean('push_notifications')->default(true),
                            PropertyBuilder::boolean('sms_notifications')->default(false),
                        ])
                        ->required(),
                ])
                ->required(),

            // Address
            PropertyBuilder::object('address')
                ->description('User address')
                ->properties([
                    PropertyBuilder::string('street')
                        ->description('Street address')
                        ->required(),

                    PropertyBuilder::string('city')
                        ->description('City')
                        ->required(),

                    PropertyBuilder::string('state')
                        ->description('State/Province')
                        ->required(),

                    PropertyBuilder::string('postal_code')
                        ->description('Postal code')
                        ->required(),

                    PropertyBuilder::string('country')
                        ->description('Country')
                        ->required(),
                ])
                ->nullable(),

            // Social Media
            PropertyBuilder::object('social_media')
                ->description('Social media links')
                ->properties([
                    PropertyBuilder::string('twitter')->nullable(),
                    PropertyBuilder::string('linkedin')->nullable(),
                    PropertyBuilder::string('github')->nullable(),
                    PropertyBuilder::string('facebook')->nullable(),
                ])
                ->nullable(),

            // Privacy
            PropertyBuilder::object('privacy')
                ->description('Privacy settings')
                ->properties([
                    PropertyBuilder::boolean('profile_public')
                        ->description('Make profile public')
                        ->default(false),

                    PropertyBuilder::array('visible_fields')
                        ->description('Publicly visible fields')
                        ->nullable(),

                    PropertyBuilder::boolean('show_email')
                        ->description('Show email to others')
                        ->default(false),
                ])
                ->required(),
        ];
    }

    public function getExampleData(): array
    {
        return [
            'basic_info' => [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john.doe@example.com',
                'phone' => '+1234567890',
            ],
            'avatar' => [
                'image' => null,
                'use_gravatar' => true,
            ],
            'security' => [
                'two_factor' => false,
                'mfa_settings' => null,
                'trusted_devices' => null,
            ],
            'preferences' => [
                'language' => 'en',
                'timezone' => 'America/New_York',
                'notifications' => [
                    'email_notifications' => true,
                    'push_notifications' => true,
                    'sms_notifications' => false,
                ],
            ],
            'address' => null,
            'social_media' => [
                'twitter' => '@johndoe',
                'linkedin' => 'johndoe',
                'github' => 'johndoe',
                'facebook' => null,
            ],
            'privacy' => [
                'profile_public' => false,
                'visible_fields' => null,
                'show_email' => false,
            ],
        ];
    }

    public function getLiveData(): array
    {
        return [];
    }
}
