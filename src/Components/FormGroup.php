<?php

namespace Skillcraft\UiSchemaCraft\Components;

use Skillcraft\UiSchemaCraft\Abstracts\UIComponentSchema;
use Skillcraft\UiSchemaCraft\Schema\PropertyBuilder;

class FormGroup extends UIComponentSchema
{
    protected string $type = 'form-group';
    protected string $component = 'FormGroup';

    protected function properties(): array
    {
        return [
            PropertyBuilder::string('label')
                ->required()
                ->description('Group label text')
                ->default(''),

            PropertyBuilder::string('description')
                ->description('Group description text')
                ->default(''),

            PropertyBuilder::boolean('collapsible')
                ->description('Whether the group can be collapsed')
                ->default(false),

            PropertyBuilder::boolean('collapsed')
                ->description('Whether the group is initially collapsed')
                ->default(false),
        ];
    }

    protected function getExampleData(): array
    {
        $formField = new FormField();
        $this->addChild($formField);

        return [
            'label' => 'User Information',
            'description' => 'Please fill in your details',
            'collapsible' => true,
            'collapsed' => false,
            'children' => [
                'default' => [
                    $formField->getExampleData(),
                ],
            ],
        ];
    }
}
