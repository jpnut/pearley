<?php

namespace JPNut\Pearley\Examples;

use JPNut\Pearley\Parser\Grammar;
use JPNut\Pearley\Parser\Symbol;
use JPNut\Pearley\Lexer\TokenDefinition;
use JPNut\Pearley\Lexer\Lexer;
use JPNut\Pearley\Lexer\LexerConfig;

/**
 * Class Json
 *
 * Generated automatically by pearley
 * https://github.com/jpnut/pearley
 */
class Json
{
    public static function grammar(): Grammar
    {
        $id = fn($x) => $x[0];

        $tokens = [
            "\{"        => "\{",
            "\}"        => "\}",
            "\["        => "\[",
            "\]"        => "\]",
            "\,"        => "\,",
            "\)"        => "\)",
            "\:"        => "\:",
            "true"     => "true",
            "false"    => "false",
            "null"     => "null",
        ];
        
        $literals = [];
        
        foreach ($tokens as $key => $literal) {
            $literals[$key] = TokenDefinition::initialise($key, $literal)
                ->create();
        }
        
        $rules = array_merge([
            TokenDefinition::initialise('space', '\s+')
                ->withLineBreaks()
                ->create(),
            TokenDefinition::initialise('number', '-?(?:[0-9]|[1-9][0-9]+)(?:\.[0-9]+)?(?:[eE][-+]?[0-9]+)?\b')
                ->create(),
            TokenDefinition::initialise('string', '"(?:\\\["bfnrt\/\\\]|\\\u[a-fA-F0-9]{4}|[^"\\\])*"')
                ->create(),
        ], $literals);
        
        $lexer = new Lexer(
            new LexerConfig(
                'main',
                $rules,
            ),
        );
        
        $extractPair = function ($kv, &$output) {
            if($kv[0]) { $output[$kv[0]] = $kv[1]; }
        };
        
        $extractObject = function ($d) use ($extractPair) {
            $output = [];
        
            $extractPair($d[2], $output);
        
            foreach ($d[3] as $i) {
                $extractPair($d[3][$i][3], $output);
            }
        
            return $output;
        };
        
        $extractArray = function ($d) {
            $output = [$d[2]];
        
            foreach ($d[3] as $i) {
                $output[] = $d[3][$i][3];
            }
        
            return $output;
        };

        return new Grammar([
            ['name' => 'json', 'symbols' => ['_', 'json$subexpression$1', '_'], 'postprocess' => fn($d) => $d[1][0]],
            ['name' => 'json$subexpression$1', 'symbols' => ['object']],
            ['name' => 'json$subexpression$1', 'symbols' => ['array']],
            ['name' => 'object', 'symbols' => [['value' => "{", 'type' => Symbol::LITERAL], '_', ['value' => "}", 'type' => Symbol::LITERAL]], 'postprocess' => fn($d) => []],
            ['name' => 'object', 'symbols' => [['value' => "{", 'type' => Symbol::LITERAL], '_', 'pair', 'object$ebnf$1', '_', ['value' => "}", 'type' => Symbol::LITERAL]], 'postprocess' => $extractObject],
            ['name' => 'object$ebnf$1', 'symbols' => []],
            ['name' => 'object$ebnf$1', 'symbols' => ['object$ebnf$1', 'object$ebnf$1$subexpression$1'], 'postprocess' => function ($d) {return [...$d[0], $d[1]]; }],
            ['name' => 'object$ebnf$1$subexpression$1', 'symbols' => ['_', ['value' => ",", 'type' => Symbol::LITERAL], '_', 'pair']],
            ['name' => 'array', 'symbols' => [['value' => "[", 'type' => Symbol::LITERAL], '_', ['value' => "]", 'type' => Symbol::LITERAL]], 'postprocess' => fn($d) => []],
            ['name' => 'array', 'symbols' => [['value' => "[", 'type' => Symbol::LITERAL], '_', 'value', 'array$ebnf$1', '_', ['value' => "]", 'type' => Symbol::LITERAL]], 'postprocess' => $extractArray],
            ['name' => 'array$ebnf$1', 'symbols' => []],
            ['name' => 'array$ebnf$1', 'symbols' => ['array$ebnf$1', 'array$ebnf$1$subexpression$1'], 'postprocess' => function ($d) {return [...$d[0], $d[1]]; }],
            ['name' => 'array$ebnf$1$subexpression$1', 'symbols' => ['_', ['value' => ",", 'type' => Symbol::LITERAL], '_', 'value']],
            ['name' => 'value', 'symbols' => ['object'], 'postprocess' => $id],
            ['name' => 'value', 'symbols' => ['array'], 'postprocess' => $id],
            ['name' => 'value', 'symbols' => ['number'], 'postprocess' => $id],
            ['name' => 'value', 'symbols' => ['string'], 'postprocess' => $id],
            ['name' => 'value', 'symbols' => [['value' => "true", 'type' => Symbol::LITERAL]], 'postprocess' => fn($d) => true],
            ['name' => 'value', 'symbols' => [['value' => "false", 'type' => Symbol::LITERAL]], 'postprocess' => fn($d) => false],
            ['name' => 'value', 'symbols' => [['value' => "null", 'type' => Symbol::LITERAL]], 'postprocess' => fn($d) => null],
            ['name' => 'number', 'symbols' => [['value' => 'number', 'type' => Symbol::TOKEN]], 'postprocess' => fn($d) => floatval($d[0]->getValue())],
            ['name' => 'string', 'symbols' => [['value' => 'string', 'type' => Symbol::TOKEN]], 'postprocess' => fn($d) => json_decode($d[0]->getValue())],
            ['name' => 'pair', 'symbols' => ['key', '_', ['value' => ":", 'type' => Symbol::LITERAL], '_', 'value'], 'postprocess' => fn($d) => [$d[0], $d[4]]],
            ['name' => 'key', 'symbols' => ['string'], 'postprocess' => $id],
            ['name' => '_', 'symbols' => []],
            ['name' => '_', 'symbols' => [['value' => 'space', 'type' => Symbol::TOKEN]], 'postprocess' => fn($d) => null]
        ], 'json', $lexer);
    }
}
