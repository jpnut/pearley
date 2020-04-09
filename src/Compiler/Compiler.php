<?php

namespace JPNut\Pearley\Compiler;

use Exception;
use InvalidArgumentException;
use JPNut\Pearley\Compiler\Components\BuiltinComponent;
use JPNut\Pearley\Compiler\Components\ConfigComponent;
use JPNut\Pearley\Compiler\Components\ContentComponent;
use JPNut\Pearley\Compiler\Components\ExpressionComponent;
use JPNut\Pearley\Compiler\Components\IncludeComponent;
use JPNut\Pearley\Compiler\Components\MacroComponent;
use JPNut\Pearley\Compiler\Components\UseComponent;
use JPNut\Pearley\Compiler\Contracts\Component;
use JPNut\Pearley\Parser\Grammar;
use JPNut\Pearley\Parser\Parser;
use JPNut\Pearley\Parser\ParserConfig;

class Compiler
{
    /**
     * @var array
     */
    protected array $already_compiled = [];

    /**
     * @var \JPNut\Pearley\Parser\Grammar
     */
    protected Grammar $grammar;

    /**
     * @param \JPNut\Pearley\Parser\Grammar|null $grammar
     */
    public function __construct(?Grammar $grammar = null)
    {
        $this->grammar = $grammar ?? PearleyGrammar::grammar();
    }

    /**
     * @param string $file
     *
     * @return \JPNut\Pearley\Compiler\CompileResult
     */
    public function parseAndCompileFromFile(string $file): CompileResult
    {
        if (!file_exists($file)) {
            throw new InvalidArgumentException(
                "File '{$file}' could not be found."
            );
        }

        if (($grammar = file_get_contents($file)) === false) {
            throw new InvalidArgumentException(
                "There was a problem reading from file '{$file}'."
            );
        }

        return $this->parseAndCompile($grammar, $file);
    }

    /**
     * @param string      $grammar
     * @param string|null $file_name
     *
     * @return \JPNut\Pearley\Compiler\CompileResult
     */
    public function parseAndCompile(string $grammar, ?string $file_name = null): CompileResult
    {
        return $this->compile($this->parseGrammar($grammar), $file_name);
    }

    /**
     * @param array       $components
     * @param string|null $file_name
     *
     * @return \JPNut\Pearley\Compiler\CompileResult
     */
    public function compile(array $components, ?string $file_name = null): CompileResult
    {
        $result = new CompileResult();

        foreach ($components as $component) {
            if (!($component instanceof Component)) {
                throw new Exception('Invalid compile component: expected instance of '.Component::class);
            }

            switch (true) {
                case $component instanceof ContentComponent:
                    $this->addContent($component, $result);
                    break;
                case $component instanceof IncludeComponent:
                    $this->includeGrammar($component, $result, $file_name);
                    break;
                case $component instanceof BuiltinComponent:
                    $this->includeBuiltinGrammar($component, $result);
                    break;
                case $component instanceof MacroComponent:
                    $this->addMacro($component, $result);
                    break;
                case $component instanceof ConfigComponent:
                    $this->addConfig($component, $result);
                    break;
                case $component instanceof UseComponent:
                    $this->addUse($component, $result);
                    break;
                case $component instanceof ExpressionComponent:
                    $this->addExpression($component, $result);
                    break;
                default:
                    throw new Exception('Unrecognised compile component: '.get_class($component));
            }
        }

        return $result;
    }

    /**
     * @param string $grammar
     *
     * @return array
     */
    protected function parseGrammar(string $grammar): array
    {
        return $this->parser()->feed($grammar)->getResults()[0];
    }

    /**
     * @return \JPNut\Pearley\Parser\Parser
     */
    protected function parser(): Parser
    {
        return new Parser(new ParserConfig($this->grammar));
    }

    /**
     * @param \JPNut\Pearley\Compiler\Components\ContentComponent $component
     * @param \JPNut\Pearley\Compiler\CompileResult               $result
     */
    protected function addContent(ContentComponent $component, CompileResult $result): void
    {
        $result->addContent($component->getValue());
    }

    /**
     * @param \JPNut\Pearley\Compiler\Components\IncludeComponent $component
     * @param \JPNut\Pearley\Compiler\CompileResult               $result
     * @param string|null                                         $file_name
     */
    protected function includeGrammar(IncludeComponent $component, CompileResult $result, ?string $file_name): void
    {
        $this->addGrammarFromFile(
            $result,
            implode(DIRECTORY_SEPARATOR, [
                rtrim(
                    !is_null($file_name) ? dirname($file_name) : getcwd(),
                    '\/'
                ),
                $component->getName(),
            ])
        );
    }

    /**
     * @param \JPNut\Pearley\Compiler\Components\BuiltinComponent $component
     * @param \JPNut\Pearley\Compiler\CompileResult               $result
     */
    protected function includeBuiltinGrammar(BuiltinComponent $component, CompileResult $result): void
    {
        $this->addGrammarFromFile(
            $result,
            implode(DIRECTORY_SEPARATOR, [
                __DIR__,
                '../Builtin',
                $component->getName(),
            ])
        );
    }

    /**
     * @param \JPNut\Pearley\Compiler\CompileResult $result
     * @param string                                $path
     */
    protected function addGrammarFromFile(CompileResult $result, string $path): void
    {
        if (isset($this->already_compiled[$path])) {
            return;
        }

        $this->already_compiled[$path] = true;

        $nested_result = $this->parseAndCompileFromFile($path);

        $result->addRules($nested_result->getRules())
            ->addContents($nested_result->getContents())
            ->mergeConfigs($nested_result->getConfigs())
            ->mergeMacros($nested_result->getMacros());
    }

    /**
     * @param \JPNut\Pearley\Compiler\Components\MacroComponent $component
     * @param \JPNut\Pearley\Compiler\CompileResult             $result
     */
    protected function addMacro(MacroComponent $component, CompileResult $result): void
    {
        $result->addMacro($component->getName(), $component->getArgs(), $component->getRules());
    }

    /**
     * @param \JPNut\Pearley\Compiler\Components\ConfigComponent $component
     * @param \JPNut\Pearley\Compiler\CompileResult              $result
     */
    protected function addConfig(ConfigComponent $component, CompileResult $result): void
    {
        $result->addConfig($component->getKey(), $component->getValue());
    }

    /**
     * @param \JPNut\Pearley\Compiler\Components\UseComponent $component
     * @param \JPNut\Pearley\Compiler\CompileResult           $result
     */
    protected function addUse(UseComponent $component, CompileResult $result): void
    {
        $result->addUse($component->getValue());
    }

    /**
     * @param \JPNut\Pearley\Compiler\Components\ExpressionComponent $component
     * @param \JPNut\Pearley\Compiler\CompileResult                  $result
     */
    protected function addExpression(ExpressionComponent $component, CompileResult $result): void
    {
        foreach ($component->getRules() as $rule) {
            $result->addRules($rule->toCompileRules($name = $component->getName(), $result), $name);
        }
    }
}
