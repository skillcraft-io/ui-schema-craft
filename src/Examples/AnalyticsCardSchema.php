<?php

namespace Skillcraft\UiSchemaCraft\Examples;

use Skillcraft\UiSchemaCraft\Abstracts\UIComponentSchema;
use Skillcraft\UiSchemaCraft\Schema\PropertyBuilder;

class AnalyticsCardSchema extends UIComponentSchema
{
    protected string $type = 'analytics-card';
    protected string $component = 'card-component';

    public function getExampleData(): array
    {
        return [
            'title' => 'Monthly Revenue',
            'metric' => 125000.50,
            'change' => 12.5,
            'timeRange' => '30d',
            'visualization' => 'line',
            'showComparison' => true,
            'currency' => 'USD',
            'dataPoints' => [
                ['date' => '2024-12-01', 'value' => 100000],
                ['date' => '2024-12-15', 'value' => 110000],
                ['date' => '2024-12-31', 'value' => 125000]
            ]
        ];
    }

    public function properties(): array
    {
        $builder = new PropertyBuilder();
        
        $builder->string('title', 'Card Title')
            ->default('')
            ->rules(['required', 'max:100']);
            
        $builder->number('metric', 'Current Value')
            ->default(0)
            ->rules(['required', 'numeric', 'min:0']);
            
        $builder->number('change', 'Percentage Change')
            ->default(0)
            ->rules(['required', 'numeric']);
            
        $builder->string('timeRange', 'Time Range')
            ->default('30d')
            ->rules(['required', 'in:7d,30d,90d,1y']);
            
        $builder->string('visualization', 'Chart Type')
            ->default('line')
            ->rules(['required', 'in:line,bar,area']);
            
        $builder->boolean('showComparison', 'Show Period Comparison')
            ->default(true)
            ->rules(['required']);
            
        $builder->string('currency', 'Currency Code')
            ->default('USD')
            ->rules(['required', 'size:3']);
            
        $builder->array('dataPoints', 'Historical Data Points')
            ->default([])
            ->rules(['required', 'array', 'min:1']);
            
        return $builder->toArray();
    }
}
