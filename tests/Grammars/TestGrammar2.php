<?php

namespace JPNut\Pearley\Tests\Grammars;

use JPNut\Pearley\Parser\Grammar;
use JPNut\Pearley\Parser\Symbol;


/**
 * Class TestGrammar2
 *
 * Generated automatically by pearley
 * https://github.com/jpnut/pearley
 */
class TestGrammar2
{
    public static function grammar(): Grammar
    {
        $id = fn($x) => $x[0];

        

        return new Grammar([
            ['name' => 'input', 'symbols' => ['ws', 'a', 'ws']],
            ['name' => 'a', 'symbols' => [['value' => "a", 'type' => Symbol::LITERAL]]],
            ['name' => 'ws', 'symbols' => []],
            ['name' => 'ws', 'symbols' => ['wsc', 'ws']],
            ['name' => 'wsc', 'symbols' => [['value' => " ", 'type' => Symbol::LITERAL]]]
        ], 'input');
    }
}
