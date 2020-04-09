<?php

namespace JPNut\Pearley\Lexer\Contracts;

use JPNut\Pearley\Parser\Contracts\Token as BaseToken;

interface Token extends BaseToken
{
    /**
     * @return \JPNut\Pearley\Lexer\Contracts\TokenDefinition
     */
    public function getDefinition(): TokenDefinition;
}
