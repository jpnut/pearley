<?php

namespace JPNut\Pearley\Lexer;

use Closure;
use InvalidArgumentException;
use JPNut\Pearley\Lexer\Contracts\TokenDefinition as TokenDefinitionContract;

class TokenDefinition implements TokenDefinitionContract
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
    protected ?Closure $valueMap;

    /**
     * @var bool
     */
    protected bool $hasLineBreaks;

    /**
     * @var bool
     */
    protected bool $throw;

    /**
     * @var bool
     */
    protected bool $pop;

    /**
     * @var string|null
     */
    protected ?string $push;

    /**
     * @var string|null
     */
    protected ?string $next;

    /**
     * @var \JPNut\Pearley\Lexer\Keyword[]
     */
    protected array $keywords;

    /**
     * @var bool
     */
    protected bool $hasKeywords;

    /**
     * @var string[]
     */
    protected array $keywordHashMap;


    /**
     * @param  string  $name
     * @param  string|null  $pattern
     * @param  \Closure|null  $valueMap
     * @param  bool  $hasLineBreaks
     * @param  bool  $throw
     * @param  bool  $pop
     * @param  string|null  $push
     * @param  string|null  $next
     * @param  array  $keywords
     */
    public function __construct(
        string $name,
        ?string $pattern,
        ?Closure $valueMap,
        bool $hasLineBreaks,
        bool $throw,
        bool $pop,
        ?string $push,
        ?string $next,
        array $keywords
    ) {
        $this->name          = $name;
        $this->pattern       = $pattern;
        $this->valueMap      = $valueMap;
        $this->hasLineBreaks = $hasLineBreaks;
        $this->throw         = $throw;
        $this->pop           = $pop;
        $this->push          = $push;
        $this->next          = $next;

        $this->setKeywords($keywords);
    }

    /**
     * @param  string  $name
     * @param  string|null  $pattern
     * @return \JPNut\Pearley\Lexer\PendingTokenDefinition
     */
    public static function initialise(string $name, ?string $pattern = null): PendingTokenDefinition
    {
        return new PendingTokenDefinition($name, $pattern);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getPattern(): ?string
    {
        return $this->pattern;
    }

    /**
     * @return \Closure|null
     */
    public function getValueMap(): ?Closure
    {
        return $this->valueMap;
    }

    /**
     * @return bool
     */
    public function shouldThrow(): bool
    {
        return $this->throw;
    }

    /**
     * @return bool
     */
    public function hasLineBreaks(): bool
    {
        return $this->hasLineBreaks;
    }

    /**
     * @return bool
     */
    public function shouldPop(): bool
    {
        return $this->pop;
    }

    /**
     * @return bool
     */
    public function shouldPush(): bool
    {
        return !is_null($this->push);
    }

    /**
     * @return string|null
     */
    public function getPush(): ?string
    {
        return $this->push;
    }

    /**
     * @return bool
     */
    public function hasNext(): bool
    {
        return !is_null($this->next);
    }

    /**
     * @return string|null
     */
    public function getNext(): ?string
    {
        return $this->next;
    }

    /**
     * @param  string  $text
     * @return \JPNut\Pearley\Lexer\Contracts\TokenDefinition
     */
    public function getDefinitionFromText(string $text): TokenDefinitionContract
    {
        if ($this->hasKeywords() && isset($this->keywordHashMap[$text])) {
            return $this->keywords[$this->keywordHashMap[$text]]->getTokenDefinition($this);
        }

        return $this;
    }

    /**
     * @return bool
     */
    protected function hasKeywords(): bool
    {
        return $this->hasKeywords ??= !empty($this->keywords);
    }

    /**
     * @param  array  $keywords
     * @return void
     */
    protected function setKeywords(array $keywords): void
    {
        foreach ($keywords as $index => $keyword) {
            if ($keyword instanceof Keyword) {
                $this->addKeyword($keyword);

                continue;
            }

            if (is_string($keyword) && !is_string($index)) {
                $this->addKeyword(new Keyword($keyword, $keyword));

                continue;
            }

            if (!is_string($keyword) && !is_array($keyword)) {
                throw new InvalidArgumentException(
                    "Invalid keyword definition: keyword must be an instance of "
                    .Keyword::class.", a string, or an array of strings."
                );
            }

            if (!is_string($index)) {
                throw new InvalidArgumentException(
                    "Invalid keyword definition: please provide a unique name for each keyword."
                );
            }

            $this->addKeyword(new Keyword($index, $keyword));
        }
    }

    /**
     * @param  \JPNut\Pearley\Lexer\Keyword  $keyword
     */
    protected function addKeyword(Keyword $keyword): void
    {
        if (isset($this->keywords[$name = $keyword->getName()])) {
            throw new InvalidArgumentException("Duplicate keyword definition detected for '{$name}'");
        }

        foreach ($keyword->getWords() as $word) {
            if (isset($this->keywordHashMap[$word])) {
                throw new InvalidArgumentException("Duplicate keyword detected for '{$word}'");
            }

            $this->keywordHashMap[$word] = $name;
        }

        $this->keywords[$name] = $keyword;
    }
}
