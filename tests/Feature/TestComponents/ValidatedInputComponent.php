<?php

namespace Skillcraft\UiSchemaCraft\Tests\Feature\TestComponents;

use Skillcraft\UiSchemaCraft\Abstracts\UIComponentSchema;
use Skillcraft\UiSchemaCraft\Facades\PropertyBuilder;


class ValidatedInputComponent extends UIComponentSchema
{
    protected string $type = 'validated-input-component';
    protected string $component = 'input-component';
    protected string $title = 'Validated Input';
    protected string $description = 'An input with validation rules';
    
    public function properties(): array
    {
        $builder = PropertyBuilder::new();
        
        $builder->string('title', 'Component Title')
            ->default('Validated Input')
            ->rules(['required'])
            ->example('Email Input');
            
        $builder->string('description', 'Component Description')
            ->default('An input with validation rules')
            ->rules(['nullable'])
            ->example('Please enter a valid email address');
            
        $builder->string('type', 'Input Type')
            ->default('email')
            ->rules(['required', 'in:email,text,tel'])
            ->example('email');
            
        $builder->string('label', 'Field Label')
            ->default('Email Address')
            ->rules(['required'])
            ->example('Your Email');
            
        $builder->string('placeholder', 'Placeholder Text')
            ->default('Enter your email address')
            ->rules(['nullable'])
            ->example('name@example.com');
            
        $builder->string('value', 'Current Value')
            ->default('')
            ->rules(['nullable'])
            ->example('test@example.com');
        
        return $builder->toArray();
    }
    
    protected function getValidationSchema(): ?array
    {
        return [
            'value' => ['required', 'email']
        ];
    }
}
