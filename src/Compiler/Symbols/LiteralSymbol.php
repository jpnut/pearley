<?php

namespace JPNut\Pearley\Compiler\Symbols;

use JPNut\Pearley\Compiler\CompileResult;
use JPNut\Pearley\Compiler\CompileRule;
use JPNut\Pearley\Compiler\Contracts\HasRules;
use JPNut\Pearley\Compiler\Contracts\Symbol;
use JPNut\Pearley\Compiler\LanguageRule;
use JPNut\Pearley\Compiler\PostProcessor;

class LiteralSymbol implements Symbol, HasRules
{
    /**
     * @var string
     */
    protected string $literal;

    /**
     * @var bool
     */
    protected bool $is_serialized = false;

    /**
     * @var string|null
     */
    protected ?string $serialized;

    /**
     * @var bool
     */
    protected bool $has_rules = false;

    /**
     * @var bool
     */
    protected bool $should_wrap = false;

    /**
     * @param string $literal
     */
    public function __construct(string $literal)
    {
        $this->literal = $literal;
    }

    /**
     * @return string
     */
    public function getLiteral(): string
    {
        return $this->literal;
    }

    /**
     * @param \JPNut\Pearley\Compiler\CompileRule   $rule
     * @param \JPNut\Pearley\Compiler\CompileResult $result
     *
     * @return string|null
     */
    public function serialize(CompileRule $rule, CompileResult $result): ?string
    {
        if ($this->is_serialized) {
            return $this->serialized;
        }

        $this->is_serialized = true;

        return $this->serialized = $this->getSerializedValue($rule, $result);
    }

    public function shouldWrap(): bool
    {
        return $this->should_wrap;
    }

    /**
     * @param \JPNut\Pearley\Compiler\CompileRule   $rule
     * @param \JPNut\Pearley\Compiler\CompileResult $result
     *
     * @return string|null
     */
    protected function getSerializedValue(CompileRule $rule, CompileResult $result): ?string
    {
        if (empty($this->literal)) {
            return null;
        }

        // \n is a special case - it will be interpreted as a string of length 2, so we must
        // check for this explicitly (since splitting \n into two symbols will not work)
        if ($this->literal === '\n' || strlen($this->literal) === 1 || isset($result->getConfigs()['lexer'])) {
            return "['value' => \"{$this->literal}\", 'type' => Symbol::LITERAL]";
        }

        $this->has_rules = true;

        $this->should_wrap = true;

        return $result->unique("{$rule->getName()}\$string");
    }

    /**
     * @param \JPNut\Pearley\Compiler\CompileRule   $rule
     * @param \JPNut\Pearley\Compiler\CompileResult $result
     *
     * @return \JPNut\Pearley\Compiler\CompileRule[]
     */
    public function generateCompileRules(CompileRule $rule, CompileResult $result): array
    {
        $name = $this->serialize($rule, $result);

        if (!$this->has_rules) {
            return [];
        }

        $rules = [];

        $rules = array_merge($rules, $this->createLanguageRule()->toCompileRules($name, $result));

        return $rules;
    }

    /**
     * @return \JPNut\Pearley\Compiler\LanguageRule
     */
    protected function createLanguageRule(): LanguageRule
    {
        return new LanguageRule(
            array_map(fn (string $char) => new static($char), str_split($this->literal)),
            PostProcessor::builtin(PostProcessor::JOINER),
        );
    }
}
