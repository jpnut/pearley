<?php

namespace JPNut\Pearley\Lexer\Contracts;

use JPNut\Pearley\Parser\Contracts\LexerState as BaseLexerState;
use JPNut\Pearley\Parser\Contracts\LineBreaks;

interface LexerState extends BaseLexerState
{
    /**
     * @param string $config
     *
     * @return \JPNut\Pearley\Lexer\Contracts\LexerState
     */
    public static function create(string $config): self;

    /**
     * @param bool $withIndex
     *
     * @return \JPNut\Pearley\Lexer\Contracts\LexerState
     */
    public function clone(bool $withIndex = false): self;

    /**
     * @return int
     */
    public function getIndex(): int;

    /**
     * @return int
     */
    public function getLine(): int;

    /**
     * @return int
     */
    public function getCol(): int;

    /**
     * @param string                                     $text
     * @param \JPNut\Pearley\Parser\Contracts\LineBreaks $lineBreaks
     *
     * @return \JPNut\Pearley\Lexer\Contracts\LexerState
     */
    public function updateState(string $text, LineBreaks $lineBreaks): self;

    /**
     * @return array
     */
    public function getStack(): array;

    /**
     * @return string
     */
    public function getConfigName(): string;

    /**
     * @param string|null $configName
     *
     * @return \JPNut\Pearley\Lexer\Contracts\LexerState
     */
    public function setConfigName(?string $configName): self;

    /**
     * @return \JPNut\Pearley\Lexer\Contracts\LexerState|null
     */
    public function pop(): ?self;

    /**
     * @param string $configName
     *
     * @return \JPNut\Pearley\Lexer\Contracts\LexerState|null
     */
    public function push(string $configName): ?self;
}
