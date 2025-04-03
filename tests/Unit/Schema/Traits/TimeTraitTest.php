<?php

namespace Skillcraft\UiSchemaCraft\Tests\Unit\Schema\Traits;

use Skillcraft\UiSchemaCraft\Tests\TestCase;
use Skillcraft\UiSchemaCraft\Schema\Traits\TimeTrait;

class TimeTraitTest extends TestCase
{
    private $traitUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test class that uses the trait
        $this->traitUser = new class('test') {
            use TimeTrait;
            
            private string $name;
            private array $rules = [];
            
            public function __construct(string $name)
            {
                $this->name = $name;
            }
            
            public function rule(string $rule): self
            {
                $this->rules[] = $rule;
                return $this;
            }
            
            public function getRules(): array
            {
                return $this->rules;
            }
        };
    }

    public function testTimeRange(): void
    {
        $result = $this->traitUser->timeRange('08:00', '17:00');
        
        $this->assertSame($this->traitUser, $result);
        $this->assertContains('time_range:08:00,17:00', $this->traitUser->getRules());
    }
    
    public function testTimeRangeWithDefaultValues(): void
    {
        $result = $this->traitUser->timeRange();
        
        $this->assertSame($this->traitUser, $result);
        $this->assertContains('time_range:00:00,23:59', $this->traitUser->getRules());
    }

    public function testDateRange(): void
    {
        $result = $this->traitUser->dateRange('2023-01-01', '2023-12-31');
        
        $this->assertSame($this->traitUser, $result);
        $this->assertContains('date_range:2023-01-01,2023-12-31', $this->traitUser->getRules());
    }
    
    public function testDateRangeWithOnlyMinValue(): void
    {
        $result = $this->traitUser->dateRange('2023-01-01');
        
        $this->assertSame($this->traitUser, $result);
        $this->assertContains('date_range:2023-01-01', $this->traitUser->getRules());
    }
    
    public function testDateRangeWithOnlyMaxValue(): void
    {
        $result = $this->traitUser->dateRange(null, '2023-12-31');
        
        $this->assertSame($this->traitUser, $result);
        $this->assertContains('date_range:2023-12-31', $this->traitUser->getRules());
    }

    public function testTime(): void
    {
        $result = $this->traitUser->time('H:i:s');
        
        $this->assertSame($this->traitUser, $result);
        $this->assertContains('time:H:i:s', $this->traitUser->getRules());
    }
    
    public function testTimeWithDefaultFormat(): void
    {
        $result = $this->traitUser->time();
        
        $this->assertSame($this->traitUser, $result);
        $this->assertContains('time:H:i', $this->traitUser->getRules());
    }

    public function testDuration(): void
    {
        $result = $this->traitUser->duration();
        
        $this->assertSame($this->traitUser, $result);
        $this->assertContains('duration', $this->traitUser->getRules());
    }

    public function testTimeRangeStatic(): void
    {
        $object = $this->traitUser::timeRangeStatic('test-name', '09:00', '18:00');
        $this->assertContains('time_range:09:00,18:00', $object->getRules());
    }

    public function testDateRangeStatic(): void
    {
        $object = $this->traitUser::dateRangeStatic('test-name', '2023-01-01', '2023-12-31');
        $this->assertContains('date_range:2023-01-01,2023-12-31', $object->getRules());
    }

    public function testTimeStatic(): void
    {
        $object = $this->traitUser::timeStatic('test-name', 'H:i:s');
        $this->assertContains('time:H:i:s', $object->getRules());
    }

    public function testDurationStatic(): void
    {
        $object = $this->traitUser::durationStatic('test-name');
        $this->assertContains('duration', $object->getRules());
    }
}
