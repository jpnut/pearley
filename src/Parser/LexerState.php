<?php

namespace JPNut\Pearley\Parser;

use JPNut\Pearley\Parser\Contracts\LexerState as LexerStateContract;
use JPNut\Pearley\Parser\Contracts\LineBreaks as LineBreaksContract;

class LexerState implements LexerStateContract
{
    /**
     * @var int
     */
    protected int $index;

    /**
     * @var int
     */
    protected int $line;

    /**
     * @var int
     */
    protected int $lastLineBreak;

    /**
     * @param int $index
     * @param int $line
     * @param int $lastLineBreak
     */
    public function __construct(int $index = 0, int $line = 1, int $lastLineBreak = 0)
    {
        $this->index = $index;
        $this->line = $line;
        $this->lastLineBreak = $lastLineBreak;
    }

    /**
     * @param bool $withIndex
     *
     * @return \JPNut\Pearley\Parser\Contracts\LexerState
     */
    public function clone(bool $withIndex = false): LexerStateContract
    {
        return new static(
            $withIndex ? $this->index : 0,
            $this->line,
            $withIndex ? $this->getLastLineBreak() : -$this->getCol(),
        );
    }

    /**
     * @return int
     */
    public function getIndex(): int
    {
        return $this->index;
    }

    /**
     * @param int $amount
     *
     * @return \JPNut\Pearley\Parser\LexerState
     */
    protected function incrementIndex(int $amount): self
    {
        $this->index += $amount;

        return $this;
    }

    /**
     * @return int
     */
    public function getLine(): int
    {
        return $this->line;
    }

    /**
     * @param int $amount
     *
     * @return \JPNut\Pearley\Parser\LexerState
     */
    protected function incrementLine(int $amount): self
    {
        $this->line += $amount;

        return $this;
    }

    /**
     * @return int
     */
    public function getCol(): int
    {
        return $this->index - $this->lastLineBreak;
    }

    /**
     * @return int
     */
    public function getLastLineBreak(): int
    {
        return $this->lastLineBreak;
    }

    /**
     * @param int $lastLineBreak
     *
     * @return \JPNut\Pearley\Parser\LexerState
     */
    protected function setLastLineBreak(int $lastLineBreak): self
    {
        $this->lastLineBreak = $lastLineBreak;

        return $this;
    }

    /**
     * @param string                                     $text
     * @param \JPNut\Pearley\Parser\Contracts\LineBreaks $lineBreaks
     *
     * @return \JPNut\Pearley\Parser\LexerState
     */
    public function updateState(string $text, LineBreaksContract $lineBreaks): self
    {
        $this->incrementIndex($size = strlen($text))
            ->incrementLine($total = $lineBreaks->getTotal())
            ->setLastLineBreak($total > 0 ? $this->index : $this->lastLineBreak);

        return $this;
    }
}
