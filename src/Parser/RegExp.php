<?php

namespace JPNut\Pearley\Parser;

class RegExp
{
    /**
     * @var string
     */
    protected string $pattern;

    /**
     * @param  string  $pattern
     */
    public function __construct(string $pattern)
    {
        $this->pattern = $pattern;
    }

    /**
     * @return string
     */
    public function getPattern(): string
    {
        return $this->pattern;
    }

    /**
     * @return string
     */
    public function getQualifiedPattern(): string
    {
        return "/{$this->pattern}/";
    }
}
