<?php

namespace Skillcraft\UiSchemaCraft\Tests\Unit\Schema\Traits;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversClass;
use Skillcraft\UiSchemaCraft\Tests\TestCase;
use Skillcraft\UiSchemaCraft\Schema\PropertyBuilder;
use Skillcraft\UiSchemaCraft\Schema\Traits\TimeTrait;

#[CoversClass(TimeTrait::class)]
class TimeTraitTest extends TestCase
{
    protected PropertyBuilder $builder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->builder = new PropertyBuilder();
    }

    #[Test]
    public function it_creates_time_range_property()
    {
        $property = $this->builder->timeRange('schedule', 'Schedule');
        $schema = $property->toArray();

        $this->assertEquals('schedule', $schema['name']);
        $this->assertEquals('Schedule', $schema['description']);
        $this->assertEquals(['object', 'null'], $schema['type']);
        
        // Check properties
        $properties = $schema['properties'];
        
        // Check start time
        $this->assertArrayHasKey('start', $properties);
        $start = $properties['start'];
        $this->assertEquals('string', $start['type']);
        $this->assertEquals('date-time', $start['format']);
        
        // Check end time
        $this->assertArrayHasKey('end', $properties);
        $end = $properties['end'];
        $this->assertEquals('string', $end['type']);
        $this->assertEquals('date-time', $end['format']);
    }

    #[Test]
    public function it_creates_time_range_with_default_label()
    {
        $property = $this->builder->timeRange('meeting_time');
        $schema = $property->toArray();

        $this->assertEquals('meeting_time', $schema['name']);
        $this->assertEquals('Meeting Time', $schema['description']);
        $this->assertEquals(['object', 'null'], $schema['type']);
    }

    #[Test]
    public function it_creates_date_range_property()
    {
        $property = $this->builder->dateRange('availability', 'Availability Period');
        $schema = $property->toArray();

        $this->assertEquals('availability', $schema['name']);
        $this->assertEquals('Availability Period', $schema['description']);
        $this->assertEquals('object', $schema['type']);
        
        // Check properties
        $properties = $schema['properties'];
        
        // Check start date
        $this->assertArrayHasKey('start', $properties);
        $start = $properties['start'];
        $this->assertEquals('string', $start['type']);
        $this->assertEquals('date', $start['format']);
        $this->assertEquals('Start date', $start['description']);
        
        // Check end date
        $this->assertArrayHasKey('end', $properties);
        $end = $properties['end'];
        $this->assertEquals('string', $end['type']);
        $this->assertEquals('date', $end['format']);
        $this->assertEquals('End date', $end['description']);
        
        // Check options
        $this->assertArrayHasKey('options', $properties);
        $options = $properties['options'];
        $this->assertEquals('object', $options['type']);
        
        // Check option properties
        $optionProps = $options['properties'];
        
        // Date constraints
        $this->assertArrayHasKey('minDate', $optionProps);
        $this->assertEquals('string', $optionProps['minDate']['type']);
        $this->assertEquals('date', $optionProps['minDate']['format']);
        
        $this->assertArrayHasKey('maxDate', $optionProps);
        $this->assertEquals('string', $optionProps['maxDate']['type']);
        $this->assertEquals('date', $optionProps['maxDate']['format']);
        
        $this->assertArrayHasKey('disabledDates', $optionProps);
        $this->assertEquals('array', $optionProps['disabledDates']['type']);
        $this->assertEquals('string', $optionProps['disabledDates']['items']['type']);
        $this->assertEquals('date', $optionProps['disabledDates']['items']['format']);
        
        // Display options
        $this->assertArrayHasKey('format', $optionProps);
        $this->assertEquals('string', $optionProps['format']['type']);
        $this->assertEquals('YYYY-MM-DD', $optionProps['format']['default']);
        
        $this->assertArrayHasKey('shortcuts', $optionProps);
        $this->assertEquals('boolean', $optionProps['shortcuts']['type']);
        $this->assertTrue($optionProps['shortcuts']['default']);
        
        $this->assertArrayHasKey('weekNumbers', $optionProps);
        $this->assertEquals('boolean', $optionProps['weekNumbers']['type']);
        $this->assertFalse($optionProps['weekNumbers']['default']);
        
        $this->assertArrayHasKey('monthSelector', $optionProps);
        $this->assertEquals('boolean', $optionProps['monthSelector']['type']);
        $this->assertTrue($optionProps['monthSelector']['default']);
        
        $this->assertArrayHasKey('yearSelector', $optionProps);
        $this->assertEquals('boolean', $optionProps['yearSelector']['type']);
        $this->assertTrue($optionProps['yearSelector']['default']);
    }

    #[Test]
    public function it_creates_date_range_with_default_label()
    {
        $property = $this->builder->dateRange('booking_period');
        $schema = $property->toArray();

        $this->assertEquals('booking_period', $schema['name']);
        $this->assertEquals('Booking Period', $schema['description']);
        $this->assertEquals('object', $schema['type']);
    }

    #[Test]
    public function it_creates_duration_property()
    {
        $property = $this->builder->duration('event_duration', 'Event Duration');
        $schema = $property->toArray();

        $this->assertEquals('event_duration', $schema['name']);
        $this->assertEquals('Event Duration', $schema['description']);
        $this->assertEquals('object', $schema['type']);
        
        // Check properties
        $properties = $schema['properties'];
        
        // Check value
        $this->assertArrayHasKey('value', $properties);
        $value = $properties['value'];
        $this->assertEquals('number', $value['type']);
        
        // Check unit
        $this->assertArrayHasKey('unit', $properties);
        $unit = $properties['unit'];
        $this->assertEquals('string', $unit['type']);
        $this->assertEquals(
            ['seconds', 'minutes', 'hours', 'days', 'weeks', 'months', 'years'],
            $unit['enum']
        );
    }

    #[Test]
    public function it_creates_duration_with_default_label()
    {
        $property = $this->builder->duration('processing_time');
        $schema = $property->toArray();

        $this->assertEquals('processing_time', $schema['name']);
        $this->assertEquals('Processing Time', $schema['description']);
        $this->assertEquals('object', $schema['type']);
    }

    #[Test]
    public function it_creates_time_property()
    {
        $property = $this->builder->time('start_time', 'Start Time');
        $schema = $property->toArray();

        $this->assertEquals('start_time', $schema['name']);
        $this->assertEquals('Start Time', $schema['description']);
        $this->assertEquals('string', $schema['type']);
        $this->assertEquals('time', $schema['format']);
    }

    #[Test]
    public function it_creates_time_with_default_label()
    {
        $property = $this->builder->time('end_time');
        $schema = $property->toArray();

        $this->assertEquals('end_time', $schema['name']);
        $this->assertEquals('End Time', $schema['description']);
        $this->assertEquals('string', $schema['type']);
        $this->assertEquals('time', $schema['format']);
    }
}
