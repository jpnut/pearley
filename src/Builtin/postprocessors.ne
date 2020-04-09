# Simple postprocessors

# Postprocessor generator that lets you select the nth element of the list.
# `$id` is equivalent to $nth(0).
@{%
$nth = fn ($n) => fn ($d) => $d[$n];
%}
