<?php

namespace JPNut\Pearley\Parser\Contracts;

use JPNut\Pearley\Parser\Grammar;

interface ParserConfig
{
    /**
     * @return \JPNut\Pearley\Parser\Grammar
     */
    public function getGrammar(): Grammar;

    /**
     * @return \JPNut\Pearley\Parser\Contracts\Lexer
     */
    public function getLexer(): Lexer;

    /**
     * @return bool
     */
    public function shouldKeepHistory(): bool;
}
