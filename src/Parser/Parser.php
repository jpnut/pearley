<?php

namespace JPNut\Pearley\Parser;

use Exception;
use JPNut\Pearley\Parser\Contracts\ParserConfig as ParserConfigContract;
use JPNut\Pearley\Parser\Contracts\Token as TokenContract;

class Parser
{
    /**
     * @var \JPNut\Pearley\Parser\Contracts\ParserConfig
     */
    protected ParserConfigContract $config;

    /**
     * @var array
     */
    protected array $results;

    /**
     * @var \JPNut\Pearley\Parser\Column[]
     */
    protected array $table;

    /**
     * @var int
     */
    protected int $current;

    /**
     * @var \JPNut\Pearley\Parser\Contracts\LexerState|null
     */
    protected ?Contracts\LexerState $lexerState;

    /**
     * @param \JPNut\Pearley\Parser\Contracts\ParserConfig $config
     */
    public function __construct(ParserConfigContract $config)
    {
        $this->config = $config;
        $this->lexerState = null;

        $this->initialise();
    }

    protected function initialise()
    {
        // Setup a table
        $column = new Column($grammar = $this->config->getGrammar(), 0);
        $this->table = [$column];

        $column->pushWant($start = $grammar->getStart());
        $column->predict($start);
        $column->process();

        $this->current = 0; // token index
    }

    /**
     * @param string $chunk
     *
     * @return \JPNut\Pearley\Parser\Parser
     */
    public function feed(string $chunk): self
    {
        $this->config->getLexer()->reset($chunk, $this->lexerState);

        $column = null;

        while ($token = $this->config->getLexer()->next()) {
            $column = $this->table[$this->current];
            $scannable = $column->getScannable();

            // GC unused states
            if (!$this->config->shouldKeepHistory() && isset($this->table[$this->current - 1])) {
                $this->table[$this->current - 1] = null;
            }

            // Create and add the next column to the table
            $this->table[] = $nextColumn = new Column($this->config->getGrammar(), $index = $this->current + 1);

            for ($w = count($scannable); $w--;) {
                $state = $scannable[$w];
                $symbol = $state->getRule()->getSymbol($state->getDot());

                if (is_null($symbol)) {
                    continue;
                }

                if ($this->validTokenForSymbol($token, $symbol)) {
                    // The token matches the symbol definition, so add the state to the stack
                    $nextColumn->pushState(
                        $state->nextState(
                            State::initialise(
                                $state->getRule(),
                                $state->getDot(),
                                $index - 1,
                                $state->getWantedBy()
                            )
                                ->withData($token)
                                ->create()
                        )
                    );
                }
            }

            $nextColumn->process();

            // If no states were pushed, that means the symbol did not
            // match any of the tokens we expected
            if (empty($nextColumn->getStates())) {
                $this->reportError($token);
            }

            if ($this->config->shouldKeepHistory()) {
                $column->setLexerState($this->config->getLexer()->save());
            }

            $this->current++;
        }

        if ($column) {
            $this->setLexerState($this->config->getLexer()->save());
        }

        // Incrementally keep track of results
        $this->results = $this->finish();

        return $this;
    }

    /**
     * @return array
     */
    public function getResults(): array
    {
        return $this->results;
    }

    /**
     * @param \JPNut\Pearley\Parser\Contracts\Token $token
     */
    protected function reportError(TokenContract $token)
    {
        $lines = [];

        $tokenDisplay = (!is_null($type = $token->getType()) ? "{$type} token: " : '').json_encode($token->getValue());

        $lines[] = $this->config->getLexer()->formatError($token, 'Syntax error');
        $lines[] = "Unexpected {$tokenDisplay}. Instead, I was expecting to see one of the following:\n";

        $lastColumnIndex = count($this->table) - 2;
        $lastColumn = $this->table[$lastColumnIndex];
        $expectantStates = array_filter($lastColumn->getStates(), function (State $state) {
            if (!$nextSymbol = $state->getRule()->getSymbol($state->getDot())) {
                return false;
            }

            return !$nextSymbol->isNonterminal();
        });

        // Display a "state stack" for each expectant state
        // - which shows you how this state came to be, step by step.
        // If there is more than one derivation, we only display the first one.
        $stateStacks = array_values($this->buildStateStack($expectantStates));

        // Display each state that is expecting a terminal symbol next.
        foreach ($stateStacks as $stateStack) {
            $state = reset($stateStack);
            $symbolDisplay = $this->getSymbolDisplay($state->getRule()->getSymbol($state->getDot()));
            $lines[] = "A {$symbolDisplay} based on:";
            $this->displayStateStack($stateStack, $lines);
        }

        $lines[] = '';

        throw new Exception(implode("\n", $lines));
    }

    /**
     * @param \JPNut\Pearley\Parser\State[] $states
     *
     * @return \JPNut\Pearley\Parser\State[][]
     */
    protected function buildStateStack(array $states): array
    {
        return array_map(fn (State $state) => $this->buildFirstStateStack($state, []), $states);
    }

    /**
     * @return array
     */
    public function finish(): array
    {
        // Return the possible parsings
        $considerations = [];
        $start = $this->config->getGrammar()->getStart();
        $column = $this->table[count($this->table) - 1];

        foreach ($column->getStates() as $state) {
            if ($state->getRule()->getName() === $start
                && $state->getDot() === count($state->getRule()->getSymbols())
                && $state->getReference() === 0) {
                $considerations[] = $state;
            }
        }

        return array_map(
            function (State $state) {
                return $state->getData();
            },
            $considerations
        );
    }

