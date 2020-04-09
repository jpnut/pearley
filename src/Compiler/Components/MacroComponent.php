<?php

namespace JPNut\Pearley\Compiler\Components;

use JPNut\Pearley\Compiler\Contracts\Component;

class MacroComponent implements Component
{
    /**
     * @var string
     */
    protected string $name;

    /**
     * @var array
     */
    protected array $args;

    /**
     * @var array
     */
    protected array $rules;

    /**
     * @param string $name
     * @param array  $args
     * @param array  $rules
     */
    public function __construct(string $name, array $args, array $rules)
    {
        $this->name = $name;
        $this->args = $args;
        $this->rules = $rules;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getArgs(): array
    {
        return $this->args;
    }

    /**
     * @return array
     */
    public function getRules(): array
    {
        return $this->rules;
    }
}
