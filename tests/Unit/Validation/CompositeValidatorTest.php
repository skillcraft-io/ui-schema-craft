<?php

namespace Skillcraft\UiSchemaCraft\Tests\Unit\Validation;

use Skillcraft\UiSchemaCraft\Tests\TestCase;
use Skillcraft\UiSchemaCraft\Validation\CompositeValidator;

class CompositeValidatorTest extends TestCase
{
    private CompositeValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new CompositeValidator();
    }

    protected function getPackageProviders($app)
    {
        return [
            'Illuminate\Validation\ValidationServiceProvider',
        ];
    }

    public function test_validate_with_empty_rules_returns_true(): void
    {
        $result = $this->validator->validate(['name' => 'John'], []);
        $this->assertTrue($result);
    }

    public function test_validate_with_valid_data_returns_true(): void
    {
        $data = ['name' => 'John'];
        $rules = ['name' => 'required|string'];
        
        $result = $this->validator->validate($data, $rules);
        $this->assertTrue($result);
    }

    public function test_validate_with_invalid_data_returns_false(): void
    {
        $data = ['age' => 'not a number'];
        $rules = ['age' => 'required|numeric'];
        
        $result = $this->validator->validate($data, $rules);
        $this->assertFalse($result);
    }
}
