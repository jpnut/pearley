<?php

namespace JPNut\Pearley\Generator;

use Exception;

class PendingGeneratorConfig
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
     * @var string|null
     */
    protected string $class;

    /**
     * @param  string[]|null  $indentation
     * @param  string|null  $stub
     * @param  string|null  $namespace
     * @param  string|null  $class
     */
    public function __construct(
        ?array $indentation = null,
        ?string $stub = null,
        ?string $namespace = null,
        ?string $class = null
    ) {
        $this->indentation = $indentation ?? $this->defaultIndentation();
        $this->stub        = $stub ?? $this->defaultStub();
        $this->namespace   = $namespace ?? "";
        $this->class       = $class ?? "Grammar";
    }

    /**
     * @param  array  $indentation
     * @return \JPNut\Pearley\Generator\PendingGeneratorConfig
     */
    public function withIndentation(array $indentation): self
    {
        $this->indentation = $indentation;

        return $this;
    }

    /**
     * @param  string  $stub
     * @return \JPNut\Pearley\Generator\PendingGeneratorConfig
     */
    public function withStub(string $stub): self
    {
        $this->stub = $this->setStubFromFile($stub);

        return $this;
    }

    /**
     * @param  string  $stub
     * @return \JPNut\Pearley\Generator\PendingGeneratorConfig
     */
    public function withStringStub(string $stub): self
    {
        $this->stub = $stub;

        return $this;
    }

    /**
     * @param  string  $namespace
     * @return \JPNut\Pearley\Generator\PendingGeneratorConfig
     */
    public function withNamespace(string $namespace): self
    {
        $this->namespace = $namespace;

        return $this;
    }

    /**
     * @param  string  $class
     * @return \JPNut\Pearley\Generator\PendingGeneratorConfig
     */
    public function withClass(string $class): self
    {
        $this->class = $class;

        return $this;
    }

    /**
     * @return \JPNut\Pearley\Generator\GeneratorConfig
     */
    public function create(): GeneratorConfig
    {
        return new GeneratorConfig(
            $this->indentation,
            $this->stub,
            $this->namespace,
            $this->class,
        );
    }

    /**
     * @return string[]
     */
    protected function defaultIndentation(): array
    {
        return [
            'body'  => '        ', // 2 tabs
            'rules' => '            ', // 3 tabs
        ];
    }

    /**
     * @return string
     */
    protected function defaultStub(): string
    {
        return $this->setStubFromFile(__DIR__.'/../stubs/grammar.stub');
    }

    /**
     * @param  string  $filename
     * @return string
     */
    protected function setStubFromFile(string $filename): string
    {
        if ($file = file_get_contents($filename)) {
            return $file;
        }

        throw new Exception("Could not find stub at {$filename}");
    }
}