<?php

namespace JPNut\Pearley\Lexer;

use JPNut\Pearley\Parser\Contracts\LexerRegex as LexerRegexContract;
use InvalidArgumentException;

class LexerRegex implements LexerRegexContract
{
    /**
     * @var string
     */
    protected string $pattern;

    /**
     * @var int
     */
    protected int $lastIndex;

    /**
     * @param  string  $pattern
     */
    public function __construct(string $pattern)
    {
        $this->pattern   = $pattern;
        $this->lastIndex = 0;
    }

    /**
     * @return int
     */
    public function getLastIndex(): int
    {
        return $this->lastIndex;
    }

    /**
     * @param  int  $index
     * @return \JPNut\Pearley\Parser\Contracts\LexerRegex
     */
    public function setLastIndex(int $index): LexerRegexContract
    {
        $this->lastIndex = $index;

        return $this;
    }

    /**
     * @param  string  $input
     * @param  mixed  ...$flags
     * @return array
     */
    public function match(string $input, ...$flags): array
    {
        $result = preg_match($this->pattern, $this->input($input), $matches, PREG_OFFSET_CAPTURE, ...$flags);

        if ($result === false) {
            throw new InvalidArgumentException("Invalid regex: {$this->pattern}");
        }

        if (!empty($matches)) {
            $this->lastIndex += $matches[0][1] + 1;
        }

        return $matches;
    }

    /**
     * @param  string  $input
     * @return string
     */
    protected function input(string $input): string
    {
        return substr($input, $this->lastIndex);
    }
}
