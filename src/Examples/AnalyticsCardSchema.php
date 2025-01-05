<?php

namespace Skillcraft\UiSchemaCraft\Examples;

use Skillcraft\UiSchemaCraft\Abstracts\UIComponentSchema;
use Skillcraft\UiSchemaCraft\Schema\PropertyBuilder;

class AnalyticsCardSchema extends UIComponentSchema
{
    protected string $type = 'analytics-card';
    protected string $component = 'card-component';

    public function properties(): array
    {
        $builder = new PropertyBuilder();
        
        $builder->string('title', 'Card Title')
            ->default('')
            ->rules(['required', 'max:100'])
            ->example('Monthly Revenue');
            
        $builder->number('metric', 'Current Value')
            ->default(0)
            ->rules(['required', 'numeric', 'min:0'])
            ->example(125000.50);
            
        $builder->number('change', 'Percentage Change')
            ->default(0)
            ->rules(['required', 'numeric'])
            ->example(12.5);
            
        $builder->string('timeRange', 'Time Range')
            ->default('30d')
            ->rules(['required', 'in:7d,30d,90d,1y'])
            ->example('30d');
            
        $builder->string('visualization', 'Chart Type')
            ->default('line')
            ->rules(['required', 'in:line,bar,area'])
            ->example('line');
            
        $builder->boolean('showComparison', 'Show Period Comparison')
            ->default(true)
            ->rules(['required'])
            ->example(true);
            
        $builder->string('currency', 'Currency Code')
            ->default('USD')
            ->rules(['required', 'size:3'])
            ->example('USD');
            
        $builder->array('dataPoints', 'Historical Data Points')
            ->default([])
            ->rules(['required', 'array', 'min:1'])
            ->items([
                $builder->string('date')
                    ->rules(['required', 'date'])
                    ->example('2024-12-01'),
                $builder->number('value')
                    ->rules(['required', 'numeric', 'min:0'])
                    ->example(100000)
            ])
            ->example([
                ['date' => '2024-12-01', 'value' => 100000],
                ['date' => '2024-12-15', 'value' => 110000],
                ['date' => '2024-12-31', 'value' => 125000]
            ]);
            
        return $builder->toArray();
    }
}
