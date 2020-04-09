<?php

namespace JPNut\Pearley\Tests\Grammars;

use JPNut\Pearley\Parser\Grammar;
use JPNut\Pearley\Parser\Symbol;


/**
 * Class TestGrammar3
 *
 * Generated automatically by pearley
 * https://github.com/jpnut/pearley
 */
class TestGrammar3
{
    public static function grammar(): Grammar
    {
        $id = fn($x) => $x[0];

        

        return new Grammar([
            ['name' => 'input', 'symbols' => ['ws', 'a', 'ws']],
            ['name' => 'a', 'symbols' => [['value' => "a", 'type' => Symbol::LITERAL]]],
            ['name' => 'ws', 'symbols' => ['ws$ebnf$1']],
            ['name' => 'ws$ebnf$1', 'symbols' => []],
            ['name' => 'ws$ebnf$1', 'symbols' => ['ws$ebnf$1', ['value' => '[ ]', 'type' => Symbol::REGEX]], 'postprocess' => function ($d) {return [...$d[0], $d[1]]; }]
        ], 'input');
    }
}
