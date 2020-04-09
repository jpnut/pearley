<?php

namespace JPNut\Pearley\Compiler;

class Macro
{
    /**
     * @var array
     */
    protected array $args;

    /**
     * @var \JPNut\Pearley\Compiler\LanguageRule[]
     */
    protected array $rules;

    /**
     * @param array                                  $args
     * @param \JPNut\Pearley\Compiler\LanguageRule[] $rules
     */
    public function __construct(array $args, array $rules)
    {
        $this->args = $args;
        $this->rules = $rules;
    }

    /**
     * @return array
     */
    public function getArgs(): array
    {
        return $this->args;
    }

    /**
     * @return \JPNut\Pearley\Compiler\LanguageRule[]
     */
    public function getRules(): array
    {
        return $this->rules;
    }
}
