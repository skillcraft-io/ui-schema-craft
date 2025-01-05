<?php

namespace Skillcraft\UiSchemaCraft\Tests\Unit\Validation;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversClass;
use Skillcraft\UiSchemaCraft\Validation\ValidationResult;

#[CoversClass(ValidationResult::class)]
class ValidationResultTest extends TestCase
{
    private ValidationResult $result;

    protected function setUp(): void
    {
        parent::setUp();
        $this->result = new ValidationResult();
    }

    #[Test]
    public function it_starts_with_no_errors(): void
    {
        $this->assertFalse($this->result->hasErrors());
        $this->assertEquals(['errors' => []], $this->result->toArray());
    }

    #[Test]
    public function it_adds_single_error(): void
    {
        $this->result->addError('field1', 'Error message 1');
        
        $this->assertTrue($this->result->hasErrors());
        $this->assertEquals(
            ['errors' => ['field1' => ['Error message 1']]],
            $this->result->toArray()
        );
    }

    #[Test]
    public function it_adds_multiple_errors_for_same_field(): void
    {
        $this->result->addError('field1', 'Error message 1');
        $this->result->addError('field1', 'Error message 2');
        
        $this->assertTrue($this->result->hasErrors());
        $this->assertEquals(
            ['errors' => ['field1' => ['Error message 1', 'Error message 2']]],
            $this->result->toArray()
        );
    }

    #[Test]
    public function it_adds_errors_for_multiple_fields(): void
    {
        $this->result->addError('field1', 'Error message 1');
        $this->result->addError('field2', 'Error message 2');
        
        $this->assertTrue($this->result->hasErrors());
        $this->assertEquals(
            [
                'errors' => [
                    'field1' => ['Error message 1'],
                    'field2' => ['Error message 2']
                ]
            ],
            $this->result->toArray()
        );
    }

    #[Test]
    public function it_merges_validation_results(): void
    {
        $other = new ValidationResult();
        $other->addError('field1', 'Error message 1');
        $other->addError('field2', 'Error message 2');

        $this->result->addError('field3', 'Error message 3');
        $this->result->merge($other);

        $this->assertTrue($this->result->hasErrors());
        $this->assertEquals(
            [
                'errors' => [
                    'field3' => ['Error message 3'],
                    'field1' => ['Error message 1'],
                    'field2' => ['Error message 2']
                ]
            ],
            $this->result->toArray()
        );
    }

    #[Test]
    public function it_merges_multiple_errors_for_same_field(): void
    {
        $other = new ValidationResult();
        $other->addError('field1', 'Error message 1');
        $other->addError('field1', 'Error message 2');

        $this->result->addError('field1', 'Error message 3');
        $this->result->merge($other);

        $this->assertTrue($this->result->hasErrors());
        $this->assertEquals(
            [
                'errors' => [
                    'field1' => ['Error message 3', 'Error message 1', 'Error message 2']
                ]
            ],
            $this->result->toArray()
        );
    }
}
