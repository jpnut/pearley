<?php

namespace JPNut\Pearley\Compiler\Symbols;

use Exception;
use JPNut\Pearley\Compiler\CompileResult;
use JPNut\Pearley\Compiler\CompileRule;
use JPNut\Pearley\Compiler\Contracts\HasRules;
use JPNut\Pearley\Compiler\Contracts\Symbol;
use JPNut\Pearley\Compiler\LanguageRule;
use JPNut\Pearley\Compiler\PostProcessor;

class EBNFSymbol implements Symbol, HasRules
{
    /**
     * @var string
     */
    protected string $modifier;

    /**
     * @var \JPNut\Pearley\Compiler\Contracts\Symbol
     */
    protected Symbol $symbol;

    /**
     * @var bool
     */
    protected bool $is_serialized = false;

    /**
     * @var string|null
     */
    protected ?string $serialized;

    /**
     * @param  string  $modifier
     * @param  \JPNut\Pearley\Compiler\Contracts\Symbol  $symbol
     */
    public function __construct(string $modifier, Symbol $symbol)
    {
        $this->modifier = $modifier;
        $this->symbol   = $symbol;
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

        return $this->serialized = $result->unique("{$rule->getName()}\$ebnf");
    }

    /**
     * @return bool
     */
    public function shouldWrap(): bool
    {
        return true;
    }

    /**
     * @param  \JPNut\Pearley\Compiler\CompileRule  $rule
     * @param  \JPNut\Pearley\Compiler\CompileResult  $result
     * @return \JPNut\Pearley\Compiler\CompileRule[]
     */
    public function generateCompileRules(CompileRule $rule, CompileResult $result): array
    {
        $name = $this->serialize($rule, $result);

        $languageRules = $this->buildLanguageRules($name);

        $rules = [];

        foreach ($languageRules as $languageRule) {
            $rules = array_merge(
                $rules,
                $languageRule->toCompileRules($name, $result)
            );
        }

        return $rules;
    }

    /**
     * @param  string  $name
     * @return \JPNut\Pearley\Compiler\LanguageRule[]
     */
    protected function buildLanguageRules(string $name): array
    {
        switch ($this->modifier) {
            case ":+":
                return $this->buildEBNFPlus($name);
            case ":*":
                return $this->buildEBNFStar($name);
            case ":?":
                return $this->buildEBNFOpt($name);
        }

        throw new Exception("Unrecognised EBNF token '{$this->modifier}'.");
    }

    /**
     * @param  string  $name
     * @return \JPNut\Pearley\Compiler\LanguageRule[]
     */
    protected function buildEBNFPlus(string $name): array
    {
        return [
            new LanguageRule(
                [$this->symbol],
                null,
            ),
            new LanguageRule(
                [new StringSymbol($name), $this->symbol],
                PostProcessor::builtin(PostProcessor::ARRPUSH),
            )
        ];
    }

    /**
     * @param  string  $name
     * @return \JPNut\Pearley\Compiler\LanguageRule[]
     */
    protected function buildEBNFStar(string $name): array
    {
        return [
            new LanguageRule(
                [],
                null
            ),
            new LanguageRule(
                [new StringSymbol($name), $this->symbol],
                PostProcessor::builtin(PostProcessor::ARRPUSH),
            )
        ];
    }

    /**
     * @param $name
     * @return \JPNut\Pearley\Compiler\LanguageRule[]
     */
    protected function buildEBNFOpt($name): array
    {
        return [
            new LanguageRule(
                [$this->symbol],
                PostProcessor::builtin(PostProcessor::ID),
            ),
            new LanguageRule(
                [],
                PostProcessor::builtin(PostProcessor::NULLER),
            )
        ];
    }
}