<?php

namespace JPNut\Pearley\Compiler;

use JPNut\Pearley\Compiler\Contracts\HasRules;

class LanguageRule
{
    /**
     * @var \JPNut\Pearley\Compiler\Contracts\Symbol[]
     */
    protected array $symbols;

    /**
     * @var \JPNut\Pearley\Compiler\PostProcessor|null
     */
    protected ?PostProcessor $postprocessor;

    /**
     * @param  array  $symbols
     * @param  \JPNut\Pearley\Compiler\PostProcessor|null  $postprocessor
     */
    public function __construct(array $symbols, ?PostProcessor $postprocessor = null)
    {
        $this->symbols       = $symbols;
        $this->postprocessor = $postprocessor;
    }

    /**
     * @param  string  $ruleName
     * @param  \JPNut\Pearley\Compiler\CompileResult  $result
     * @return \JPNut\Pearley\Compiler\CompileRule[]
     */
    public function toCompileRules(string $ruleName, CompileResult $result): array
    {
        $rule = new CompileRule(
            $ruleName,
            $this->symbols,
            $this->postprocessor
        );

        return array_merge(
            [$rule],
            $this->generateCompileRules($ruleName, $rule, $result)
        );
    }

    /**
     * @param  string  $ruleName
     * @param  \JPNut\Pearley\Compiler\CompileRule  $rule
     * @param  \JPNut\Pearley\Compiler\CompileResult  $result
     * @return \JPNut\Pearley\Compiler\CompileRule[]
     */
    public function generateCompileRules(string $ruleName, CompileRule $rule, CompileResult $result): array
    {
        $rules = [];

        foreach ($this->symbols as $symbol) {
            if ($symbol instanceof HasRules) {
                $rules = array_merge($rules, $symbol->generateCompileRules($rule, $result));
            }
        }

        return $rules;
    }

    /**
     * @return \JPNut\Pearley\Compiler\Contracts\Symbol[]
     */
    public function getSymbols(): array
    {
        return $this->symbols;
    }
}