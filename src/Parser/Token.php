<?php

namespace JPNut\Pearley\Parser;

use JPNut\Pearley\Parser\Contracts\Token as TokenContract;

class Token implements TokenContract
{
    /**
     * @var string
     */
    protected string $text;

    /**
     * @var int
     */
    protected int $offset;

    /**
     * @var int
     */
    protected int $lineBreaks;

    /**
     * @var int
     */
    protected int $line;

    /**
     * @var int
     */
    protected int $col;

    /**
     * @param  string  $text
     * @param  int  $offset
     * @param  int  $lineBreaks
     * @param  int  $line
     * @param  int  $col
     */
    public function __construct(
        string $text,
        int $offset,
        int $lineBreaks,
        int $line,
        int $col
    ) {
        $this->text       = $text;
        $this->offset     = $offset;
        $this->lineBreaks = $lineBreaks;
        $this->line       = $line;
        $this->col        = $col;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->text;
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * @return int
     */
    public function getLineBreaks(): int
    {
        return $this->lineBreaks;
    }

    /**
     * @return int
     */
    public function getLine(): int
    {
        return $this->line;
    }

    /**
     * @return int
     */
    public function getCol(): int
    {
        return $this->col;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return null;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getValue();
    }
}
