<?php

namespace JPNut\Pearley\Compiler\Symbols;

use Exception;
use JPNut\Pearley\Compiler\CompileResult;
use JPNut\Pearley\Compiler\CompileRule;
use JPNut\Pearley\Compiler\Contracts\Symbol;

class MixinSymbol implements Symbol
{
    /**
     * @var string
     */
    protected string $mixin;

    /**
     * @param  string  $mixin
     */
    public function __construct(string $mixin)
    {
        $this->mixin = $mixin;
    }

    /**
     * @return string
     */
    public function getMixin(): string
    {
        return $this->mixin;
    }

    /**
     * @param  \JPNut\Pearley\Compiler\CompileRule  $rule
     * @param  \JPNut\Pearley\Compiler\CompileResult  $result
     * @return string
     */
    public function serialize(CompileRule $rule, CompileResult $result): string
    {
        $qualified_name = $this->qualifyName($name = $this->getArgNameFromRuleName($rule->getName()));

        while (is_null($mapped_name = $result->getMacroMap($qualified_name))) {
            $qualified_name = $this->qualifyName($this->getArgNameFromRuleName($name));
        }

        return $mapped_name;
    }

    /**
     * @param  string  $name
     * @return string
     */
    protected function qualifyName(string $name): string
    {
        return "{$name}-{$this->mixin}";
    }

    /**
     * @return bool
     */
    public function shouldWrap(): bool
    {
        return true;
    }

    /**
     * @param  string  $name
     * @return string
     */
    protected function getArgNameFromRuleName(string $name): string
    {
        if (preg_match(
                '/(?:\$macrocall\$\d+)(?!\$macrocall\$\d+)/',
                $name,
                $matches, PREG_OFFSET_CAPTURE
            ) === 0) {
            throw new Exception("Could not parse rule name: expecting '\$macrocall\$\d+' but none found.");
        }

        return substr($name, 0, $matches[0][1]);
    }
}