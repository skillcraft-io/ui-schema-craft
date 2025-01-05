<?php

namespace Skillcraft\UiSchemaCraft\Exceptions;

use Exception;

class ComponentTypeNotFoundException extends Exception
{
    public function __construct(string $type)
    {
        parent::__construct("Component type '{$type}' not found");
    }
}
