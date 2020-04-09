<?php

namespace JPNut\Pearley\Compiler\Components;

use JPNut\Pearley\Compiler\Contracts\Component;

class IncludeComponent implements Component
{
    /**
     * @var string
     */
    protected string $name;

    /**
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
