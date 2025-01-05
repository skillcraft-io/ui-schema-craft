<?php

namespace Skillcraft\UiSchemaCraft\Examples;

use Skillcraft\UiSchemaCraft\Abstracts\UIComponentSchema;
use Skillcraft\UiSchemaCraft\Schema\PropertyBuilder;

class SettingsPanelSchema extends UIComponentSchema
{
    protected string $type = 'settings-panel';
    protected string $component = 'panel-component';

    public function getExampleData(): array
    {
        return [
            'notifications' => true,
            'theme' => 'dark',
            'language' => 'en',
            'autoSave' => true
        ];
    }

    public function properties(): array
    {
        $builder = new PropertyBuilder();
        
        $builder->boolean('notifications', 'Enable Notifications')
            ->default(true)
            ->rules(['required']);
            
        $builder->string('theme', 'Theme')
            ->default('system')
            ->rules(['required', 'in:light,dark,system']);
            
        $builder->string('language', 'Language')
            ->default('en')
            ->rules(['required', 'in:en,es,fr,de']);
            
        $builder->boolean('autoSave', 'Enable Auto-Save')
            ->default(true)
            ->rules(['required']);
            
        return $builder->toArray();
    }
}
