<?php

namespace JPNut\Pearley\Lexer;

use Closure;

class PendingTokenDefinition
{
    /**
     * @var string
     */
    protected string $name;

    /**
     * @var string|null
     */
    protected ?string $pattern;

    /**
     * @var \Closure|null
     */
    protected ?Closure $valueMap = null;

    /**
     * @var bool
     */
    protected bool $hasLineBreaks = false;

    /**
     * @var bool
     */
    protected bool $throw = false;

    /**
     * @var bool
     */
    protected bool $pop = false;

    /**
     * @var string|null
     */
    protected ?string $push = null;

    /**
     * @var string|null
     */
    protected ?string $next = null;

    /**
     * @var array
     */
    protected array $keywords = [];

    /**
     * @param string      $name
     * @param string|null $pattern
     */
    public function __construct(string $name, ?string $pattern)
    {
        $this->name = $name;
        $this->pattern = $pattern;
    }

    /**
     * @param \Closure $valueMap
     *
     * @return $this
     */
    public function withValueMap(Closure $valueMap): self
    {
        $this->valueMap = $valueMap;

        return $this;
    }

    /**
     * @return $this
     */
    public function withLineBreaks(): self
    {
        $this->hasLineBreaks = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function withoutLineBreaks(): self
    {
        $this->hasLineBreaks = false;

        return $this;
    }

    /**
     * @return $this
     */
    public function shouldThrow(): self
    {
        $this->throw = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function shouldPop(): self
    {
        $this->pop = true;

        return $this;
    }

    /**
     * @param string $push
     *
     * @return $this
     */
    public function shouldPushTo(string $push): self
    {
        $this->push = $push;

        return $this;
    }

    /**
     * @param string $next
     *
     * @return $this
     */
    public function withNext(string $next): self
    {
        $this->next = $next;

        return $this;
    }

    public function withKeywords(array $keywords): self
    {
        $this->keywords = $keywords;

        return $this;
    }

    /**
     * @return \JPNut\Pearley\Lexer\TokenDefinition
     */
    public function create(): TokenDefinition
    {
        return new TokenDefinition(
            $this->name,
            $this->pattern,
            $this->valueMap,
            $this->hasLineBreaks,
            $this->throw,
            $this->pop,
            $this->push,
            $this->next,
            $this->keywords,
        );
    }
}
