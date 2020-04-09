<?php

namespace JPNut\Pearley\Compiler\Symbols;

use JPNut\Pearley\Compiler\CompileResult;
use JPNut\Pearley\Compiler\CompileRule;
use JPNut\Pearley\Compiler\Contracts\Symbol;

class StringSymbol implements Symbol
{
    /**
     * @var string
     */
    protected string $string;

    /**
     * @var bool
     */
    protected bool $should_wrap = false;

    /**
     * @param string $string
     */
    public function __construct(string $string)
    {
        $this->string = $string;
    }

    /**
     * @return string
     */
    public function getString(): string
    {
        return $this->string;
    }

    /**
     * @param \JPNut\Pearley\Compiler\CompileRule   $rule
     * @param \JPNut\Pearley\Compiler\CompileResult $result
     *
     * @return string|null
     */
    public function serialize(CompileRule $rule, CompileResult $result): ?string
    {
        if ($this->string === 'null') {
            return null;
        }

        $this->should_wrap = true;

        return $this->string;
    }

    /**
     * @return bool
     */
    public function shouldWrap(): bool
    {
        return $this->should_wrap;
    }
}
