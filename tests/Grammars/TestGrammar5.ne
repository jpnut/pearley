@use "JPNut\Pearley\Lexer\TokenDefinition"
@use "JPNut\Pearley\Lexer\Lexer"
@use "JPNut\Pearley\Lexer\LexerConfig"

@{%

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

%}

@lexer lexer

blocks -> blocks ws block {% $joiner %}
    | block {% $id %}

block -> word {% $id %}
    | number {% $id %}

word -> %word {% $getValue %}

number -> %number {% $getValue %}

ws -> %ws {% $getValue %}