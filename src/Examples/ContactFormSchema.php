<?php

namespace Skillcraft\UiSchemaCraft\Examples;

use Skillcraft\UiSchemaCraft\Abstracts\UIComponentSchema;
use Skillcraft\UiSchemaCraft\Schema\PropertyBuilder;

class ContactFormSchema extends UIComponentSchema
{
    protected string $type = 'contact-form';
    protected string $component = 'form-component';

    public function getExampleData(): array
    {
        return [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'General Inquiry',
            'message' => 'Hello, I would like to know more about your services.'
        ];
    }

    public function properties(): array
    {
        $builder = new PropertyBuilder();
        
        $builder->string('name', 'Full Name')
            ->default('')
            ->rules(['required', 'max:100']);
            
        $builder->string('email', 'Email Address')
            ->default('')
            ->rules(['required', 'email']);
            
        $builder->string('subject', 'Subject')
            ->default('General Inquiry')
            ->rules(['required', 'max:200']);
            
        $builder->string('message', 'Message')
            ->default('')
            ->rules(['required', 'max:1000']);
            
        return $builder->toArray();
    }

    public function getComponent(): string
    {
        return $this->component;
    }
}