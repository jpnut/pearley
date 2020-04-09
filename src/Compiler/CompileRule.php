<?php

namespace JPNut\Pearley\Compiler;

class CompileRule
{
    /**
     * @var string
     */
    protected string $name;

    /**
     * @var \JPNut\Pearley\Compiler\Contracts\Symbol[]
     */
    protected array $symbols;

    /**
     * @var \JPNut\Pearley\Compiler\PostProcessor|null
     */
    protected ?PostProcessor $postprocess;

    /**
     * @param  string  $name
     * @param  \JPNut\Pearley\Compiler\Contracts\Symbol[]  $symbols
     * @param  \JPNut\Pearley\Compiler\PostProcessor|null  $postprocess
     */
    public function __construct(string $name, array $symbols, ?PostProcessor $postprocess = null)
    {
        $this->name        = $name;
        $this->symbols     = $symbols;
        $this->postprocess = $postprocess;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return \JPNut\Pearley\Compiler\Contracts\Symbol[]
     */
    public function getSymbols(): array
    {
        return $this->symbols;
    }

    /**
     * @return \JPNut\Pearley\Compiler\PostProcessor|null
     */
    public function getPostprocess(): ?PostProcessor
    {
        return $this->postprocess;
    }
}