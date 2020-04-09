<?php

namespace JPNut\Pearley\Lexer\Contracts;

use Closure;

interface TokenDefinition
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string|null
     */
    public function getPattern(): ?string;

    /**
     * @return \Closure|null
     */
    public function getValueMap(): ?Closure;

    /**
     * @return bool
     */
    public function shouldThrow(): bool;

    /**
     * @return bool
     */
    public function hasLineBreaks(): bool;

    /**
     * @return bool
     */
    public function shouldPop(): bool;

    /**
     * @return bool
     */
    public function shouldPush(): bool;

    /**
     * @return string|null
     */
    public function getPush(): ?string;

    /**
     * @return bool
     */
    public function hasNext(): bool;

    /**
     * @return string|null
     */
    public function getNext(): ?string;

    /**
     * @param  string  $text
     * @return \JPNut\Pearley\Lexer\Contracts\TokenDefinition
     */
    public function getDefinitionFromText(string $text): TokenDefinition;
}
