<?php

namespace Skillcraft\UiSchemaCraft\Schema\Traits;

use Skillcraft\UiSchemaCraft\Schema\Property;

trait ChartTrait
{
    /**
     * Create a chart property.
     */
    public function chart(string $name, ?string $label = null): Property
    {
        $property = new Property($name, 'object', $label);
        $property->addAttribute('properties', [
            'type' => ['type' => 'string', 'enum' => ['line', 'bar', 'pie', 'scatter']],
            'data' => ['type' => 'object'],
            'options' => ['type' => 'object']
        ]);
        return $property;
    }

    /**
     * Create a line chart property.
     */
    public function lineChart(string $name, ?string $label = null): Property
    {
        $property = $this->chart($name, $label);
        $property->addAttribute('type', 'line');
        return $property;
    }

    /**
     * Create a bar chart property.
     */
    public function barChart(string $name, ?string $label = null): Property
    {
        $property = $this->chart($name, $label);
        $property->addAttribute('type', 'bar');
        return $property;
    }

    /**
     * Create a pie chart property.
     */
    public function pieChart(string $name, ?string $label = null): Property
    {
        $property = $this->chart($name, $label);
        $property->addAttribute('type', 'pie');
        return $property;
    }

    /**
     * Create a scatter plot property.
     */
    public function scatterPlot(string $name, ?string $label = null): Property
    {
        $property = $this->chart($name, $label);
        $property->addAttribute('type', 'scatter');
        return $property;
    }
}
