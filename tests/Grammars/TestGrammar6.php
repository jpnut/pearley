<?php

namespace JPNut\Pearley\Tests\Grammars;

use JPNut\Pearley\Parser\Grammar;
use JPNut\Pearley\Parser\Symbol;
use JPNut\Pearley\Lexer\TokenDefinition;
use JPNut\Pearley\Lexer\Lexer;
use JPNut\Pearley\Lexer\LexerConfig;

/**
 * Class TestGrammar6
 *
 * Generated automatically by pearley
 * https://github.com/jpnut/pearley
 */
class TestGrammar6
{
    public static function grammar(): Grammar
    {
        $id = fn($x) => $x[0];

        $nth = fn ($n) => fn ($d) => $d[$n];
        
        $rules = [
            TokenDefinition::initialise('ws', '\s+')
                ->withLineBreaks()
                ->create(),
            TokenDefinition::initialise('number', '\d+')
                ->create(),
            TokenDefinition::initialise('word', '[a-zA-Z_]+')
                ->create(),
        ];
        
        $lexer = new Lexer(
            new LexerConfig(
                'main',
                $rules,
            ),
        );
        
        $joiner = fn($d) => join('', $d);
        
        $getValue = fn($d) => $d[0]->getValue();

        return new Grammar([
            ['name' => 'blocks', 'symbols' => ['blocks', 'ws', 'block'], 'postprocess' => $joiner],
            ['name' => 'blocks', 'symbols' => ['block'], 'postprocess' => $id],
            ['name' => 'block', 'symbols' => ['word'], 'postprocess' => $id],
            ['name' => 'block', 'symbols' => ['number'], 'postprocess' => $id],
            ['name' => 'bracketed_word', 'symbols' => ['bracketed_word$macrocall$1'], 'postprocess' => $id],
            ['name' => 'bracketed_word$macrocall$2', 'symbols' => ['word']],
            ['name' => 'bracketed_word$macrocall$1', 'symbols' => [['value' => "(", 'type' => Symbol::LITERAL], 'bracketed_word$macrocall$2', ['value' => ")", 'type' => Symbol::LITERAL]]],
            ['name' => 'word', 'symbols' => [['value' => 'word', 'type' => Symbol::TOKEN]], 'postprocess' => $getValue],
            ['name' => 'number', 'symbols' => [['value' => 'number', 'type' => Symbol::TOKEN]], 'postprocess' => $getValue],
            ['name' => 'ws', 'symbols' => [['value' => 'ws', 'type' => Symbol::TOKEN]], 'postprocess' => $getValue]
        ], 'blocks', $lexer);
    }
}