    /**
     * @param int $index
     *
     * @return \JPNut\Pearley\Parser\Parser
     */
    public function rewind(int $index): self
    {
        if (!$this->config->shouldKeepHistory()) {
            throw new Exception('Enable history to enable rewinding');
        }
        // nb. recall column (table) indices fall between token indices.
        //        col 0   --   token 0   --   col 1
        $this->restore($this->table[$index]);

        return $this;
    }

    /**
     * @param \JPNut\Pearley\Parser\Column $column
     *
     * @return \JPNut\Pearley\Parser\Parser
     */
    public function restore(Column $column): self
    {
        $this->current = ($index = $column->getIndex());
        $this->table[$index] = $column;
        array_splice($this->table, $index + 1);
        $this->lexerState = $column->getLexerState();

        // Incrementally keep track of results
        $this->results = $this->finish();

        return $this;
    }

    /**
     * @return \JPNut\Pearley\Parser\Column
     */
    public function save(): Column
    {
        $column = $this->table[$this->current];
        $column->setLexerState($this->lexerState);

        return $column;
    }

    /**
     * Builds the first state stack. You can think of a state stack as the call stack
     * of the recursive-descent parser which the Nearley parse algorithm simulates.
     * A state stack is represented as an array of state objects. Within a
     * state stack, the first item of the array will be the starting
     * state, with each successive item in the array going further back into history.
     * This function needs to be given a starting state and an empty array representing
     * the visited states, and it returns a single state stack.
     *
     * @param \JPNut\Pearley\Parser\State   $state
     * @param \JPNut\Pearley\Parser\State[] $visited
     *
     * @return \JPNut\Pearley\Parser\State[]
     */
    protected function buildFirstStateStack(State $state, array $visited): array
    {
        if (array_key_exists($state->getId(), $visited)) {
            // The state already appears in our array of visited states, so return early to prevent infinite loop.
            return $visited;
        }

        if (count($wantedBy = $state->getWantedBy()) === 0) {
            return $this->buildInitialStateStack($state);
        }

        $childResult = $this->buildFirstStateStack(
            $wantedBy[0],
            ($initialStack = $this->buildInitialStateStack($state)) + $visited,
        );

        return $initialStack + $childResult;
    }

    /**
     * @param \JPNut\Pearley\Parser\State $state
     *
     * @return \JPNut\Pearley\Parser\State[]
     */
    protected function buildInitialStateStack(State $state): array
    {
        return [$state->getId() => $state];
    }

    /**
     * @param \JPNut\Pearley\Parser\Symbol|null $symbol
     *
     * @return string
     */
    protected function getSymbolDisplay(?Symbol $symbol): string
    {
        if (is_null($symbol)) {
            throw new Exception('Cannot display symbol: no symbol found');
        }

        if ($symbol->isNonterminal()) {
            return $symbol->getValue();
        }

        if ($symbol->isLiteral()) {
            return json_encode($symbol->getValue());
        }

        if ($symbol->isRegex()) {
            return "character matching {$symbol->getValue()}";
        }

        if ($symbol->isToken()) {
            return "{$symbol->getValue()} token";
        }

        throw new Exception("Cannot display symbol: unknown symbol type '{$symbol->getType()}'");
    }

    /**
     * @param \JPNut\Pearley\Parser\State[] $stateStack
     * @param array                         $lines
     */
    protected function displayStateStack(array $stateStack, array &$lines)
    {
        $lastDisplay = null;

        $sameDisplayCount = 0;

        foreach ($stateStack as $state) {
            $display = $state->getRule()->toString($state->getDot());

            if ($display === $lastDisplay) {
                $sameDisplayCount++;
            } else {
                if ($sameDisplayCount > 0) {
                    $lines[] = '    ↑ ︎'.$sameDisplayCount.' more lines identical to this';
                }

                $sameDisplayCount = 0;

                $lines[] = '    '.$display;
            }

            $lastDisplay = $display;
        }
    }

    /**
     * @param \JPNut\Pearley\Parser\Symbol $symbol
     * @param $value
     *
     * @return bool
     */
    protected function performRegexTest(Symbol $symbol, $value): bool
    {
        if (!is_string($value)) {
            return false;
        }

        return (bool) (preg_match((new RegExp($symbol->getValue()))->getQualifiedPattern(), $value));
    }

    /**
     * @param \JPNut\Pearley\Parser\Contracts\LexerState|null $state
     *
     * @return \JPNut\Pearley\Parser\Parser
     */
    protected function setLexerState(?Contracts\LexerState $state): self
    {
        $this->lexerState = $state;

        return $this;
    }

    /**
     * @return int
     */
    public function getCurrent(): int
    {
        return $this->current;
    }

    /**
     * @return \JPNut\Pearley\Parser\Column[]
     */
    public function getTable(): array
    {
        return $this->table;
    }

    /**
     * @return \JPNut\Pearley\Parser\Contracts\ParserConfig
     */
    public function getConfig(): ParserConfigContract
    {
        return $this->config;
    }

    /**
     * @return \JPNut\Pearley\Parser\Contracts\LexerState|null
     */
    public function getLexerState(): ?Contracts\LexerState
    {
        return $this->lexerState;
    }

    /**
     * @param \JPNut\Pearley\Parser\Contracts\Token $token
     * @param \JPNut\Pearley\Parser\Symbol          $symbol
     *
     * @return bool
     */
    protected function validTokenForSymbol(TokenContract $token, Symbol $symbol): bool
    {
        if ($symbol->isRegex()) {
            return $this->performRegexTest($symbol, $token->getValue());
        }

        if ($symbol->isToken()) {
            return $symbol->getValue() === $token->getType();
        }

        return $symbol->getValue() === $token->getText();
    }
}
