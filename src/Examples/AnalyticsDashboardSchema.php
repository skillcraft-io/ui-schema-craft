<?php

namespace Skillcraft\UiSchemaCraft\Examples;

use Skillcraft\UiSchemaCraft\Abstracts\UIComponentSchema;
use Skillcraft\UiSchemaCraft\Facades\PropertyBuilder;

class AnalyticsDashboardSchema extends UIComponentSchema
{
    protected string $component = 'AnalyticsDashboard';

    protected function properties(): array
    {
        return [
            PropertyBuilder::timeRange('date_range')
                ->description('Analysis time period')
                ->required(),
                
            PropertyBuilder::duration('interval')
                ->description('Data aggregation interval')
                ->enum(['hour', 'day', 'week', 'month'])
                ->default('day'),

            PropertyBuilder::array('metrics')
                ->description('Selected metrics to display')
                ->required(),

            PropertyBuilder::object('filters')
                ->description('Data filters')
                ->properties([
                    PropertyBuilder::array('dimensions')
                        ->description('Dimension filters')
                        ->nullable(),

                    PropertyBuilder::array('conditions')
                        ->description('Filter conditions')
                        ->nullable(),
                ])
                ->nullable(),

            PropertyBuilder::object('visualization')
                ->description('Visualization settings')
                ->properties([
                    PropertyBuilder::string('chart_type')
                        ->description('Type of chart')
                        ->enum(['line', 'bar', 'pie'])
                        ->required(),

                    PropertyBuilder::object('chart_options')
                        ->description('Chart-specific options')
                        ->nullable(),

                    PropertyBuilder::boolean('show_legend')
                        ->description('Display chart legend')
                        ->default(true),

                    PropertyBuilder::boolean('show_grid')
                        ->description('Display chart grid')
                        ->default(true),
                ])
                ->required(),

            PropertyBuilder::object('export')
                ->description('Export settings')
                ->properties([
                    PropertyBuilder::array('formats')
                        ->description('Available export formats')
                        ->enum(['csv', 'excel', 'pdf'])
                        ->default(['csv'])
                        ->required(),

                    PropertyBuilder::boolean('include_metadata')
                        ->description('Include metadata in export')
                        ->default(false),
                ])
                ->nullable(),

            PropertyBuilder::object('kpi_widgets')
                ->description('KPI display widgets')
                ->properties([
                    PropertyBuilder::number('total_users')->required(),
                    PropertyBuilder::number('active_users')->required(),
                    PropertyBuilder::currency('revenue')->required(),
                    PropertyBuilder::percentage('conversion_rate')->required(),
                ])
                ->required(),

            PropertyBuilder::object('traffic_chart')
                ->description('Traffic overview chart')
                ->properties([
                    PropertyBuilder::string('type')->enum(['line', 'bar'])->default('line'),
                    PropertyBuilder::array('metrics')->required(),
                    PropertyBuilder::object('appearance')->nullable(),
                ])
                ->required(),

            PropertyBuilder::object('demographics')
                ->description('User demographics visualization')
                ->properties([
                    PropertyBuilder::string('type')->enum(['pie', 'donut'])->default('pie'),
                    PropertyBuilder::array('segments')->required(),
                    PropertyBuilder::object('colors')->nullable(),
                ])
                ->required(),

            PropertyBuilder::object('user_locations')
                ->description('User geographic distribution')
                ->properties([
                    PropertyBuilder::string('type')->enum(['heat', 'marker'])->default('heat'),
                    PropertyBuilder::array('data_points')->required(),
                    PropertyBuilder::object('style')->nullable(),
                ])
                ->required(),

            PropertyBuilder::object('top_content')
                ->description('Most viewed content')
                ->properties([
                    PropertyBuilder::array('columns')->required(),
                    PropertyBuilder::array('rows')->required(),
                    PropertyBuilder::object('sorting')->nullable(),
                    PropertyBuilder::object('pagination')->nullable(),
                ])
                ->required(),

            PropertyBuilder::object('custom_reports')
                ->description('Custom report builder')
                ->properties([
                    PropertyBuilder::array('metrics')->required(),
                    PropertyBuilder::array('dimensions')->required(),
                    PropertyBuilder::object('filters')->nullable(),
                ])
                ->nullable(),

            PropertyBuilder::object('export_settings')
                ->description('Report export configuration')
                ->properties([
                    PropertyBuilder::string('format')->enum(['csv', 'pdf', 'excel'])->required(),
                    PropertyBuilder::boolean('include_charts')->default(true),
                    PropertyBuilder::string('schedule')->nullable(),
                ])
                ->nullable(),

            PropertyBuilder::object('layout')
                ->description('Widget layout configuration')
                ->properties([
                    PropertyBuilder::string('widget_id')->required(),
                    PropertyBuilder::number('position')->required(),
                    PropertyBuilder::string('size')->enum(['small', 'medium', 'large'])->required(),
                ])
                ->required(),

            PropertyBuilder::object('alerts')
                ->description('Dashboard alerts configuration')
                ->properties([
                    PropertyBuilder::string('metric')->required(),
                    PropertyBuilder::string('condition')->required(),
                    PropertyBuilder::number('threshold')->required(),
                    PropertyBuilder::string('notification_type')->required(),
                ])
                ->nullable(),
        ];
    }

    public function getExampleData(): array
    {
        return [
            'date_range' => [
                'start' => '2024-01-01T00:00:00',
                'end' => '2024-12-31T23:59:59',
            ],
            'interval' => 'day',
            'kpi_widgets' => [
                'total_users' => 15000,
                'active_users' => 8500,
                'revenue' => 125000.50,
                'conversion_rate' => 3.5,
            ],
            'traffic_chart' => [
                'type' => 'line',
                'metrics' => ['pageviews', 'unique_visitors', 'bounce_rate'],
                'appearance' => [
                    'theme' => 'light',
                    'colors' => ['#4CAF50', '#2196F3', '#FFC107'],
                ],
            ],
            'demographics' => [
                'type' => 'pie',
                'segments' => [
                    ['label' => '18-24', 'value' => 25],
                    ['label' => '25-34', 'value' => 40],
                    ['label' => '35-44', 'value' => 20],
                    ['label' => '45+', 'value' => 15],
                ],
            ],
            'layout' => [
                [
                    'widget_id' => 'traffic_chart',
                    'position' => 1,
                    'size' => 'large',
                ],
                [
                    'widget_id' => 'demographics',
                    'position' => 2,
                    'size' => 'medium',
                ],
            ],
        ];
    }

    public function getLiveData(): array
    {
        return [];
    }
}
