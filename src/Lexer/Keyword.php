<?php

namespace JPNut\Pearley\Lexer;

use JPNut\Pearley\Lexer\Contracts\TokenDefinition as TokenDefinitionContract;

class Keyword
{
    /**
     * @var string
     */
    protected string $name;

    /**
     * @var string[]
     */
    protected array $words;

    /**
     * @var \JPNut\Pearley\Lexer\Contracts\TokenDefinition
     */
    protected TokenDefinitionContract $definition;

    /**
     * @param string $name
     * @param string ...$words
     */
    public function __construct(string $name, ...$words)
    {
        $this->name = $name;
        $this->words = $words;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string[]
     */
    public function getWords(): array
    {
        return $this->words;
    }

    /**
     * @param \JPNut\Pearley\Lexer\Contracts\TokenDefinition $definition
     *
     * @return \JPNut\Pearley\Lexer\Contracts\TokenDefinition
     */
    public function getTokenDefinition(TokenDefinitionContract $definition): TokenDefinitionContract
    {
        return $this->definition ??= new TokenDefinition(
            $this->getName(),
            null,
            null,
            $definition->hasLineBreaks(),
            $definition->shouldThrow(),
            $definition->shouldPop(),
            $definition->getPush(),
            $definition->getNext(),
            [],
        );
    }
}
