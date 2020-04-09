@{%
$joiner = fn ($d) => join('', $d);
%}

blocks -> blocks ws block {% $joiner %}
    | block {% $id %}

block -> word {% $id %}
    | number {% $id %}

word -> [a-zA-Z]:+ {% fn ($d) => $joiner(array_map($joiner, $d)) %}

number -> [0-9]:+ {% fn ($d) => $joiner(array_map($joiner, $d)) %}

ws -> [\s]:+ {% fn ($d) => " "  %}