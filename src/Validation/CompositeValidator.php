<?php

namespace Skillcraft\UiSchemaCraft\Validation;

use Illuminate\Support\Facades\Validator;

class CompositeValidator
{
    public function validate($data, array $rules): bool
    {
        if (empty($rules)) {
            return true;
        }

        $validator = Validator::make($data, $rules);
        return !$validator->fails();
    }
}
