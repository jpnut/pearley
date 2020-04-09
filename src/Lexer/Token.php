<?php

namespace JPNut\Pearley\Lexer;

use JPNut\Pearley\Lexer\Contracts\Token as TokenContract;
use JPNut\Pearley\Lexer\Contracts\TokenDefinition as TokenDefinitionContract;

class Token implements TokenContract
{
    /**
     * @var \JPNut\Pearley\Lexer\Contracts\TokenDefinition
     */
    protected TokenDefinitionContract $definition;

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
     * @param \JPNut\Pearley\Lexer\Contracts\TokenDefinition $definition
     * @param string                                         $text
     * @param int                                            $offset
     * @param int                                            $lineBreaks
     * @param int                                            $line
     * @param int                                            $col
     */
    public function __construct(
        TokenDefinitionContract $definition,
        string $text,
        int $offset,
        int $lineBreaks,
        int $line,
        int $col
    ) {
        $this->definition = $definition;
        $this->text = $text;
        $this->offset = $offset;
        $this->lineBreaks = $lineBreaks;
        $this->line = $line;
        $this->col = $col;
    }

    /**
     * @return \JPNut\Pearley\Lexer\Contracts\TokenDefinition
     */
    public function getDefinition(): TokenDefinitionContract
    {
        return $this->definition;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        if (is_null($valueMap = $this->definition->getValueMap())) {
            return $this->text;
        }

        return $valueMap($this->text);
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
        return $this->definition->getName();
    }
}
