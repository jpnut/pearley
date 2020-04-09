<?php

namespace JPNut\Pearley\Parser;

use Closure;
use InvalidArgumentException;

class Rule
{
    /**
     * @var string
     */
    protected string $name;

    /**
     * @var \JPNut\Pearley\Parser\Symbol[]
     */
    protected array $symbols;

    /**
     * @var \Closure|null
     */
    protected ?Closure $postprocess;

    /**
     * @param  string  $name
     * @param  array  $symbols
     * @param  \Closure|null  $postprocess
     */
    public function __construct(string $name, array $symbols, ?Closure $postprocess = null)
    {
        $this->name        = $name;
        $this->symbols     = $this->parseSymbols($symbols);
        $this->postprocess = $postprocess;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return \JPNut\Pearley\Parser\Symbol[]
     */
    public function getSymbols(): array
    {
        return $this->symbols;
    }

    /**
     * @param  int  $index
     * @return \JPNut\Pearley\Parser\Symbol|null
     */
    public function getSymbol(int $index)
    {
        return $this->symbols[$index] ?? null;
    }

    /**
     * @param  int  $dot
     * @return bool
     */
    public function isComplete(int $dot): bool
    {
        return $dot === count($this->symbols);
    }

    /**
     * @return bool
     */
    public function hasPostProcessor(): bool
    {
        return !is_null($this->postprocess);
    }

    /**
     * @param  mixed  $data
     * @param  int  $reference
     * @return mixed
     */
    public function postprocess($data, int $reference)
    {
        if (!$this->hasPostProcessor()) {
            return $data;
        }

        $postprocessor = $this->postprocess;

        return $postprocessor($data, $reference);
    }

    /**
     * @param  int  $withCursorAt
     * @return string
     */
    public function toString(int $withCursorAt): string
    {
        $symbols = array_map(fn(Symbol $symbol) => (string) $symbol, $this->symbols);

        $symbolSequence = join(
            ' ',
            [...array_slice($symbols, 0, $withCursorAt), " ● ", ...array_slice($symbols, $withCursorAt)]
        );

        return "{$this->name} → {$symbolSequence}";
    }

    /**
     * @param  array  $symbols
     * @return array
     */
    protected function parseSymbols(array $symbols): array
    {
        return array_map(fn($symbol) => $this->createSymbol($symbol), $symbols);
    }

    /**
     * @param  mixed  $symbol
     * @return \JPNut\Pearley\Parser\Symbol
     */
    protected function createSymbol($symbol): Symbol
    {
        if ($symbol instanceof Symbol) {
            return $symbol;
        }

        if (is_string($symbol)) {
            return $this->createSymbolFromString($symbol);
        }

        if (is_array($symbol)) {
            return $this->createSymbolFromArray($symbol);
        }

        throw new InvalidArgumentException(
            "Invalid symbol provided: symbol must be a string, an array or an instance of ".Symbol::class
        );
    }

    /**
     * @param  string  $symbol
     * @return \JPNut\Pearley\Parser\Symbol
     */
    protected function createSymbolFromString(string $symbol): Symbol
    {
        return new Symbol($symbol);
    }

    /**
     * @param  array  $symbol
     * @return \JPNut\Pearley\Parser\Symbol
     */
    protected function createSymbolFromArray(array $symbol): Symbol
    {
        if (!isset($symbol['value'])) {
            throw new InvalidArgumentException("Symbol value is required");
        }

        if (!is_string($symbol['value'])) {
            throw new InvalidArgumentException("Symbol value must be a string");
        }

        if (isset($symbol['type']) && !is_null($symbol['type']) && !is_int($symbol['type'])) {
            throw new InvalidArgumentException("Symbol type must be either: an integer or null");
        }

        return new Symbol(
            $symbol['value'],
            $symbol['type'] ?? null,
        );
    }
}
