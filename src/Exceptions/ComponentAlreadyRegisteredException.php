<?php

namespace Skillcraft\UiSchemaCraft\Exceptions;

use Exception;

class ComponentAlreadyRegisteredException extends Exception
{
    public function __construct(string $name)
    {
        parent::__construct("Component '{$name}' is already registered");
    }
}
