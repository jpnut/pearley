# http://www.json.org/
# http://www.asciitable.com/
@use "JPNut\Pearley\Lexer\TokenDefinition"
@use "JPNut\Pearley\Lexer\Lexer"
@use "JPNut\Pearley\Lexer\LexerConfig"

@{%
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

%}
@lexer lexer

json -> _ (object | array) _ {% fn($d) => $d[1][0] %}

object -> "{" _ "}" {% fn($d) => [] %}
    | "{" _ pair (_ "," _ pair):* _ "}" {% $extractObject %}

array -> "[" _ "]" {% fn($d) => [] %}
    | "[" _ value (_ "," _ value):* _ "]" {% $extractArray %}

value ->
      object {% $id %}
    | array {% $id %}
    | number {% $id %}
    | string {% $id %}
    | "true" {% fn($d) => true %}
    | "false" {% fn($d) => false %}
    | "null" {% fn($d) => null %}

number -> %number {% fn($d) => floatval($d[0]->getValue()) %}

string -> %string {% fn($d) => json_decode($d[0]->getValue()) %}

pair -> key _ ":" _ value {% fn($d) => [$d[0], $d[4]] %}

key -> string {% $id %}

_ -> null | %space {% fn($d) => null %}