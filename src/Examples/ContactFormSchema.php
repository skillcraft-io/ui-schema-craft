<?php

namespace Skillcraft\UiSchemaCraft\Examples;

use Skillcraft\UiSchemaCraft\Abstracts\UIComponentSchema;
use Skillcraft\UiSchemaCraft\Schema\PropertyBuilder;

class ContactFormSchema extends UIComponentSchema
{
    protected string $type = 'contact-form';
    protected string $component = 'form-component';

    public function properties(): array
    {
        $builder = new PropertyBuilder();
        
        $builder->string('name', 'Full Name')
            ->default('')
            ->rules(['required', 'max:100'])
            ->example('John Doe');
            
        $builder->string('email', 'Email Address')
            ->default('')
            ->rules(['required', 'email'])
            ->example('john@example.com');
            
        $builder->string('subject', 'Subject')
            ->default('General Inquiry')
            ->rules(['required', 'max:200'])
            ->example('General Inquiry');
            
        $builder->string('message', 'Message')
            ->default('')
            ->rules(['required', 'max:1000'])
            ->example('Hello, I would like to know more about your services.');
            
        return $builder->toArray();
    }

    public function getComponent(): string
    {
        return $this->component;
    }
}