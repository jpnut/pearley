<?php

namespace JPNut\Pearley\Parser\Contracts;

interface LexerRegex
{
    /**
     * @return int
     */
    public function getLastIndex(): int;

    /**
     * @param  int  $index
     * @return \JPNut\Pearley\Parser\Contracts\LexerRegex
     */
    public function setLastIndex(int $index): self;

    /**
     * @param  string  $input
     * @param  mixed  ...$flags
     * @return array
     */
    public function match(string $input, ...$flags): array;
}
