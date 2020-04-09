<?php

namespace JPNut\Pearley\Generator;

use Exception;

class GeneratorConfig
{
    /**
     * @var string[]
     */
    protected array $indentation;

    /**
     * @var string;
     */
    protected string $stub;

    /**
     * @var string
     */
    protected string $namespace;

    /**
     * @var string
     */
    protected string $class;

    /**
     * @param  string[]  $indentation
     * @param  string  $stub
     * @param  string  $namespace
     * @param  string  $class
     */
    public function __construct(array $indentation, string $stub, string $namespace, string $class)
    {
        $this->indentation = $indentation;
        $this->stub        = $stub;
        $this->namespace   = $namespace;
        $this->class       = $class;
    }

    /**
     * @return \JPNut\Pearley\Generator\PendingGeneratorConfig
     */
    public static function initialise(): PendingGeneratorConfig
    {
        return new PendingGeneratorConfig;
    }

    /**
     * @param  string  $component
     * @return string
     */
    public function getIndentationFor(string $component): string
    {
        return $this->indentation[$component] ?? "";
    }

    /**
     * @return string
     */
    public function getStub(): string
    {
        return $this->stub;
    }

    /**
     * @return string
     */
    public function getNamespace(): string
    {
        return $this->namespace;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }
}
