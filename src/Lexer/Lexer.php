<?php

namespace JPNut\Pearley\Lexer;

use Exception;
use InvalidArgumentException;
use JPNut\Pearley\Lexer\Contracts\Lexer as LexerContract;
use JPNut\Pearley\Lexer\Contracts\LexerConfig as LexerConfigContract;
use JPNut\Pearley\Lexer\Contracts\LexerState as LexerStateContract;
use JPNut\Pearley\Lexer\Contracts\Token as TokenContract;
use JPNut\Pearley\Lexer\Contracts\TokenDefinition as TokenDefinitionContract;
use JPNut\Pearley\Parser\Contracts\LexerState as BaseLexerStateContract;
use JPNut\Pearley\Parser\Contracts\Token as BaseTokenContract;
use JPNut\Pearley\Parser\LineBreaks;

class Lexer implements LexerContract
{
    /**
     * @var \JPNut\Pearley\Lexer\Contracts\LexerConfig[]
     */
    protected array $configs;

    /**
     * @var \JPNut\Pearley\Lexer\Contracts\LexerState
     */
    protected LexerStateContract $state;

    /**
     * @var string
     */
    protected string $buffer;

    /**
     * @var \JPNut\Pearley\Lexer\Contracts\TokenDefinition
     */
    protected TokenDefinitionContract $errorTokenDefinition;

    /**
     * @var array
     */
    protected array $token_names = [];

    /**
     * @var \JPNut\Pearley\Parser\LineBreaks
     */
    protected LineBreaks $lineBreaks;

    /**
     * @param \JPNut\Pearley\Lexer\Contracts\LexerConfig[] $additionalConfigs
     */
    public function __construct(LexerConfigContract ...$additionalConfigs)
    {
        $this->configs = $this->setConfigs(...$additionalConfigs);
        $this->lineBreaks = new LineBreaks();

        $this->reset();
    }

    /**
     * @return \JPNut\Pearley\Lexer\Contracts\LexerConfig
     */
    public function config(): LexerConfigContract
    {
        return $this->getConfig($this->state->getConfigName());
    }

    /**
     * @param string                                          $buffer
     * @param \JPNut\Pearley\Parser\Contracts\LexerState|null $state
     *
     * @return \JPNut\Pearley\Lexer\Lexer
     */
    public function reset(string $buffer = '', ?BaseLexerStateContract $state = null): self
    {
        $this->buffer = $buffer;

        $this->state = is_null($state)
            ? LexerState::create($this->defaultConfigName())
            : $state->clone();

        return $this;
    }

    /**
     * @return \JPNut\Pearley\Parser\Contracts\Token|null
     */
    public function next(): ?BaseTokenContract
    {
        if (($index = $this->state->getIndex()) === ($bufferLength = $this->bufferLength())) {
            return null; // End of buffer
        }

        $matches = $this->config()->getRegex()
            ->setLastIndex($index)
            ->match($this->buffer);

        if (empty($matches)) {
            // create & return error token
            return $this->createToken(
                $this->errorTokenDefinition(),
                substr($this->buffer, $index, $bufferLength),
                $index
            );
        }

        // There was a match, but it was not the first character in the remaining portion of the buffer
        // That means there was at least one character which did not match the combined regex pattern
        if ($matches[0][1] !== 0) {
            return $this->createToken(
                $this->errorTokenDefinition(),
                substr($this->buffer, $index, $matches[0][1]),
                $index
            );
        }

        // matched - create & return a new token
        return $this->createToken(
            $this->findMatchDefinition($matches),
            $this->getMatchText($matches),
            $index
        );
    }

    /**
     * @return \JPNut\Pearley\Parser\Contracts\LexerState
     */
    public function save(): BaseLexerStateContract
    {
        return $this->state->clone(true);
    }

