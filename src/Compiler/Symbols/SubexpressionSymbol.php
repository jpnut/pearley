<?php

namespace JPNut\Pearley\Compiler\Symbols;

use JPNut\Pearley\Compiler\CompileResult;
use JPNut\Pearley\Compiler\CompileRule;
use JPNut\Pearley\Compiler\Contracts\HasRules;
use JPNut\Pearley\Compiler\Contracts\Symbol;

class SubexpressionSymbol implements Symbol, HasRules
{
    /**
     * @var \JPNut\Pearley\Compiler\LanguageRule[]
     */
    protected array $rules;

    /**
     * @var bool
     */
    protected bool $is_serialized = false;

    /**
     * @var string|null
     */
    protected ?string $serialized;

    /**
     * @param  array  $rules
     */
    public function __construct(array $rules)
    {
        $this->rules = $rules;
    }

    /**
     * @param  \JPNut\Pearley\Compiler\CompileRule  $rule
     * @param  \JPNut\Pearley\Compiler\CompileResult  $result
     * @return string|null
     */
    public function serialize(CompileRule $rule, CompileResult $result): ?string
    {
        if ($this->is_serialized) {
            return $this->serialized;
        }

        $this->is_serialized = true;

        return $this->serialized = $result->unique("{$rule->getName()}\$subexpression");
    }

    /**
     * @param  \JPNut\Pearley\Compiler\CompileRule  $rule
     * @param  \JPNut\Pearley\Compiler\CompileResult  $result
     * @return \JPNut\Pearley\Compiler\CompileRule[]
     */
    public function generateCompileRules(CompileRule $rule, CompileResult $result): array
    {
        $name = $this->serialize($rule, $result);

        $rules = [];

        foreach ($this->rules as $languageRule) {
            $rules = array_merge(
                $rules,
                $languageRule->toCompileRules($name, $result)
            );
        }

        return $rules;
    }

    /**
     * @return bool
     */
    public function shouldWrap(): bool
    {
        return true;
    }
}