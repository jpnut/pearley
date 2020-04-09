<?php

namespace JPNut\Pearley\Compiler\Symbols;

use Exception;
use JPNut\Pearley\Compiler\CompileResult;
use JPNut\Pearley\Compiler\CompileRule;
use JPNut\Pearley\Compiler\Contracts\HasRules;
use JPNut\Pearley\Compiler\Contracts\Symbol;

class MacroCallSymbol implements Symbol, HasRules
{
    /**
     * @var string
     */
    protected string $macro;

    /**
     * @var \JPNut\Pearley\Compiler\LanguageRule[]
     */
    protected array $args;

    /**
     * @var bool
     */
    protected bool $is_serialized = false;

    /**
     * @var string|null
     */
    protected ?string $serialized;

    /**
     * @param string $macro
     * @param array  $args
     */
    public function __construct(string $macro, array $args)
    {
        $this->macro = $macro;
        $this->args = $args;
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

        return $this->serialized = $this->uniqueName($rule, $result);
    }

    /**
     * @return bool
     */
    public function shouldWrap(): bool
    {
        return true;
    }

    /**
     * @param \JPNut\Pearley\Compiler\CompileRule   $rule
     * @param \JPNut\Pearley\Compiler\CompileResult $result
     *
     * @return \JPNut\Pearley\Compiler\CompileRule[]
     */
    public function generateCompileRules(CompileRule $rule, CompileResult $result): array
    {
        if (!$result->hasMacro($this->macro)) {
            throw new Exception("Unknown macro: {$this->macro}");
        }

        if (($received = count($this->args)) !== ($expected = count(($macro = $result->getMacro($this->macro))->getArgs()))) {
            throw new Exception(
                "Argument count mismatch for macro {$this->macro}: expected {$expected} but received {$received}."
            );
        }

        $name = $this->serialize($rule, $result);

        $rules = [];

        foreach ($macro->getArgs() as $index => $arg) {
            // The macro map is a simple hashmap of all expressions which include macros.
            // Because there is no guarantee that macros will use different variable names,
            // we must namespace these variables in order to make them apply uniquely to
            // the relevant expression. i.e. given foo[X] and bar[X], we have to make sure
            // that any usages of $X within the macro expression specifically refer back to
            // the X for foo, or the X for bar.
            $result->addMacroMap("{$rule->getName()}-{$arg}", $argName = $this->uniqueName($rule, $result));

            $rules = array_merge(
                $rules,
                $this->args[$index]->toCompileRules($argName, $result)
            );
        }

        foreach ($macro->getRules() as $macroRule) {
            $rules = array_merge(
                $rules,
                $macroRule->toCompileRules($name, $result)
            );
        }

        return $rules;
    }

    /**
     * @param \JPNut\Pearley\Compiler\CompileRule   $rule
     * @param \JPNut\Pearley\Compiler\CompileResult $result
     *
     * @return string
     */
    protected function uniqueName(CompileRule $rule, CompileResult $result): string
    {
        return $result->unique("{$rule->getName()}\$macrocall");
    }
}
