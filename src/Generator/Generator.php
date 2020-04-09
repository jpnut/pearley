<?php

namespace JPNut\Pearley\Generator;

use JPNut\Pearley\Compiler\Compiler;
use JPNut\Pearley\Compiler\CompileResult;
use JPNut\Pearley\Compiler\CompileRule;
use JPNut\Pearley\Compiler\Contracts\Symbol;

class Generator
{
    /**
     * @var \JPNut\Pearley\Generator\GeneratorConfig
     */
    protected GeneratorConfig $config;

    /**
     * @var string
     */
    protected string $result = "";

    /**
     * @param  \JPNut\Pearley\Generator\GeneratorConfig|null  $config
     */
    public function __construct(?GeneratorConfig $config = null)
    {
        $this->config = $config ?? $this->defaultConfig();
    }

    /**
     * @param  string  $file
     * @return string
     */
    public function generateFromFile(string $file): string
    {
        return $this->compileAndGenerate($file);
    }

    /**
     * @param  string  $file
     * @return string
     */
    protected function compileAndGenerate(string $file): string
    {
        return $this->generate($this->compiler()->parseAndCompileFromFile($file));
    }

    /**
     * @return \JPNut\Pearley\Compiler\Compiler
     */
    protected function compiler(): Compiler
    {
        return new Compiler;
    }

    /**
     * @param  \JPNut\Pearley\Compiler\CompileResult  $result
     * @return string
     */
    public function generate(CompileResult $result): string
    {
        return $this->reset()
            ->replaceNamespace()
            ->replaceClass()
            ->replaceGrammarUse($result)
            ->replaceGrammarBody($result, $this->config->getIndentationFor('body'))
            ->replaceGrammarRules($result, $this->config->getIndentationFor('rules'))
            ->replaceGrammarStart($result)
            ->replaceGrammarLexer($result)
            ->getResult();
    }

    /**
     * @return \JPNut\Pearley\Generator\Generator
     */
    protected function reset(): self
    {
        $this->result = $this->config->getStub();

        return $this;
    }

    /**
     * @return \JPNut\Pearley\Generator\Generator
     */
    protected function replaceNamespace(): self
    {
        $this->result = str_replace(
            '{{ DummyNamespace }}',
            $this->config->getNamespace(),
            $this->result
        );

        return $this;
    }

    /**
     * @return \JPNut\Pearley\Generator\Generator
     */
    protected function replaceClass(): self
    {
        $this->result = str_replace(
            '{{ DummyClass }}',
            $this->config->getClass(),
            $this->result
        );

        return $this;
    }

    /**
     * @param  \JPNut\Pearley\Compiler\CompileResult  $result
     * @return \JPNut\Pearley\Generator\Generator
     */
    protected function replaceGrammarUse(CompileResult $result): self
    {
        $this->result = str_replace(
            '{{ GrammarUse }}',
            join("\n", array_map(fn(string $use) => "use {$use};", $result->getUse())),
            $this->result
        );

        return $this;
    }

    /**
     * @param  \JPNut\Pearley\Compiler\CompileResult  $result
     * @param  string  $indentation
     * @return \JPNut\Pearley\Generator\Generator
     */
    protected function replaceGrammarBody(CompileResult $result, string $indentation): self
    {
        $this->result = str_replace(
            '{{ GrammarBody }}',
            $this->serialiseBody($result->getContents(), $indentation),
            $this->result
        );

        return $this;
    }

    /**
     * @param  \JPNut\Pearley\Compiler\CompileResult  $result
     * @param  string  $indentation
     * @return \JPNut\Pearley\Generator\Generator
     */
    protected function replaceGrammarRules(CompileResult $result, string $indentation): self
    {
        $this->result = str_replace(
            '{{ GrammarRules }}',
            $this->serialiseRules($result->getRules(), $result, $indentation),
            $this->result);

        return $this;
    }

