<?php

namespace JPNut\Pearley\Compiler;

use JPNut\Pearley\Compiler\Components\BuiltinComponent;
use JPNut\Pearley\Compiler\Components\ConfigComponent;
use JPNut\Pearley\Compiler\Components\ContentComponent;
use JPNut\Pearley\Compiler\Components\ExpressionComponent;
use JPNut\Pearley\Compiler\Components\IncludeComponent;
use JPNut\Pearley\Compiler\Components\MacroComponent;
use JPNut\Pearley\Compiler\Components\UseComponent;
use JPNut\Pearley\Compiler\Symbols\EBNFSymbol;
use JPNut\Pearley\Compiler\Symbols\LiteralSymbol;
use JPNut\Pearley\Compiler\Symbols\MacroCallSymbol;
use JPNut\Pearley\Compiler\Symbols\MixinSymbol;
use JPNut\Pearley\Compiler\Symbols\RegexSymbol;
use JPNut\Pearley\Compiler\Symbols\StringSymbol;
use JPNut\Pearley\Compiler\Symbols\SubexpressionSymbol;
use JPNut\Pearley\Compiler\Symbols\TokenSymbol;
use JPNut\Pearley\Lexer\Lexer;
use JPNut\Pearley\Lexer\LexerConfig;
use JPNut\Pearley\Lexer\TokenDefinition;
use JPNut\Pearley\Parser\Grammar;
use JPNut\Pearley\Parser\Parser;
use JPNut\Pearley\Parser\ParserConfig;
use JPNut\Pearley\Parser\RegExp;
use JPNut\Pearley\Parser\Symbol;

class PearleyGrammar
{
    public static function parser()
    {
        return new Parser(new ParserConfig(static::grammar()));
    }

