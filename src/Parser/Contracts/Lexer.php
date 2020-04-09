<?php

namespace JPNut\Pearley\Parser\Contracts;

interface Lexer
{
    /**
     * @param string                                          $buffer
     * @param \JPNut\Pearley\Parser\Contracts\LexerState|null $state
     *
     * @return \JPNut\Pearley\Parser\Contracts\Lexer
     */
    public function reset(string $buffer = '', ?LexerState $state = null): self;

    /**
     * @return \JPNut\Pearley\Parser\Contracts\Token|null
     */
    public function next(): ?Token;

    /**
     * @return \JPNut\Pearley\Parser\Contracts\LexerState|null
     */
    public function save(): ?LexerState;

    /**
     * @param \JPNut\Pearley\Parser\Contracts\Token|null $token
     * @param string                                     $message
     *
     * @return string
     */
    public function formatError(?Token $token = null, string $message = 'Error'): string;

    /**
     * @param string $name
     *
     * @return bool
     */
    public function has(string $name): bool;

    /**
     * @return \JPNut\Pearley\Parser\Contracts\LexerState
     */
    public function getState(): LexerState;
}
