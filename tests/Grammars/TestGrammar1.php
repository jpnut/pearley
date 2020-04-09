<?php

namespace JPNut\Pearley\Tests\Grammars;

use JPNut\Pearley\Parser\Grammar;
use JPNut\Pearley\Parser\Symbol;


/**
 * Class TestGrammar1
 *
 * Generated automatically by pearley
 * https://github.com/jpnut/pearley
 */
class TestGrammar1
{
    public static function grammar(): Grammar
    {
        $id = fn($x) => $x[0];

        

        return new Grammar([
            ['name' => 'y', 'symbols' => ['y$ebnf$1']],
            ['name' => 'y$ebnf$1', 'symbols' => ['x']],
            ['name' => 'y$ebnf$1', 'symbols' => ['y$ebnf$1', 'x'], 'postprocess' => function ($d) {return [...$d[0], $d[1]]; }],
            ['name' => 'x', 'symbols' => [['value' => '[a-z0-9]', 'type' => Symbol::REGEX]]],
            ['name' => 'x', 'symbols' => [['value' => "\n", 'type' => Symbol::LITERAL]]]
        ], 'y');
    }
}
