<?php

namespace JPNut\Pearley\Lexer\Contracts;

use JPNut\Pearley\Parser\Contracts\Lexer as BaseLexer;
use JPNut\Pearley\Parser\Contracts\LexerState;
use JPNut\Pearley\Parser\Contracts\Token;

interface Lexer extends BaseLexer
{
    /**
     * @return \JPNut\Pearley\Lexer\Contracts\LexerConfig
     */
    public function config(): LexerConfig;

    /**
     * @return \JPNut\Pearley\Lexer\Contracts\Token|null
     */
    public function next(): ?Token;

    /**
     * @return \JPNut\Pearley\Lexer\Contracts\LexerState|null
     */
    public function save(): ?LexerState;

    /**
     * @param  string  $buffer
     * @param  \JPNut\Pearley\Parser\Contracts\LexerState|null  $state
     * @return \JPNut\Pearley\Lexer\Contracts\Lexer
     */
    public function reset(string $buffer = "", ?LexerState $state = null): self;

    /**
     * @param  \JPNut\Pearley\Parser\Contracts\Token|null  $token
     * @param  string  $message
     * @return string
     */
    public function formatError(?Token $token = null, string $message = "Error"): string;

    /**
     * @param  string  $name
     * @return bool
     */
    public function has(string $name): bool;
}
