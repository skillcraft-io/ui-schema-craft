<?php

namespace Skillcraft\UiSchemaCraft\Properties;

interface PropertyTypeInterface
{
    /**
     * Validate a value against this property type
     *
     * @param mixed $value
     * @return bool
     */
    public function validate($value): bool;

    /**
     * Cast a value to this property type
     *
     * @param mixed $value
     * @return mixed
     */
    public function cast($value): mixed;

    /**
     * Get the JavaScript type for this property
     *
     * @return string
     */
    public function getJsType(): string;

    /**
     * Get the default value for this property type
     *
     * @return mixed
     */
    public function getDefaultValue(): mixed;
}
