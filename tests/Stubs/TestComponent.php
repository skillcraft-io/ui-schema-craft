<?php

namespace Skillcraft\UiSchemaCraft\Tests\Stubs;

use Skillcraft\UiSchemaCraft\Abstracts\UIComponentSchema;
use Skillcraft\UiSchemaCraft\Schema\PropertyBuilder;

class TestComponent extends UIComponentSchema
{
    protected string $type = 'test-component';
    protected string $component = 'TestComponent';

    public function properties(): array
    {
        $builder = new PropertyBuilder();

        return [
            $builder->string('label')
                ->required()
                ->description('Label text')
                ->setDefault(''),

            $builder->string('name')
                ->required()
                ->description('Field name')
                ->setDefault(''),

            $builder->boolean('required')
                ->description('Whether the field is required')
                ->setDefault(false),
        ];
    }

    protected function getExampleData(): array
    {
        return [
            'label' => 'Example Label',
            'name' => 'example_name',
            'required' => false,
        ];
    }
}