    /**
     * @param \JPNut\Pearley\Parser\Contracts\Token|null $token
     * @param string                                     $message
     *
     * @return string
     */
    public function formatError(?BaseTokenContract $token = null, string $message = 'Error'): string
    {
        if (is_null($token)) {
            $token = new Token(
                $definition = $this->errorTokenDefinition(),
                $text = substr($this->buffer, ($index = $this->state->getIndex())),
                $index,
                $this->calculateLineBreaks($definition, $text)->getTotal(),
                $this->state->getLine(),
                $this->state->getCol()
            );
        }

        $start = max(0, $token->getOffset() - $token->getCol() + 1);
        $eol = $token->getLineBreaks() > 0
            ? strpos($token->getText(), "\n")
            : (strpos($this->buffer, "\n", $start) ?: strlen($this->buffer));
        $firstLine = substr($this->buffer, $start, $eol - $start);

        $message .= ' at line '.$token->getLine().' col '.$token->getCol().":\n \n";
        $message .= '  '.$firstLine."\n";
        $message .= '  '.implode(' ', array_fill(0, $token->getCol(), null)).'^';

        return $message;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function has(string $name): bool
    {
        return isset($this->token_names[$name]);
    }

    /**
     * @return int
     */
    protected function bufferLength(): int
    {
        return strlen($this->buffer);
    }

    /**
     * @param \JPNut\Pearley\Lexer\Contracts\TokenDefinition $definition
     * @param string                                         $text
     * @param int                                            $offset
     *
     * @return \JPNut\Pearley\Lexer\Contracts\Token
     */
    protected function createToken(TokenDefinitionContract $definition, string $text, int $offset): TokenContract
    {
        $token = new Token(
            $definition,
            $text,
            $offset,
            ($lineBreaks = $this->calculateLineBreaks($definition, $text))->getTotal(),
            $this->state->getLine(),
            $this->state->getCol()
        );

        $this->state->updateState($text, $lineBreaks);

        if ($definition->shouldThrow()) {
            throw new Exception($this->formatError($token, 'invalid syntax'));
        }

        if ($definition->shouldPop()) {
            $this->state->pop();
        } elseif ($definition->shouldPush()) {
            $this->state->push($definition->getPush());
        } elseif ($definition->hasNext()) {
            $this->state->setConfigName($definition->getNext());
        }

        return $token;
    }

    /**
     * @param \JPNut\Pearley\Lexer\Contracts\TokenDefinition $definition
     * @param string                                         $text
     *
     * @return \JPNut\Pearley\Parser\LineBreaks
     */
    protected function calculateLineBreaks(TokenDefinitionContract $definition, string $text): LineBreaks
    {
        return $definition->hasLineBreaks()
            ? $this->lineBreaks->calculate($text)
            : $this->lineBreaks->default();
    }

    /**
     * @return \JPNut\Pearley\Lexer\Contracts\TokenDefinition
     */
    protected function errorTokenDefinition(): TokenDefinitionContract
    {
        return $this->errorTokenDefinition ??= TokenDefinition::initialise('error')
            ->withoutLineBreaks()
            ->shouldThrow()
            ->create();
    }

    /**
     * @param array $matches
     *
     * @return \JPNut\Pearley\Lexer\Contracts\TokenDefinition
     */
    protected function findMatchDefinition(array $matches): TokenDefinitionContract
    {
        array_shift($matches);

        foreach ($matches as $index => $match) {
            if (empty($match[0])) {
                continue;
            }

            return $this->config()->getTokenDefinition($index, $match[0]);
        }

        return $this->errorTokenDefinition();
    }

    /**
     * @param array $matches
     *
     * @return string
     */
    protected function getMatchText(array $matches): string
    {
        return $matches[0][0];
    }

    /**
     * @return string
     */
    protected function defaultConfigName(): string
    {
        return array_key_first($this->configs);
    }

    /**
     * @param string $getConfigName
     *
     * @return \JPNut\Pearley\Lexer\Contracts\LexerConfig
     */
    protected function getConfig(string $getConfigName): LexerConfigContract
    {
        return $this->configs[$getConfigName];
    }

    /**
     * @param \JPNut\Pearley\Lexer\Contracts\LexerConfig ...$additionalConfigs
     *
     * @return \JPNut\Pearley\Lexer\Contracts\LexerConfig[]
     */
    protected function setConfigs(LexerConfigContract ...$additionalConfigs): array
    {
        $configs = [];

        $references = [];

        foreach ($additionalConfigs as $config) {
            if (isset($configs[$name = $config->getName()])) {
                throw new InvalidArgumentException(
                    "Config name collision detected for name '{$name}'. All configs must have a unique name"
                );
            }

            $references = array_merge($references, $config->getOtherConfigs());

            $configs[$name] = $config;

            $this->token_names = array_merge($this->token_names, $config->getTokenNames());
        }

        foreach ($references as $config => $definition) {
            if (isset($configs[$config])) {
                continue;
            }

            throw new Exception(
                "Missing config '{$config}' in token definition '{$definition}'"
            );
        }

        return $configs;
    }

    /**
     * @return \JPNut\Pearley\Parser\Contracts\LexerState
     */
    public function getState(): BaseLexerStateContract
    {
        return $this->state;
    }
}
