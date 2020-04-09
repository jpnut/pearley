<?php

namespace JPNut\Pearley\Parser;

use ArrayObject;

class State
{
    /**
     * @var int
     */
    protected static int $INSTANCES = 0;

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
    protected ?State $left;

    /**
     * @var \JPNut\Pearley\Parser\State|null
     */
    protected ?State $right;

    /**
     * @var bool
     */
    protected bool $isComplete;

    /**
     * @var int
     */
    protected int $id;

    /**
     * @param \JPNut\Pearley\Parser\Rule       $rule
     * @param int                              $dot
     * @param int                              $reference
     * @param \ArrayObject                     $wantedBy
     * @param mixed                            $data
     * @param \JPNut\Pearley\Parser\State|null $left
     * @param \JPNut\Pearley\Parser\State|null $right
     */
    public function __construct(
        Rule $rule,
        int $dot,
        int $reference,
        ArrayObject $wantedBy,
        $data,
        ?self $left = null,
        ?self $right = null
    ) {
        $this->rule = $rule;
        $this->dot = $dot;
        $this->reference = $reference;
        $this->wantedBy = $wantedBy;
        $this->data = $data;
        $this->left = $left;
        $this->right = $right;

        $this->isComplete = $rule->isComplete($dot);

        $this->id = ++self::$INSTANCES;
    }

    /**
     * @param \JPNut\Pearley\Parser\Rule $rule
     * @param int                        $dot
     * @param int                        $reference
     * @param \ArrayObject               $wantedBy
     *
     * @return \JPNut\Pearley\Parser\PendingState
     */
    public static function initialise(Rule $rule, int $dot, int $reference, ArrayObject $wantedBy): PendingState
    {
        return new PendingState($rule, $dot, $reference, $wantedBy);
    }

    /**
     * @return \JPNut\Pearley\Parser\Rule
     */
    public function getRule(): Rule
    {
        return $this->rule;
    }

    /**
     * @return int
     */
    public function getDot(): int
    {
        return $this->dot;
    }

    /**
     * @return int
     */
    public function getReference(): int
    {
        return $this->reference;
    }

    /**
     * @return \ArrayObject
     */
    public function getWantedBy(): ArrayObject
    {
        return $this->wantedBy;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return \JPNut\Pearley\Parser\State|null
     */
    public function getLeft(): ?self
    {
        return $this->left;
    }

    /**
     * @return \JPNut\Pearley\Parser\State|null
     */
    public function getRight(): ?self
    {
        return $this->right;
    }

    /**
     * @return bool
     */
    public function isComplete(): bool
    {
        return $this->isComplete;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param \JPNut\Pearley\Parser\State $right
     *
     * @return \JPNut\Pearley\Parser\State
     */
    public function nextState(self $right): self
    {
        $state = static::initialise($this->rule, $dot = ($this->dot + 1), $this->reference, $this->wantedBy)
            ->withLeft($this)
            ->withRight($right);

        if ($this->rule->isComplete($dot)) {
            $state->withData($this->buildData($this, $right));
        }

        return $state->create();
    }

    public function finish(): void
    {
        if ($this->rule->hasPostProcessor()) {
            $this->data = $this->rule->postprocess($this->data, $this->reference);
        }
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return "{{$this->rule->toString($this->dot)}}, from: ".($this->reference ?: 0);
    }

    /**
     * @param \JPNut\Pearley\Parser\State $node
     * @param \JPNut\Pearley\Parser\State $right
     *
     * @return array
     */
    protected function buildData(self $node, self $right): array
    {
        $children = [$right->getData()];

        while (!is_null($node->getLeft())) {
            if ($r = $node->getRight()) {
                $children[] = $r->getData();
            }

            $node = $node->getLeft();
        }

        return array_reverse($children);
    }
}
