<?php

namespace JPNut\Pearley\Compiler\Components;

use JPNut\Pearley\Compiler\Contracts\Component;

class ConfigComponent implements Component
{
    /**
     * @var string
     */
    protected string $key;

    /**
     * @var string
     */
    protected string $value;

    /**
     * @param  string  $key
     * @param  string  $value
     */
    public function __construct(string $key, string $value)
    {
        $this->key   = $key;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }
}