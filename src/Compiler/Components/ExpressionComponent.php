<?php

namespace JPNut\Pearley\Compiler\Components;

use JPNut\Pearley\Compiler\Contracts\Component;

class ExpressionComponent implements Component
{
    /**
     * @var string
     */
    protected string $name;

    /**
     * @var \JPNut\Pearley\Compiler\LanguageRule[]
     */
    protected array $rules;

    /**
     * @param string                                 $name
     * @param \JPNut\Pearley\Compiler\LanguageRule[] $rules
     */
    public function __construct(string $name, array $rules)
    {
        $this->name = $name;
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
     * @return \JPNut\Pearley\Compiler\LanguageRule[]
     */
    public function getRules(): array
    {
        return $this->rules;
    }
}
