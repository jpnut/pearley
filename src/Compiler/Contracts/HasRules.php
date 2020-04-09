<?php

namespace JPNut\Pearley\Compiler\Contracts;

use JPNut\Pearley\Compiler\CompileResult;
use JPNut\Pearley\Compiler\CompileRule;

interface HasRules
{
    /**
     * @param \JPNut\Pearley\Compiler\CompileRule   $rule
     * @param \JPNut\Pearley\Compiler\CompileResult $result
     *
     * @return \JPNut\Pearley\Compiler\CompileRule[]
     */
    public function generateCompileRules(CompileRule $rule, CompileResult $result): array;
}
