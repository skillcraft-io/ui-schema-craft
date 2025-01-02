<?php

namespace Skillcraft\UiSchemaCraft\Components;

use Skillcraft\UiSchemaCraft\Abstracts\UIComponentSchema;
use Skillcraft\UiSchemaCraft\Schema\PropertyBuilder;

class FormField extends UIComponentSchema
{
    protected string $type = 'form-field';
    protected string $component = 'FormField';

    protected function properties(): array
    {
        return [
            PropertyBuilder::string('label')
                ->required()
                ->description('Field label text')
                ->default(''),

            PropertyBuilder::string('name')
                ->required()
                ->pattern('/^[a-zA-Z_][a-zA-Z0-9_]*$/')
                ->description('Field name (must be valid identifier)')
                ->default(''),

            PropertyBuilder::string('placeholder')
                ->description('Placeholder text')
                ->default(''),

            PropertyBuilder::string('helpText')
                ->description('Help text shown below the field')
                ->default(''),

            PropertyBuilder::boolean('required')
                ->description('Whether the field is required')
                ->default(false),

            PropertyBuilder::boolean('disabled')
                ->description('Whether the field is disabled')
                ->default(false),

            PropertyBuilder::array('validation')
                ->description('Validation rules for the field')
                ->default([]),

            PropertyBuilder::object('style')
                ->description('Custom styles for the field')
                ->default([]),
        ];
    }

    protected function getExampleData(): array
    {
        return [
            'label' => 'Username',
            'name' => 'username',
            'placeholder' => 'Enter your username',
            'helpText' => 'Choose a unique username',
            'required' => true,
            'disabled' => false,
            'validation' => [
                'minLength' => 3,
                'maxLength' => 20,
            ],
            'style' => [
                'width' => '100%',
                'marginBottom' => '1rem',
            ],
        ];
    }
}
