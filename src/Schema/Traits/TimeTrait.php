<?php

namespace Skillcraft\UiSchemaCraft\Schema\Traits;

trait TimeTrait
{
    /**
     * Add time range validation
     */
    public function timeRange(string $min = '00:00', string $max = '23:59'): self
    {
        $this->rule("time_range:$min,$max");
        return $this;
    }

    /**
     * Add date range validation
     */
    public function dateRange(string $min = null, string $max = null): self
    {
        $range = implode(',', array_filter([$min, $max]));
        $this->rule("date_range:$range");
        return $this;
    }

    /**
     * Add time validation
     */
    public function time(string $format = 'H:i'): self
    {
        $this->rule("time:$format");
        return $this;
    }

    /**
     * Add duration validation
     */
    public function duration(): self
    {
        $this->rule('duration');
        return $this;
    }

    /**
     * Static methods for fluent interface
     */
    public static function timeRangeStatic(string $name, string $min = '00:00', string $max = '23:59'): self
    {
        return (new static($name, 'object'))->timeRange($min, $max);
    }

    public static function dateRangeStatic(string $name, string $min = null, string $max = null): self
    {
        return (new static($name, 'object'))->dateRange($min, $max);
    }

    public static function timeStatic(string $name, string $format = 'H:i'): self
    {
        return (new static($name, 'string'))->time($format);
    }

    public static function durationStatic(string $name): self
    {
        return (new static($name, 'string'))->duration();
    }
}
