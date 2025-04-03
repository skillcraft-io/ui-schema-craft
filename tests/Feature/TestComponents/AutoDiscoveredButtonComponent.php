<?php

namespace Skillcraft\UiSchemaCraft\Tests\Feature\TestComponents;

use Skillcraft\UiSchemaCraft\Abstracts\UIComponentSchema;
use Skillcraft\UiSchemaCraft\Facades\PropertyBuilder;

class AutoDiscoveredButtonComponent extends UIComponentSchema
{
    protected string $type = 'auto-discovered-button-component';
    protected string $component = 'button-component';
    protected string $title = 'Auto-discovered Button';
    protected string $description = 'A button that is auto-discovered from the default namespace';
    
    public function properties(): array
    {
        $builder = PropertyBuilder::new();
        
        $builder->string('title', 'Component Title')
            ->default('Auto-discovered Button')
            ->rules(['required'])
            ->example('Auto Button');
            
        $builder->string('description', 'Component Description')
            ->default('A button that is auto-discovered from the default namespace')
            ->rules(['nullable'])
            ->example('Automatically registered component');
            
        $builder->string('text', 'Button Text')
            ->default('Discover')
            ->rules(['required'])
            ->example('Search');
            
        $builder->string('variant', 'Button Style Variant')
            ->default('secondary')
            ->rules(['required', 'in:primary,secondary,danger,success'])
            ->example('secondary');
        
        return $builder->toArray();
    }
}
