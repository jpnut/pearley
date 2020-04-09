@use "JPNut\Pearley\Compiler\LanguageRule"
@use "JPNut\Pearley\Compiler\PostProcessor"
@use "JPNut\Pearley\Compiler\Components\BuiltinComponent"
@use "JPNut\Pearley\Compiler\Components\ConfigComponent"
@use "JPNut\Pearley\Compiler\Components\ContentComponent"
@use "JPNut\Pearley\Compiler\Components\ExpressionComponent"
@use "JPNut\Pearley\Compiler\Components\IncludeComponent"
@use "JPNut\Pearley\Compiler\Components\MacroComponent"
@use "JPNut\Pearley\Compiler\Components\UseComponent"
@use "JPNut\Pearley\Compiler\Symbols\EBNFSymbol"
@use "JPNut\Pearley\Compiler\Symbols\LiteralSymbol"
@use "JPNut\Pearley\Compiler\Symbols\MacroCallSymbol"
@use "JPNut\Pearley\Compiler\Symbols\MixinSymbol"
@use "JPNut\Pearley\Compiler\Symbols\RegexSymbol"
@use "JPNut\Pearley\Compiler\Symbols\StringSymbol"
@use "JPNut\Pearley\Compiler\Symbols\SubexpressionSymbol"
@use "JPNut\Pearley\Compiler\Symbols\TokenSymbol"
@use "JPNut\Pearley\Lexer\Lexer"
@use "JPNut\Pearley\Lexer\LexerConfig"
@use "JPNut\Pearley\Lexer\TokenDefinition"
@use "JPNut\Pearley\Parser\RegExp"

# nearley grammar
@{%
$literals = function () {
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
};

$rules = array_merge([
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

 ], $literals());

$lexer = new Lexer(
     new LexerConfig(
         'main',
         array_merge(
             $rules,
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
%}
@lexer lexer

final -> _ prog _ %ws:?  {% fn($d) => $d[1] %}

prog -> prod  {% fn($d) => [$d[0]] %}
      | prod ws prog  {% fn($d) => array_merge([$d[0]], $d[2]) %}

prod -> word _ %arrow _ expression+  {% fn($d) => new ExpressionComponent($d[0], $d[4]) %}
      | word "[" wordlist "]" _ %arrow _ expression+ {% fn($d) => new MacroComponent($d[0], $d[2], $d[7]) %}
      | "@" _ js  {% fn($d) => new ContentComponent($d[2]) %}
      | "@" word ws word  {% fn($d) => new ConfigComponent($d[1], $d[3]) %}
      | "@include"  _ string {% fn($d) => new IncludeComponent($d[2]) %}
      | "@builtin"  _ string {% fn($d) => new BuiltinComponent($d[2]) %}
      | "@use" _ string {% fn($d) => new UseComponent($d[2]) %}

expression+ -> completeexpression
             | expression+ _ "|" _ completeexpression  {% fn($d) => [...$d[0], $d[4]] %}

expressionlist -> completeexpression
             | expressionlist _ "," _ completeexpression {% fn($d) => [...$d[0], $d[4]] %}

wordlist -> word
            | wordlist _ "," _ word {% fn($d) => [...$d[0], $d[4]] %}

completeexpression -> expr  {% fn($d) => new LanguageRule($d[0]) %}
                    | expr _ js  {% fn($d) => new LanguageRule($d[0], new PostProcessor($d[2])) %}

expr_member ->
      word {% fn($d) => new StringSymbol($d[0]) %}
    | "$" word {% fn($d) => new MixinSymbol($d[1]) %}
    | word "[" expressionlist "]" {% fn($d) => new MacroCallSymbol($d[0], $d[2]) %}
    | string "i":? {% fn($d) => is_null($d[1]) ? new LiteralSymbol($d[0]) : $insensitive($d[0]) %}
    | "%" word {% fn($d) => new TokenSymbol($d[1]) %}
    | charclass {% $id %}
    | "(" _ expression+ _ ")" {% fn($d) => new SubexpressionSymbol($d[2]) %}
    | expr_member _ ebnf_modifier {% fn($d) => new EBNFSymbol($d[2], $d[0]) %}

ebnf_modifier -> ":+" {% $getValue %} | ":*" {% $getValue %} | ":?" {% $getValue %}

expr -> expr_member
      | expr ws expr_member  {% fn($d) => [...$d[0], $d[2]] %}

word -> %word {% $getValue %}

string -> %string {% $getValue %}
        | %btstring {% $getValue %}

charclass -> %charclass  {% fn($d) => new RegexSymbol($d[0]->getValue()) %}

php -> %php  {% $getValue %}

_ -> ws:?
ws -> %ws
      | %ws:? %comment _