<?php

namespace JPNut\Pearley\Parser;

use InvalidArgumentException;

class Symbol
{
    const NONTERMINAL = 0;

    const LITERAL = 1;

    const REGEX = 2;

    const TOKEN = 3;

    const TYPES = [
        self::NONTERMINAL => true,
        self::LITERAL     => true,
        self::REGEX       => true,
        self::TOKEN       => true,
    ];

    /**
     * @var string
     */
    protected string $value;

    /**
     * @var int
     */
    protected int $type;

    /**
     * @param string   $value
     * @param int|null $type
     */
    public function __construct(string $value, ?int $type = null)
    {
        $this->value = $value;
        $this->type = $this->parseType($type);
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @return bool
     */
    public function isNonterminal(): bool
    {
        return $this->type === static::NONTERMINAL;
    }

    /**
     * @return bool
     */
    public function isLiteral(): bool
    {
        return $this->type === static::LITERAL;
    }

    /**
     * @return bool
     */
    public function isRegex(): bool
    {
        return $this->type === static::REGEX;
    }

    /**
     * @return bool
     */
    public function isToken(): bool
    {
        return $this->type === static::TOKEN;
    }

    /**
     * @param int|null $type
     *
     * @return int
     */
    protected function parseType(?int $type): int
    {
        if (is_null($type)) {
            return static::NONTERMINAL;
        }

        if (!isset(static::TYPES[$type])) {
            throw new InvalidArgumentException("Symbol type '{$type}' not recognised.");
        }

        return $type;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        switch ($this->type) {
            case static::NONTERMINAL:
            case static::REGEX:
                return $this->value;
            case static::LITERAL:
                return json_encode($this->value);
            case static::TOKEN:
                return "%{$this->value}";
            default:
                throw new InvalidArgumentException("Symbol type '{$this->type}' not recognised.");
        }
    }
}
