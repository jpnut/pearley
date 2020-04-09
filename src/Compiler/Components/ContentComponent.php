<?php

namespace JPNut\Pearley\Compiler\Components;

use JPNut\Pearley\Compiler\Contracts\Component;

class ContentComponent implements Component
{
    /**
     * @var string
     */
    protected string $value;

    /**
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->value = trim($value);
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }
}
