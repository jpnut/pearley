<?php

namespace JPNut\Pearley\Parser;

use JPNut\Pearley\Parser\Contracts\Lexer as LexerContract;
use JPNut\Pearley\Parser\Contracts\LexerState as LexerStateContract;
use JPNut\Pearley\Parser\Contracts\Token as TokenContract;

class StreamLexer implements LexerContract
{
    /**
     * @var string
     */
    public string $buffer;

    /**
     * @var \JPNut\Pearley\Parser\Contracts\LexerState
     */
    protected LexerStateContract $state;

    /**
     * @var \JPNut\Pearley\Parser\LineBreaks
     */
    protected LineBreaks $lineBreaks;

    public function __construct()
    {
        $this->lineBreaks = new LineBreaks;

        $this->reset();
    }

    /**
     * @param  string  $buffer
     * @param  \JPNut\Pearley\Parser\Contracts\LexerState|null  $state
     * @return \JPNut\Pearley\Parser\StreamLexer
     */
    public function reset(string $buffer = "", ?LexerStateContract $state = null): self
    {
        $this->buffer = $buffer;

        $this->state = is_null($state)
            ? new LexerState
            : $state->clone();

        return $this;
    }

    /**
     * @return \JPNut\Pearley\Parser\Contracts\Token|null
     */
    public function next(): ?TokenContract
    {
        if (($index = $this->state->getIndex()) === ($bufferLength = $this->bufferLength())) {
            return null; // End of buffer
        }

        $token = new Token(
            $text = $this->buffer[$index],
            $this->state->getIndex(),
            ($lineBreaks = ($this->lineBreaks)->calculate($text))->getTotal(),
            $this->state->getLine(),
            $this->state->getCol(),
        );

        $this->state->updateState($text, $lineBreaks);

        return $token;
    }

    /**
     * @return \JPNut\Pearley\Parser\Contracts\LexerState
     */
    public function save(): LexerStateContract
    {
        return $this->state->clone(true);
    }

    /**
     * @param  \JPNut\Pearley\Parser\Contracts\Token|null  $token
     * @param  string  $message
     * @return string
     */
    public function formatError(?TokenContract $token = null, string $message = "Error"): string
    {
        $nextLineBreak = strpos($this->buffer, "\n", $this->getState()->getIndex());

        if ($nextLineBreak === false) {
            $nextLineBreak = strlen($this->buffer);
        }

        $line = substr($this->buffer, $this->getState()->getLastLineBreak(), $nextLineBreak);
        $col  = $this->getState()->getIndex() - $this->getState()->getLastLineBreak();

        $message .= " at line ".$this->getState()->getLine()." col ".$col.":\n \n";
        $message .= "  ".$line."\n";
        $message .= "  ".join(' ', array_fill(0, $col, null))."^";

        return $message;
    }

    /**
     * @param  string  $name
     * @return bool
     */
    public function has(string $name): bool
    {
        return true;
    }

    /**
     * @return int
     */
    protected function bufferLength(): int
    {
        return strlen($this->buffer);
    }

    /**
     * @return \JPNut\Pearley\Parser\Contracts\LexerState
     */
    public function getState(): LexerStateContract
    {
        return $this->state;
    }
}
