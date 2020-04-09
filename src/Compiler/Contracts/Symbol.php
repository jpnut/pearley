<?php

namespace JPNut\Pearley\Compiler\Contracts;

use JPNut\Pearley\Compiler\CompileResult;
use JPNut\Pearley\Compiler\CompileRule;

interface Symbol
{
    /**
     * @param \JPNut\Pearley\Compiler\CompileRule   $rule
     * @param \JPNut\Pearley\Compiler\CompileResult $result
     *
     * @return string|null
     */
    public function serialize(CompileRule $rule, CompileResult $result): ?string;

    /**
     * @return bool
     */
    public function shouldWrap(): bool;
}
