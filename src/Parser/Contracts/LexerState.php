<?php

namespace JPNut\Pearley\Parser\Contracts;

interface LexerState
{
    /**
     * @param  bool  $withIndex
     * @return \JPNut\Pearley\Parser\Contracts\LexerState
     */
    public function clone(bool $withIndex = false): LexerState;

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
     * @return int
     */
    public function getLastLineBreak(): int;

    /**
     * @param  string  $text
     * @param  \JPNut\Pearley\Parser\Contracts\LineBreaks  $lineBreaks
     * @return \JPNut\Pearley\Parser\Contracts\LexerState
     */
    public function updateState(string $text, LineBreaks $lineBreaks): self;
}