    public static function grammar()
    {
        $id = fn($x) => $x[0] ?? null;

        $insensitive = function ($s) {
            $result = [];
            $length = strlen($s);

            for ($i = 0; $i < $length; $i++) {
                $c     = $s[$i];
                $upper = strtoupper($c);
                $lower = strtolower($c);

                if ($upper !== $c || $lower !== $c) {
                    $result[] = new RegexSymbol(new RegExp("[{$lower}{$upper}]"));
                } else {
                    $result[] = new LiteralSymbol($c);
                }
            }

            return new SubexpressionSymbol([
                new LanguageRule(
                    $result,
                    PostProcessor::builtin(PostProcessor::JOINER),
                )
            ]);
        };

        $getValue = fn($d) => $d[0]->getValue();

        return new Grammar([
            [
                'name'        => 'final$ebnf$1',
                'symbols'     => [['value' => 'ws', 'type' => Symbol::TOKEN]],
                'postprocess' => $id
            ],
            [
                'name'        => 'final$ebnf$1',
                'symbols'     => [],
                'postprocess' => fn() => null,
            ],
            [
                'name'        => 'final',
                'symbols'     => ['_', 'prog', '_', 'final$ebnf$1'],
                'postprocess' => function ($d) {
                    return $d[1];
                }
            ],
            [
                'name'        => 'prog',
                'symbols'     => ['prod'],
                'postprocess' => function ($d) {
                    return [$d[0]];
                }
            ],
            [
                'name'        => 'prog',
                'symbols'     => ['prod', 'ws', 'prog'],
                'postprocess' => function ($d) {
                    return array_merge([$d[0]], $d[2]);
                }
            ],
            [
                'name'        => 'prod',
                'symbols'     => ['word', '_', ['value' => 'arrow', 'type' => Symbol::TOKEN], '_', 'expression+'],
                'postprocess' => function ($d) {
                    return new ExpressionComponent($d[0], $d[4]);
                }
            ],
            [
                'name'        => 'prod',
                'symbols'     => [
                    'word',
                    ['value' => '[', 'type' => Symbol::LITERAL],
                    'wordlist',
                    ['value' => ']', 'type' => Symbol::LITERAL],
                    '_',
                    ['value' => 'arrow', 'type' => Symbol::TOKEN],
                    '_',
                    'expression+'
                ],
                'postprocess' => function ($d) {
                    return new MacroComponent($d[0], $d[2], $d[7]);
                }
            ],
            [
                'name'        => 'prod',
                'symbols'     => [['value' => '@', 'type' => Symbol::LITERAL], '_', 'php'],
                'postprocess' => function ($d) {
                    return new ContentComponent($d[2]);
                }
            ],
            [
                'name'        => 'prod',
                'symbols'     => [['value' => '@', 'type' => Symbol::LITERAL], 'word', 'ws', 'word'],
                'postprocess' => function ($d) {
                    return new ConfigComponent($d[1], $d[3]);
                }
            ],
            [
                'name'        => 'prod',
                'symbols'     => [['value' => '@include', 'type' => Symbol::LITERAL], '_', 'string'],
                'postprocess' => function ($d) {
                    return new IncludeComponent($d[2]);
                }
            ],
            [
                'name'        => 'prod',
                'symbols'     => [['value' => '@builtin', 'type' => Symbol::LITERAL], '_', 'string'],
                'postprocess' => function ($d) {
                    return new BuiltinComponent($d[2]);
                }
            ],
            [
                'name'        => 'prod',
                'symbols'     => [['value' => '@use', 'type' => Symbol::LITERAL], '_', 'string'],
                'postprocess' => function ($d) {
                    return new UseComponent($d[2]);
                }
            ],
            [
                'name'    => 'expression+',
                'symbols' => ['completeexpression']
            ],
            [
                'name'        => 'expression+',
                'symbols'     => [
                    'expression+',
                    '_',
                    ['value' => '|', 'type' => Symbol::LITERAL],
                    '_',
                    'completeexpression'
                ],
                'postprocess' => function ($d) {
                    return [...$d[0], $d[4]];
                }
            ],
            [
                'name'    => 'expressionlist',
                'symbols' => ['completeexpression']
            ],
            [
                'name'        => 'expressionlist',
                'symbols'     => [
                    'expressionlist',
                    '_',
                    ['value' => ',', 'type' => Symbol::LITERAL],
                    '_',
                    'completeexpression'
                ],
                'postprocess' => function ($d) {
                    return [...$d[0], $d[4]];
                }
            ],
            [
                'name'    => 'wordlist',
                'symbols' => ['word']
            ],
            [
                'name'        => 'wordlist',
                'symbols'     => ['wordlist', '_', ['value' => ',', 'type' => Symbol::LITERAL], '_', 'word'],
                'postprocess' => function ($d) {
                    return [...$d[0], $d[4]];
                }
            ],
            [
                'name'        => 'completeexpression',
                'symbols'     => ['expr'],
                'postprocess' => function ($d) {
                    return new LanguageRule($d[0]);
                }
            ],
            [
                'name'        => 'completeexpression',
                'symbols'     => ['expr', '_', 'php'],
                'postprocess' => function ($d) {
                    return new LanguageRule($d[0], new PostProcessor($d[2]));
                }
            ],
            [
                'name'        => 'expr_member',
                'symbols'     => ['word'],
                'postprocess' => function ($d) {
                    return new StringSymbol($d[0]);
                }
            ],
            [
                'name'        => 'expr_member',
                'symbols'     => [['value' => '$', 'type' => Symbol::LITERAL], 'word'],
                'postprocess' => function ($d) {
                    return new MixinSymbol($d[1]);
                }
            ],
            [
                'name'        => 'expr_member',
                'symbols'     => [
                    'word',
                    ['value' => '[', 'type' => Symbol::LITERAL],
                    'expressionlist',
                    ['value' => ']', 'type' => Symbol::LITERAL]
                ],
                'postprocess' => function ($d) {
                    return new MacroCallSymbol($d[0], $d[2]);
                }
            ],
            [
                'name'        => 'expr_member$ebnf$1',
                'symbols'     => [['value' => 'i', 'type' => Symbol::LITERAL]],
                'postprocess' => $id
            ],
            [
                'name'        => 'expr_member$ebnf$1',
                'symbols'     => [],
                'postprocess' => function () {
                    return null;
                }
            ],
            [
                'name'        => 'expr_member',
                'symbols'     => ['string', 'expr_member$ebnf$1'],
                'postprocess' => function ($d) use ($insensitive) {
                    return is_null($d[1])
                        ? new LiteralSymbol($d[0])
                        : $insensitive($d[0]);
                }
            ],
            [
                'name'        => 'expr_member',
                'symbols'     => [['value' => '%', 'type' => Symbol::LITERAL], 'word'],
                'postprocess' => function ($d) {
                    return new TokenSymbol($d[1]);
                }
            ],
            [
                'name'        => 'expr_member',
                'symbols'     => ['charclass'],
                'postprocess' => $id
            ],
            [
                'name'        => 'expr_member',
                'symbols'     => [
                    ['value' => '(', 'type' => Symbol::LITERAL],
                    '_',
                    'expression+',
                    '_',
                    ['value' => ')', 'type' => Symbol::LITERAL]
                ],
                'postprocess' => function ($d) {
                    return new SubexpressionSymbol($d[2]);
                }
            ],
            [
                'name'        => 'expr_member',
                'symbols'     => ['expr_member', '_', 'ebnf_modifier'],
                'postprocess' => function ($d) {
                    return new EBNFSymbol($d[2], $d[0]);
                }
            ],
            [
                'name'        => 'ebnf_modifier',
                'symbols'     => [['value' => ':+', 'type' => Symbol::LITERAL]],
                'postprocess' => $getValue
            ],
            [
                'name'        => 'ebnf_modifier',
                'symbols'     => [['value' => ':*', 'type' => Symbol::LITERAL]],
                'postprocess' => $getValue
            ],
            [
                'name'        => 'ebnf_modifier',
                'symbols'     => [['value' => ':?', 'type' => Symbol::LITERAL]],
                'postprocess' => $getValue
            ],
            [
                'name'    => 'expr',
                'symbols' => ['expr_member']
            ],
            [
                'name'        => 'expr',
                'symbols'     => ['expr', 'ws', 'expr_member'],
                'postprocess' => function ($d) {
                    return [...$d[0], $d[2]];
                }
            ],
            [
                'name'        => 'word',
                'symbols'     => [['value' => 'word', 'type' => Symbol::TOKEN]],
                'postprocess' => $getValue
            ],
            [
                'name'        => 'string',
                'symbols'     => [['value' => 'string', 'type' => Symbol::TOKEN]],
                'postprocess' => $getValue
            ],
            [
                'name'        => 'string',
                'symbols'     => [['value' => 'btstring', 'type' => Symbol::TOKEN]],
                'postprocess' => $getValue
            ],
            [
                'name'        => 'charclass',
                'symbols'     => [['value' => 'charclass', 'type' => Symbol::TOKEN]],
                'postprocess' => function ($d) {
                    return new RegexSymbol($d[0]->getValue());
                }
            ],
            [
                'name'        => 'php',
                'symbols'     => [['value' => 'php', 'type' => Symbol::TOKEN]],
                'postprocess' => $getValue
            ],
            [
                'name'        => '_$ebnf$1',
                'symbols'     => ['ws'],
                'postprocess' => $id
            ],
            [
                'name'        => '_$ebnf$1',
                'symbols'     => [],
                'postprocess' => function () {
                    return null;
                }
            ],
            [
                'name'    => '_',
                'symbols' => ['_$ebnf$1']
            ],
            [
                'name'    => 'ws',
                'symbols' => [['value' => 'ws', 'type' => Symbol::TOKEN]]
            ],
            [
                'name'        => 'ws$ebnf$1',
                'symbols'     => [['value' => 'ws', 'type' => Symbol::TOKEN]],
                'postprocess' => $id
            ],
            [
                'name'        => 'ws$ebnf$1',
                'symbols'     => [],
                'postprocess' => function () {
                    return null;
                }
            ],
            [
                'name'    => 'ws',
                'symbols' => ['ws$ebnf$1', ['value' => 'comment', 'type' => Symbol::TOKEN], '_']
            ],
        ], 'final', static::lexer());
    }

