<?php

namespace Skillcraft\UiSchemaCraft\Schema\Traits;

use Skillcraft\UiSchemaCraft\Schema\Property;

trait TimeTrait
{
    /**
     * Create a time range property.
     */
    public function timeRange(string $name, ?string $description = null): Property
    {
        return static::timeRangeStatic($name, $description);
    }

    /**
     * Create a time range property (static version).
     */
    public static function timeRangeStatic(string $name, ?string $description = null): Property
    {
        $property = new Property($name, ['object', 'null'], $description ?? ucwords(str_replace('_', ' ', $name)));
        $property->addAttribute('properties', [
            'start' => ['type' => 'string', 'format' => 'date-time'],
            'end' => ['type' => 'string', 'format' => 'date-time']
        ]);
        return $property;
    }

    /**
     * Create a date range property.
     */
    public function dateRange(string $name, ?string $description = null): Property
    {
        return static::dateRangeStatic($name, $description);
    }

    /**
     * Create a date range property (static version).
     */
    public static function dateRangeStatic(string $name, ?string $description = null): Property
    {
        $property = Property::object($name, $description ?? ucwords(str_replace('_', ' ', $name)));
        $property->withBuilder(function ($builder) {
            $builder->string('start')
                ->format('date')
                ->description('Start date');

            $builder->string('end')
                ->format('date')
                ->description('End date');

            $builder->object('options')
                ->properties([
                    'minDate' => ['type' => 'string', 'format' => 'date'],
                    'maxDate' => ['type' => 'string', 'format' => 'date'],
                    'disabledDates' => ['type' => 'array', 'items' => ['type' => 'string', 'format' => 'date']],
                    'format' => ['type' => 'string', 'default' => 'YYYY-MM-DD'],
                    'shortcuts' => ['type' => 'boolean', 'default' => true],
                    'weekNumbers' => ['type' => 'boolean', 'default' => false],
                    'monthSelector' => ['type' => 'boolean', 'default' => true],
                    'yearSelector' => ['type' => 'boolean', 'default' => true]
                ]);
        });
        return $property;
    }

    /**
     * Create a time property.
     */
    public function time(string $name, ?string $description = null): Property
    {
        return static::timeStatic($name, $description);
    }

    /**
     * Create a time property (static version).
     */
    public static function timeStatic(string $name, ?string $description = null): Property
    {
        $property = new Property($name, 'string', $description ?? ucwords(str_replace('_', ' ', $name)));
        $property->format('time');
        return $property;
    }

    /**
     * Create a duration property.
     */
    public function duration(string $name, ?string $description = null): Property
    {
        return static::durationStatic($name, $description);
    }

    /**
     * Create a duration property (static version).
     */
    public static function durationStatic(string $name, ?string $description = null): Property
    {
        $property = new Property($name, 'object', $description ?? ucwords(str_replace('_', ' ', $name)));
        $property->addAttribute('properties', [
            'value' => ['type' => 'number'],
            'unit' => [
                'type' => 'string',
                'enum' => ['seconds', 'minutes', 'hours', 'days', 'weeks', 'months', 'years']
            ]
        ]);
        return $property;
    }
}
