<?php

namespace JPNut\Pearley\Lexer;

use JPNut\Pearley\Lexer\Contracts\LexerState as LexerStateContract;
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
    protected int $col;

    /**
     * @var string
     */
    protected string $configName;

    /**
     * @var string[]
     */
    protected array $stack;

    /**
     * @param int    $index
     * @param int    $line
     * @param int    $col
     * @param string $configName
     * @param array  $stack
     */
    public function __construct(int $index, int $line, int $col, string $configName, array $stack)
    {
        $this->index = $index;
        $this->line = $line;
        $this->col = $col;
        $this->configName = $configName;
        $this->stack = $stack;
    }

    /**
     * @param string $configName
     *
     * @return \JPNut\Pearley\Lexer\LexerState
     */
    public static function create(string $configName): self
    {
        return new static(
            0,
            1,
            1,
            $configName,
            [],
        );
    }

    /**
     * @param bool $withIndex
     *
     * @return \JPNut\Pearley\Lexer\LexerState
     */
    public function clone(bool $withIndex = false): self
    {
        return new static(
            $withIndex ? $this->index : 0,
            $this->line,
            $this->col,
            $this->configName,
            array_values($this->stack),
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
     * @return \JPNut\Pearley\Lexer\LexerState
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
     * @return \JPNut\Pearley\Lexer\LexerState
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
        return $this->col;
    }

    /**
     * @param int $col
     *
     * @return \JPNut\Pearley\Lexer\LexerState
     */
    protected function setCol(int $col): self
    {
        $this->col = $col;

        return $this;
    }

    /**
     * @return int
     */
    public function getLastLineBreak(): int
    {
        return $this->index - $this->col;
    }

    /**
     * @param string                                     $text
     * @param \JPNut\Pearley\Parser\Contracts\LineBreaks $lineBreaks
     *
     * @return \JPNut\Pearley\Lexer\LexerState
     */
    public function updateState(string $text, LineBreaksContract $lineBreaks): self
    {
        $this->incrementIndex($size = strlen($text))
            ->incrementLine($lineBreaks->getTotal())
            ->setCol($this->calculateNewCol($lineBreaks, $size));

        return $this;
    }

    /**
     * @param \JPNut\Pearley\Parser\Contracts\LineBreaks $lineBreaks
     * @param int                                        $size
     *
     * @return int
     */
    protected function calculateNewCol(LineBreaksContract $lineBreaks, int $size): int
    {
        if ($lineBreaks->getTotal() !== 0) {
            return $size - $lineBreaks->getLastBreakIndex() + 1;
        }

        return $this->getCol() + $size;
    }

    /**
     * @return string[]
     */
    public function getStack(): array
    {
        return $this->stack;
    }

    /**
     * @return string
     */
    public function getConfigName(): string
    {
        return $this->configName;
    }

    /**
     * @param string|null $configName
     *
     * @return \JPNut\Pearley\Lexer\LexerState
     */
    public function setConfigName(?string $configName): self
    {
        $this->configName = $configName ?? $this->configName;

        return $this;
    }

    /**
     * @return \JPNut\Pearley\Lexer\Contracts\LexerState|null
     */
    public function pop(): ?LexerStateContract
    {
        return $this->setConfigName(array_pop($this->stack));
    }

    /**
     * @param string $configName
     *
     * @return \JPNut\Pearley\Lexer\LexerState
     */
    public function push(string $configName): self
    {
        array_push($this->stack, $this->configName);

        return $this->setConfigName($configName);
    }
}