    public static function lexer()
    {
        return new Lexer(
            new LexerConfig(
                'main',
                array_merge(
                    $rules = static::nearleyRules(),
                    [
                        TokenDefinition::initialise('charclass', '\.|\[(?:\\\.|[^\\\n])+?\]')
                            ->withValueMap(fn(string $text) => new RegExp($text))
                            ->create(),
                    ]
                )
            ),
            new LexerConfig(
                'afterWord',
                array_merge(
                    $rules,
                    [
                        TokenDefinition::initialise('[', '\[')
                            ->withNext('main')
                            ->create(),
                    ]
                )
            )
        );
    }


    protected static function nearleyRules()
    {
        return array_merge([
            TokenDefinition::initialise('ws', '\s+')
                ->withLineBreaks()
                ->withNext('main')
                ->create(),
            'comment' => '\#.*',
            TokenDefinition::initialise('arrow', '[=-]+\>')
                ->withNext('main')
                ->create(),
            TokenDefinition::initialise('php', '\{\%(?:[^%]|\%[^}])*\%\}')
                ->withValueMap(fn(string $text) => substr($text, 2, -2))
                ->create(),
            TokenDefinition::initialise('word', '[\w\?\+]+')
                ->withNext('afterWord')
                ->create(),
            TokenDefinition::initialise('string', '"(?:[^\\"\n]|\\["\\/bfnrt]|\\u[a-fA-F0-9]{4})*"')
                ->withValueMap(fn(string $text) => preg_replace(
                    '/(\")((?:[^\"\n]|\\["\\/bfnrt]|u[a-fA-F0-9]{4})*)(\")/', '$2', $text)
                )
                ->withNext('main')
                ->create(),
            TokenDefinition::initialise('btstring', '\`[^`]*\`')
                ->withValueMap(fn(string $text) => substr($text, 1, -1))
                ->withNext('main')
                ->create(),

        ], static::nearleyLiterals());
    }

    protected static function nearleyLiterals()
    {
        $tokens = [
            ","        => '\,',
            "|"        => "\|",
            "$"        => "\\$",
            "%"        => "\%",
            "("        => "\(",
            ")"        => "\)",
            ":?"       => "\:\?",
            ":*"       => "\:\*",
            ":+"       => "\:\+",
            "@include" => "\@include",
            "@builtin" => "\@builtin",
            "@use"     => "\@use",
            "@"        => "\@",
            "]"        => "\]",
        ];

        $literals = [];

        foreach ($tokens as $key => $literal) {
            $literals[$key] = TokenDefinition::initialise($key, $literal)
                ->withNext('main')
                ->create();
        }

        return $literals;
    }
}
