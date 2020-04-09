<?php

namespace JPNut\Pearley\Compiler\Symbols;

use JPNut\Pearley\Compiler\CompileResult;
use JPNut\Pearley\Compiler\CompileRule;
use JPNut\Pearley\Compiler\Contracts\Symbol;

class TokenSymbol implements Symbol
{
    /**
     * @var string
     */
    protected string $token;

    /**
     * @var bool
     */
    protected bool $should_wrap = false;

    /**
     * @param string $token
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param \JPNut\Pearley\Compiler\CompileRule   $rule
     * @param \JPNut\Pearley\Compiler\CompileResult $result
     *
     * @return string|null
     */
    public function serialize(CompileRule $rule, CompileResult $result): ?string
    {
        if (isset($result->getConfigs()['lexer'])) {
            return "['value' => '{$this->token}', 'type' => Symbol::TOKEN]";
        }

        $this->should_wrap = true;

        return $this->token;
    }

    /**
     * @return bool
     */
    public function shouldWrap(): bool
    {
        return $this->should_wrap;
    }
}
