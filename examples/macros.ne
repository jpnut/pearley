# Matches "Cows oink." and "Cows moo!"
sentence[ANIMAL, PUNCTUATION] -> animalGoes[("moo" | "oink" | "baa")] $PUNCTUATION
animalGoes[SOUND] -> $ANIMAL " " $SOUND # uses $ANIMAL from its caller

main -> sentence["Cows", ("." | "!")] {% fn($d) => "{$d[0][0][0][0]} {$d[0][0][2][0][0]}{$d[0][1][0][0]->getValue()}" %}