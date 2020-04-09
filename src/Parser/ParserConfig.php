<?php

namespace JPNut\Pearley\Parser;

use JPNut\Pearley\Parser\Contracts\Lexer as LexerContract;
use JPNut\Pearley\Parser\Contracts\ParserConfig as ParserConfigContract;

class ParserConfig implements ParserConfigContract
{
    /**
     * @var \JPNut\Pearley\Parser\Grammar
     */
    protected Grammar $grammar;

    /**
     * @var \JPNut\Pearley\Parser\Contracts\Lexer
     */
    protected LexerContract $lexer;

    /**
     * @var bool
     */
    protected bool $keepHistory;

    /**
     * @param \JPNut\Pearley\Parser\Grammar              $grammar
     * @param \JPNut\Pearley\Parser\Contracts\Lexer|null $lexer
     * @param bool                                       $keepHistory
     */
    public function __construct(Grammar $grammar, ?LexerContract $lexer = null, bool $keepHistory = false)
    {
        $this->grammar = $grammar;
        $this->lexer = $lexer ?? $grammar->getLexer() ?? new StreamLexer();
        $this->keepHistory = $keepHistory;
    }

    /**
     * @return \JPNut\Pearley\Parser\Grammar
     */
    public function getGrammar(): Grammar
    {
        return $this->grammar;
    }

    /**
     * @return \JPNut\Pearley\Parser\Contracts\Lexer
     */
    public function getLexer(): LexerContract
    {
        return $this->lexer;
    }

    /**
     * @return bool
     */
    public function shouldKeepHistory(): bool
    {
        return $this->keepHistory;
    }
}
