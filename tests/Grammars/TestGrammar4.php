<?php

namespace JPNut\Pearley\Tests\Grammars;

use JPNut\Pearley\Parser\Grammar;
use JPNut\Pearley\Parser\Symbol;


/**
 * Class TestGrammar4
 *
 * Generated automatically by pearley
 * https://github.com/jpnut/pearley
 */
class TestGrammar4
{
    public static function grammar(): Grammar
    {
        $id = fn($x) => $x[0];

        $joiner = fn ($d) => join('', $d);

        return new Grammar([
            ['name' => 'blocks', 'symbols' => ['blocks', 'ws', 'block'], 'postprocess' => $joiner],
            ['name' => 'blocks', 'symbols' => ['block'], 'postprocess' => $id],
            ['name' => 'block', 'symbols' => ['word'], 'postprocess' => $id],
            ['name' => 'block', 'symbols' => ['number'], 'postprocess' => $id],
            ['name' => 'word', 'symbols' => ['word$ebnf$1'], 'postprocess' => fn ($d) => $joiner(array_map($joiner, $d))],
            ['name' => 'word$ebnf$1', 'symbols' => [['value' => '[a-zA-Z]', 'type' => Symbol::REGEX]]],
            ['name' => 'word$ebnf$1', 'symbols' => ['word$ebnf$1', ['value' => '[a-zA-Z]', 'type' => Symbol::REGEX]], 'postprocess' => function ($d) {return [...$d[0], $d[1]]; }],
            ['name' => 'number', 'symbols' => ['number$ebnf$1'], 'postprocess' => fn ($d) => $joiner(array_map($joiner, $d))],
            ['name' => 'number$ebnf$1', 'symbols' => [['value' => '[0-9]', 'type' => Symbol::REGEX]]],
            ['name' => 'number$ebnf$1', 'symbols' => ['number$ebnf$1', ['value' => '[0-9]', 'type' => Symbol::REGEX]], 'postprocess' => function ($d) {return [...$d[0], $d[1]]; }],
            ['name' => 'ws', 'symbols' => ['ws$ebnf$1'], 'postprocess' => fn ($d) => " "],
            ['name' => 'ws$ebnf$1', 'symbols' => [['value' => '[\s]', 'type' => Symbol::REGEX]]],
            ['name' => 'ws$ebnf$1', 'symbols' => ['ws$ebnf$1', ['value' => '[\s]', 'type' => Symbol::REGEX]], 'postprocess' => function ($d) {return [...$d[0], $d[1]]; }]
        ], 'blocks');
    }
}
