<?php

namespace Skillcraft\UiSchemaCraft\Properties;

abstract class AbstractPropertyType implements PropertyTypeInterface
{
    protected array $options;

    public function __construct(array $options = [])
    {
        $this->options = array_merge($this->getDefaultOptions(), $options);
    }

    /**
     * Get the default options for this property type
     *
     * @return array
     */
    abstract protected function getDefaultOptions(): array;

    /**
     * Get an option value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function getOption(string $key, mixed $default = null): mixed
    {
        return $this->options[$key] ?? $default;
    }

    /**
     * Set an option value
     *
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public function setOption(string $key, mixed $value): self
    {
        $this->options[$key] = $value;
        return $this;
    }

    /**
     * Get all options
     *
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}
