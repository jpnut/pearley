<?php

namespace JPNut\Pearley\Lexer;

use Exception;
use InvalidArgumentException;
use JPNut\Pearley\Lexer\Contracts\LexerConfig as LexerConfigContract;
use JPNut\Pearley\Lexer\Contracts\TokenDefinition as TokenDefinitionContract;
use JPNut\Pearley\Parser\Contracts\LexerRegex as LexerRegexContract;

class LexerConfig implements LexerConfigContract
{
    /**
     * @var string
     */
    protected string $name;

    /**
     * @var \JPNut\Pearley\Lexer\Contracts\TokenDefinition[]
     */
    protected array $definitions = [];

    /**
     * @var array
     */
    protected array $names = [];

    /**
     * @var string[]
     */
    protected array $newDefinitions = [];

    /**
     * @var \JPNut\Pearley\Parser\Contracts\LexerRegex
     */
    protected LexerRegexContract $regex;

    /**
     * @var string[]
     */
    protected array $other_configs = [];

    /**
     * @param  string  $name
     * @param  array  $definitions
     */
    public function __construct(string $name, array $definitions)
    {
        $this->name = $name;

        foreach ($definitions as $key => $definition) {
            if ($definition instanceof TokenDefinition) {
                $this->addTokenDefinition($definition);

                continue;
            }

            if ($definition instanceof PendingTokenDefinition) {
                throw new InvalidArgumentException(
                    'Cannot use Pending Token Definition as Token Definition. Make sure to call the "create" method after initialising.'
                );
            }

            $this->addTokenDefinition($this->createTokenDefinition($key, $definition));
        }
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param  \JPNut\Pearley\Lexer\Contracts\TokenDefinition  $definition
     * @return void
     */
    public function addTokenDefinition(TokenDefinitionContract $definition): void
    {
        if (isset($this->names[$name = $definition->getName()])) {
            throw new Exception("Duplicate Token Definition Name detected: {$name}");
        }

        $this->newDefinitions[] = $name;
        $this->names[$name]     = true;
        $this->definitions[]    = $definition;

        if ($definition->shouldPush()) {
            $this->other_configs[$definition->getPush()] = $definition->getName();
        } elseif ($definition->hasNext()) {
            $this->other_configs[$definition->getNext()] = $definition->getName();
        }
    }

    /**
     * @return \JPNut\Pearley\Lexer\Contracts\TokenDefinition[]
     */
    public function getTokenDefinitions(): array
    {
        return $this->definitions;
    }

    /**
     * @param  int  $index
     * @param  string  $text
     * @return \JPNut\Pearley\Lexer\Contracts\TokenDefinition
     */
    public function getTokenDefinition(int $index, string $text): TokenDefinitionContract
    {
        return $this->definitions[$index]->getDefinitionFromText($text);
    }

    /**
     * @return bool[]
     */
    public function getTokenNames(): array
    {
        return $this->names;
    }

    /**
     * @return string[]
     */
    public function getOtherConfigs(): array
    {
        return $this->other_configs;
    }

    /**
     * @return \JPNut\Pearley\Parser\Contracts\LexerRegex
     */
    public function getRegex(): LexerRegexContract
    {
        if (empty($this->newDefinitions) && !is_null($this->regex ?? null)) {
            return $this->regex;
        }

        $this->newDefinitions = [];

        return $this->regex = $this->createRegex();
    }

    /**
     * @param $key
     * @param $definition
     * @return \JPNut\Pearley\Lexer\Contracts\TokenDefinition
     */
    protected function createTokenDefinition($key, $definition): TokenDefinitionContract
    {
        return TokenDefinition::initialise(strval($key), is_string($definition) ? $definition : null)
            ->create();
    }

    /**
     * @return \JPNut\Pearley\Parser\Contracts\LexerRegex
     */
    protected function createRegex(): LexerRegexContract
    {
        return new LexerRegex($this->formatPattern($this->constructSinglePattern()));
    }

    /**
     * @return string
     */
    protected function constructSinglePattern(): string
    {
        $patterns = [];

        foreach ($this->definitions as $definition) {
            if (is_null($pattern = $definition->getPattern())) {
                continue;
            }

            $patterns[] = $this->validatePattern($definition, $pattern = $this->regexUnion([$pattern]));
        }

        return empty($patterns) ? '(?!)' : $this->regexUnion($patterns);
    }

    /**
     * @param  string[]  $patterns
     * @return string
     */
    protected function regexUnion(array $patterns): string
    {
        return '(?:'.join('|', array_map(fn(string $pattern) => "(?:{$pattern})", $patterns)).')';
    }

    /**
     * @param  \JPNut\Pearley\Lexer\Contracts\TokenDefinition  $definition
     * @param  string  $pattern
     * @return string
     */
    protected function validatePattern(TokenDefinitionContract $definition, string $pattern): string
    {
        $usablePattern = $this->formatPattern($pattern);

        if (preg_match($usablePattern, null) === false) {
            throw new Exception("Invalid RegEx: {$pattern}");
        }

        if (preg_match($usablePattern, '') > 0) {
            throw new Exception("RegEx matches empty string: {$pattern}");
        }

        if ($this->regexGroupCount($pattern) > 0) {
            throw new Exception("RegEx has capture groups: {$pattern}. Use (?:...) instead");
        }

        if (!$definition->hasLineBreaks() && preg_match($usablePattern, "\n")) {
            throw new Exception("Definition should declare line breaks: {$pattern}");
        }


        return "({$pattern})";
    }

    /**
     * @param  string  $pattern
     * @return string
     */
    protected function formatPattern(string $pattern): string
    {
        return "/{$pattern}/";
    }

    /**
     * @param  string  $pattern
     * @return int
     */
    protected function regexGroupCount(string $pattern): int
    {
        preg_match_all($this->formatPattern("|{$pattern}"), '', $matches);

        return count($matches) - 1;
    }
}
