<?php

namespace JPNut\Pearley\Tests\Lexer;

use JPNut\Pearley\Lexer\LexerConfig;
use JPNut\Pearley\Lexer\LexerRegex;
use JPNut\Pearley\Lexer\TokenDefinition;
use PHPUnit\Framework\TestCase;

class LexerConfigTest extends TestCase
{
    /**
     * @test
     */
    public function it_throws_if_duplicate_token_definition_name()
    {
        $this->expectExceptionMessage("Duplicate Token Definition Name detected: foo");

        new LexerConfig(
            'main',
            [
                TokenDefinition::initialise('foo', 'foo')
                    ->create(),
                TokenDefinition::initialise('foo', 'foo')
                    ->create(),
            ]
        );
    }

    /**
     * @test
     */
    public function it_throws_if_pending_token_definition_used()
    {
        $this->expectExceptionMessage("Cannot use Pending Token Definition as Token Definition. Make sure to call the \"create\" method after initialising.");

        new LexerConfig(
            'main',
            [
                TokenDefinition::initialise('foo', 'foo'),
            ]
        );
    }

    /**
     * @test
     */
    public function it_throws_if_invalid_regex()
    {
        $this->expectWarning();
        $this->expectWarningMessage("Unknown modifier 'b'");

        $config = new LexerConfig(
            'main',
            [
                TokenDefinition::initialise('foo', 'foo/bar')
                    ->create(),
            ]
        );

        $config->getRegex();
    }

    /**
     * @test
     */
    public function it_throws_if_regex_matches_empty_string()
    {
        $this->expectExceptionMessage("RegEx matches empty string: (?:(?:.?))");

        $config = new LexerConfig(
            'main',
            [
                TokenDefinition::initialise('foo', '.?')
                    ->create(),
            ]
        );

        $config->getRegex();
    }

    /**
     * @test
     */
    public function it_throws_if_regex_has_groups()
    {
        $this->expectExceptionMessage("RegEx has capture groups: (?:(?:foo(bar))). Use (?:...) instead");

        $config = new LexerConfig(
            'main',
            [
                TokenDefinition::initialise('foo', 'foo(bar)')
                    ->create(),
            ]
        );

        $config->getRegex();
    }

    /**
     * @test
     */
    public function it_throws_if_definition_should_define_line_breaks()
    {
        $this->expectExceptionMessage("Definition should declare line breaks: (?:(?:\s+))");

        $config = new LexerConfig(
            'main',
            [
                TokenDefinition::initialise('foo', "\s+")
                    ->create(),
            ]
        );

        $config->getRegex();
    }

    /**
     * @test
     */
    public function it_memoises_regex_object()
    {
        $config = new LexerConfig(
            'main',
            [
                TokenDefinition::initialise('foo', "foo")
                    ->create(),
            ]
        );

        $this->assertSame($config->getRegex(), $config->getRegex());
    }
}