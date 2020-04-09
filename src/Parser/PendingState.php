<?php

namespace JPNut\Pearley\Parser;

use ArrayObject;

class PendingState
{
    /**
     * @var \JPNut\Pearley\Parser\Rule
     */
    protected Rule $rule;

    /**
     * @var int
     */
    protected int $dot;

    /**
     * @var int
     */
    protected int $reference;

    /**
     * @var \ArrayObject
     */
    protected ArrayObject $wantedBy;

    /**
     * @var mixed
     */
    protected $data;

    /**
     * @var \JPNut\Pearley\Parser\State|null
     */
    protected ?State $left = null;

    /**
     * @var \JPNut\Pearley\Parser\State|null
     */
    protected ?State $right = null;

    /**
     * @param  \JPNut\Pearley\Parser\Rule  $rule
     * @param  int  $dot
     * @param  int  $reference
     * @param  \ArrayObject  $wantedBy
     */
    public function __construct(Rule $rule, int $dot, int $reference, ArrayObject $wantedBy)
    {
        $this->rule      = $rule;
        $this->dot       = $dot;
        $this->reference = $reference;
        $this->wantedBy  = $wantedBy;
        $this->data      = [];
    }

    /**
     * @param  mixed  $data
     * @return \JPNut\Pearley\Parser\PendingState
     */
    public function withData($data): self
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @param  \JPNut\Pearley\Parser\State  $state
     * @return \JPNut\Pearley\Parser\PendingState
     */
    public function withLeft(State $state): self
    {
        $this->left = $state;

        return $this;
    }

    /**
     * @param  \JPNut\Pearley\Parser\State  $state
     * @return \JPNut\Pearley\Parser\PendingState
     */
    public function withRight(State $state): self
    {
        $this->right = $state;

        return $this;
    }

    /**
     * @return \JPNut\Pearley\Parser\State
     */
    public function create(): State
    {
        return new State(
            $this->rule,
            $this->dot,
            $this->reference,
            $this->wantedBy,
            $this->data,
            $this->left,
            $this->right,
        );
    }
}
