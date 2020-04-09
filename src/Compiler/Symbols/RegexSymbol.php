<?php

namespace JPNut\Pearley\Compiler\Symbols;

use JPNut\Pearley\Compiler\CompileResult;
use JPNut\Pearley\Compiler\CompileRule;
use JPNut\Pearley\Compiler\Contracts\Symbol;
use JPNut\Pearley\Parser\RegExp;

class RegexSymbol implements Symbol
{
    /**
     * @var \JPNut\Pearley\Parser\RegExp
     */
    protected RegExp $regexp;

    /**
     * @param  \JPNut\Pearley\Parser\RegExp  $regexp
     */
    public function __construct(RegExp $regexp)
    {
        $this->regexp = $regexp;
    }

    /**
     * @return \JPNut\Pearley\Parser\RegExp
     */
    public function getRegExp(): RegExp
    {
        return $this->regexp;
    }

    /**
     * @param  \JPNut\Pearley\Compiler\CompileRule  $rule
     * @param  \JPNut\Pearley\Compiler\CompileResult  $result
     * @return string
     */
    public function serialize(CompileRule $rule, CompileResult $result): string
    {
        return "['value' => '{$this->regexp->getPattern()}', 'type' => Symbol::REGEX]";
    }

    /**
     * @return bool
     */
    public function shouldWrap(): bool
    {
        return false;
    }
}