    /**
     * @param  \JPNut\Pearley\Compiler\CompileResult  $result
     * @return \JPNut\Pearley\Generator\Generator
     */
    protected function replaceGrammarStart(CompileResult $result): self
    {
        $this->result = str_replace('{{ GrammarStart }}', $result->getStart(), $this->result);

        return $this;
    }

    /**
     * @param  \JPNut\Pearley\Compiler\CompileResult  $result
     * @return \JPNut\Pearley\Generator\Generator
     */
    protected function replaceGrammarLexer(CompileResult $result): self
    {
        $this->result = str_replace(
            '{{ GrammarLexer }}',
            isset($result->getConfigs()['lexer']) ? ", \${$result->getConfigs()['lexer']}" : null,
            $this->result
        );

        return $this;
    }

    /**
     * @return string
     */
    protected function getResult(): string
    {
        return $this->result;
    }

    /**
     * @param  array  $lines
     * @param  string  $indentation
     * @param  string  $prefix
     * @return string
     */
    protected function indentLines(array $lines, string $indentation, string $prefix = ""): string
    {
        return join("{$prefix}\n{$indentation}", $lines);
    }

    /**
     * @param  array  $body
     * @param  string  $indentation
     * @return string
     */
    protected function serialiseBody(array $body, string $indentation): string
    {
        return $this->indentLines(
            explode(
                PHP_EOL,
                join("\n\n", $body)
            ),
            $indentation
        );
    }

    /**
     * Note that each rule is indented by 12 spaces to match the formatting of the php file
     *
     * @param  \JPNut\Pearley\Compiler\CompileRule[]  $rules
     * @param  \JPNut\Pearley\Compiler\CompileResult  $result
     * @param  string  $indentation
     * @return string
     */
    protected function serialiseRules(array $rules, CompileResult $result, string $indentation): string
    {
        return $this->indentLines(
            array_map(fn(CompileRule $rule) => $this->serialiseRule($rule, $result), $rules),
            $indentation,
            ","
        );
    }

    /**
     * @param  \JPNut\Pearley\Compiler\CompileRule  $rule
     * @param  \JPNut\Pearley\Compiler\CompileResult  $result
     * @return string
     */
    protected function serialiseRule(CompileRule $rule, CompileResult $result): string
    {
        $ruleString = "[";
        $ruleString .= "'name' => '{$rule->getName()}', ";
        $ruleString .= "'symbols' => [{$this->serialiseSymbols($rule, $result, $rule->getSymbols())}]";

        if (!is_null($postprocess = $rule->getPostprocess()) && !is_null($value = $postprocess->getValue())) {
            $ruleString .= ", 'postprocess' => {$value}";
        }

        $ruleString .= "]";

        return $ruleString;
    }

    /**
     * @param  \JPNut\Pearley\Compiler\CompileRule  $rule
     * @param  \JPNut\Pearley\Compiler\CompileResult  $result
     * @param  array  $symbols
     * @return string
     */
    protected function serialiseSymbols(CompileRule $rule, CompileResult $result, array $symbols): string
    {
        return join(', ', array_map(fn(Symbol $symbol) => $this->serialiseSymbol($rule, $result, $symbol), $symbols));
    }

    /**
     * @param  \JPNut\Pearley\Compiler\CompileRule  $rule
     * @param  \JPNut\Pearley\Compiler\CompileResult  $result
     * @param  \JPNut\Pearley\Compiler\Contracts\Symbol  $symbol
     * @return string|null
     */
    protected function serialiseSymbol(CompileRule $rule, CompileResult $result, Symbol $symbol): ?string
    {
        $value = $symbol->serialize($rule, $result);

        return $symbol->shouldWrap()
            ? "'{$value}'"
            : $value;
    }

    /**
     * @return \JPNut\Pearley\Generator\GeneratorConfig
     */
    protected function defaultConfig(): GeneratorConfig
    {
        return GeneratorConfig::initialise()->create();
    }
}
