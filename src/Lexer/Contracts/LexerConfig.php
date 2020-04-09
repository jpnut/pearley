<?php

namespace JPNut\Pearley\Lexer\Contracts;

use JPNut\Pearley\Parser\Contracts\LexerRegex;

interface LexerConfig
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param \JPNut\Pearley\Lexer\Contracts\TokenDefinition $definition
     *
     * @return mixed
     */
    public function addTokenDefinition(TokenDefinition $definition);

    /**
     * @return \JPNut\Pearley\Lexer\Contracts\TokenDefinition[]
     */
    public function getTokenDefinitions(): array;

    /**
     * @param int    $index
     * @param string $text
     *
     * @return \JPNut\Pearley\Lexer\Contracts\TokenDefinition
     */
    public function getTokenDefinition(int $index, string $text): TokenDefinition;

    /**
     * @return string[]
     */
    public function getTokenNames(): array;

    /**
     * @return string[]
     */
    public function getOtherConfigs(): array;

    /**
     * @return \JPNut\Pearley\Parser\Contracts\LexerRegex
     */
    public function getRegex(): LexerRegex;
}
