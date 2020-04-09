<?php

namespace JPNut\Pearley\Parser;

use Closure;
use InvalidArgumentException;
use JPNut\Pearley\Parser\Contracts\Lexer as LexerContract;

class Grammar
{
    /**
     * @var \JPNut\Pearley\Parser\Rule[]
     */
    protected array $rules;

    /**
     * @var string
     */
    protected string $start;

    /**
     * @var \JPNut\Pearley\Parser\Rule[][]
     */
    protected array $byName;

    /**
     * @var \JPNut\Pearley\Parser\Contracts\Lexer|null
     */
    protected ?LexerContract $lexer;

    /**
     * @param array                                 $rules
     * @param string|null                           $start
     * @param \JPNut\Pearley\Parser\Contracts\Lexer $lexer
     */
    public function __construct(array $rules, ?string $start = null, ?LexerContract $lexer = null)
    {
        $this->rules = $this->parseRules($rules);
        $this->start = $start ?? $this->rules[0]->getName();
        $this->lexer = $lexer;

        $this->byName = [];

        foreach ($this->rules as $rule) {
            if (!array_key_exists(($name = $rule->getName()), $this->byName)) {
                $this->byName[$name] = [];
            }

            $this->byName[$name][] = $rule;
        }
    }

    /**
     * @return string
     */
    public function getStart(): string
    {
        return $this->start;
    }

    /**
     * @return \JPNut\Pearley\Parser\Contracts\Lexer|null
     */
    public function getLexer(): ?LexerContract
    {
        return $this->lexer;
    }

    /**
     * @param string $name
     *
     * @return \JPNut\Pearley\Parser\Rule[]
     */
    public function getRulesByName(string $name): array
    {
        return $this->byName[$name] ?? [];
    }

    /**
     * @param array $rules
     *
     * @return array
     */
    protected function parseRules(array $rules): array
    {
        return array_map(fn ($rule) => $this->createRule($rule), $rules);
    }

    /**
     * @param mixed $rule
     *
     * @return \JPNut\Pearley\Parser\Rule
     */
    protected function createRule($rule): Rule
    {
        if ($rule instanceof Rule) {
            return $rule;
        }

        if (is_array($rule)) {
            return $this->createRuleFromArray($rule);
        }

        throw new InvalidArgumentException(
            'Invalid rule provided: rule must be array or instance of '.Rule::class
        );
    }

    /**
     * @param array $rule
     *
     * @return \JPNut\Pearley\Parser\Rule
     */
    protected function createRuleFromArray(array $rule): Rule
    {
        if (!isset($rule['name'])) {
            throw new InvalidArgumentException('Rule name is required');
        }

        if (!is_string($rule['name'])) {
            throw new InvalidArgumentException('Rule name must be a string');
        }

        if (isset($rule['symbols']) && !is_null($rule['symbols']) && !is_array($rule['symbols'])) {
            throw new InvalidArgumentException('Rule symbols must be either: an array or null');
        }

        if (isset($rule['postprocess']) && !is_null($rule['postprocess']) && !$rule['postprocess'] instanceof Closure) {
            throw new InvalidArgumentException('Rule postprocess must be either: a closure or null');
        }

        return new Rule(
            $rule['name'],
            $rule['symbols'] ?? [],
            $rule['postprocess'] ?? null
        );
    }
}
