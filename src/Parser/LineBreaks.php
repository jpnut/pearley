<?php

namespace JPNut\Pearley\Parser;

use JPNut\Pearley\Lexer\LexerRegex;
use JPNut\Pearley\Parser\Contracts\LexerRegex as LexerRegexContract;
use JPNut\Pearley\Parser\Contracts\LineBreaks as LineBreaksContract;

class LineBreaks implements LineBreaksContract
{
    /**
     * @var int
     */
    protected int $total;

    /**
     * @var int
     */
    protected int $lastBreakIndex;

    /**
     * @var \JPNut\Pearley\Lexer\LexerRegex
     */
    protected LexerRegex $regex;

    /**
     * @param int $total
     * @param int $lastBreakIndex
     */
    public function __construct(int $total = 0, int $lastBreakIndex = 0)
    {
        $this->total = $total;
        $this->lastBreakIndex = $lastBreakIndex;
    }

    /**
     * @return int
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * @return int
     */
    public function getLastBreakIndex(): int
    {
        return $this->lastBreakIndex;
    }

    /**
     * @param string $text
     *
     * @return \JPNut\Pearley\Parser\LineBreaks
     */
    public function calculate(string $text): self
    {
        $this->reset();

        if ($text === "\n") {
            $this->total = 1;

            return $this;
        }

        $regex = $this->regex();

        while (!empty($regex->match($text))) {
            $this->total++;
            $this->lastBreakIndex = $regex->getLastIndex();
        }

        return $this->cloneAndReset();
    }

    /**
     * @return \JPNut\Pearley\Parser\LineBreaks
     */
    public function default(): self
    {
        return $this->reset()->clone();
    }

    /**
     * @return \JPNut\Pearley\Parser\Contracts\LexerRegex
     */
    protected function regex(): LexerRegexContract
    {
        return $this->regex ??= new LexerRegex('/\n/');
    }

    /**
     * @return \JPNut\Pearley\Parser\LineBreaks
     */
    protected function clone(): self
    {
        return new static($this->total, $this->lastBreakIndex);
    }

    /**
     * @return \JPNut\Pearley\Parser\LineBreaks
     */
    protected function cloneAndReset(): self
    {
        $clone = $this->clone();

        $this->reset();

        return $clone;
    }

    /**
     * @return \JPNut\Pearley\Parser\LineBreaks
     */
    protected function reset(): self
    {
        $this->total = 0;
        $this->lastBreakIndex = 0;

        return $this;
    }
}
