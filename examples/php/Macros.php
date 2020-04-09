<?php

namespace JPNut\Pearley\Examples;

use JPNut\Pearley\Parser\Grammar;
use JPNut\Pearley\Parser\Symbol;


/**
 * Class Macros
 *
 * Generated automatically by pearley
 * https://github.com/jpnut/pearley
 */
class Macros
{
    public static function grammar(): Grammar
    {
        $id = fn($x) => $x[0];

        

        return new Grammar([
            ['name' => 'main', 'symbols' => ['main$macrocall$1'], 'postprocess' => fn($d) => "{$d[0][0][0][0]} {$d[0][0][2][0][0]}{$d[0][1][0][0]->getValue()}"],
            ['name' => 'main$macrocall$2', 'symbols' => ['main$macrocall$2$string$1']],
            ['name' => 'main$macrocall$2$string$1', 'symbols' => [['value' => "C", 'type' => Symbol::LITERAL], ['value' => "o", 'type' => Symbol::LITERAL], ['value' => "w", 'type' => Symbol::LITERAL], ['value' => "s", 'type' => Symbol::LITERAL]], 'postprocess' => function ($d) { return join('', $d); }],
            ['name' => 'main$macrocall$3', 'symbols' => ['main$macrocall$3$subexpression$1']],
            ['name' => 'main$macrocall$3$subexpression$1', 'symbols' => [['value' => ".", 'type' => Symbol::LITERAL]]],
            ['name' => 'main$macrocall$3$subexpression$1', 'symbols' => [['value' => "!", 'type' => Symbol::LITERAL]]],
            ['name' => 'main$macrocall$1', 'symbols' => ['main$macrocall$1$macrocall$1', 'main$macrocall$3']],
            ['name' => 'main$macrocall$1$macrocall$2', 'symbols' => ['main$macrocall$1$macrocall$2$subexpression$1']],
            ['name' => 'main$macrocall$1$macrocall$2$subexpression$1', 'symbols' => ['main$macrocall$1$macrocall$2$subexpression$1$string$1']],
            ['name' => 'main$macrocall$1$macrocall$2$subexpression$1$string$1', 'symbols' => [['value' => "m", 'type' => Symbol::LITERAL], ['value' => "o", 'type' => Symbol::LITERAL], ['value' => "o", 'type' => Symbol::LITERAL]], 'postprocess' => function ($d) { return join('', $d); }],
            ['name' => 'main$macrocall$1$macrocall$2$subexpression$1', 'symbols' => ['main$macrocall$1$macrocall$2$subexpression$1$string$2']],
            ['name' => 'main$macrocall$1$macrocall$2$subexpression$1$string$2', 'symbols' => [['value' => "o", 'type' => Symbol::LITERAL], ['value' => "i", 'type' => Symbol::LITERAL], ['value' => "n", 'type' => Symbol::LITERAL], ['value' => "k", 'type' => Symbol::LITERAL]], 'postprocess' => function ($d) { return join('', $d); }],
            ['name' => 'main$macrocall$1$macrocall$2$subexpression$1', 'symbols' => ['main$macrocall$1$macrocall$2$subexpression$1$string$3']],
            ['name' => 'main$macrocall$1$macrocall$2$subexpression$1$string$3', 'symbols' => [['value' => "b", 'type' => Symbol::LITERAL], ['value' => "a", 'type' => Symbol::LITERAL], ['value' => "a", 'type' => Symbol::LITERAL]], 'postprocess' => function ($d) { return join('', $d); }],
            ['name' => 'main$macrocall$1$macrocall$1', 'symbols' => ['main$macrocall$2', ['value' => " ", 'type' => Symbol::LITERAL], 'main$macrocall$1$macrocall$2']]
        ], 'main');
    }
}
