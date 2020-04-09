<?php

namespace JPNut\Pearley\Parser;

use ArrayObject;
use JPNut\Pearley\Parser\Contracts\LexerState as LexerStateContract;

class Column
{
    /**
     * @var \JPNut\Pearley\Parser\Grammar
     */
    protected Grammar $grammar;

    /**
     * @var int
     */
    protected int $index;

    /**
     * @var \JPNut\Pearley\Parser\State[]
     */
    protected array $states;

    /**
     * @var \JPNut\Pearley\Parser\State[][]
     */
    protected array $wants;

    /**
     * @var \JPNut\Pearley\Parser\State[]
     */
    protected array $scannable;

    /**
     * @var array
     */
    protected array $completed;

    /**
     * @var \JPNut\Pearley\Parser\Contracts\LexerState|null
     */
    protected ?LexerStateContract $lexerState;

    /**
     * @param  \JPNut\Pearley\Parser\Grammar  $grammar
     * @param  int  $index
     */
    public function __construct(Grammar $grammar, int $index)
    {
        $this->grammar    = $grammar;
        $this->index      = $index;
        $this->states     = [];
        $this->wants      = []; // states indexed by the non-terminal they expect
        $this->scannable  = []; // list of states that expect a token
        $this->completed  = []; // states that are nullable
        $this->lexerState = null;
    }

    /**
     * @return int
     */
    public function getIndex(): int
    {
        return $this->index;
    }

    /**
     * @return \JPNut\Pearley\Parser\State[]
     */
    public function getStates(): array
    {
        return $this->states;
    }

    /**
     * @param  \JPNut\Pearley\Parser\State  $state
     * @return \JPNut\Pearley\Parser\Column
     */
    public function pushState(State $state): self
    {
        $this->states[] = $state;

        return $this;
    }

    /**
     * @param  string  $index
     * @param  \JPNut\Pearley\Parser\State|null  $state
     * @return \JPNut\Pearley\Parser\Column
     */
    public function pushWant(string $index, ?State $state = null): self
    {
        if (!isset($this->wants[$index])) {
            $this->wants[$index] = new ArrayObject([]);
        }

        if (!is_null($state)) {
            $this->wants[$index][] = $state;
        }

        return $this;
    }

    /**
     * @return \JPNut\Pearley\Parser\State[]
     */
    public function getScannable(): array
    {
        return $this->scannable;
    }

    /**
     * @return \JPNut\Pearley\Parser\Contracts\LexerState|null
     */
    public function getLexerState(): ?LexerStateContract
    {
        return $this->lexerState;
    }

    /**
     * @param  \JPNut\Pearley\Parser\Contracts\LexerState|null  $lexerState
     * @return \JPNut\Pearley\Parser\Column
     */
    public function setLexerState(?LexerStateContract $lexerState): self
    {
        $this->lexerState = $lexerState;

        return $this;
    }

    public function process(): void
    {
        // In this for loop, we may add additional states (see the predict and complete methods).
        // Therefore, we count the number of states at every iteration.
        for ($w = 0; $w < count($this->states); $w++) {
            $state = $this->states[$w];

            if ($state->isComplete()) {
                $this->finish($state);

                continue;
            }

            $symbol = $state->getRule()->getSymbol($state->getDot());

            // queue scannable states
            if (!$symbol->isNonterminal()) {
                $this->scannable[] = $state;

                continue;
            }

            // predict
            if (isset($this->wants[$name = $symbol->getValue()])) {
                $this->pushWant($name, $state);

                if (array_key_exists($name, $this->completed)) {
                    $nulls = $this->completed[$name];
                    foreach ($nulls as $right) {
                        $this->complete($state, $right);
                    }
                }

                continue;
            }

            $this->pushWant($name, $state)
                ->predict($name);
        }
    }

    /**
     * @param  \JPNut\Pearley\Parser\State  $state
     */
    protected function finish(State $state): void
    {
        $state->finish();

        // complete
        $wantedBy = $state->getWantedBy();
        for ($i = count($wantedBy); $i--;) {
            $left = $wantedBy[$i];
            $this->complete($left, $state);
        }

        // special-case nullables
        if ($state->getReference() === $this->index) {
            // make sure future predictors of this rule get completed.
            $name                     = $state->getRule()->getName();
            $this->completed[$name]   = isset($this->completed[$name]) ? $this->completed[$name] : [];
            $this->completed[$name][] = $state;
        }
    }

    /**
     * @param  string  $name
     */
    public function predict(string $name): void
    {
        $rules = $this->grammar->getRulesByName($name);

        foreach ($rules as $index => $rule) {
            $this->states[] = State::initialise($rule, 0, $this->index, $this->wants[$name])->create();
        }
    }

    /**
     * @param  \JPNut\Pearley\Parser\State  $left
     * @param  \JPNut\Pearley\Parser\State  $right
     */
    public function complete(State $left, State $right): void
    {
        $this->states[] = $left->nextState($right);
    }
}
