<?php

namespace JPNut\Pearley\Tests\Lexer;

use JPNut\Pearley\Lexer\Keyword;
use JPNut\Pearley\Lexer\TokenDefinition;
use PHPUnit\Framework\TestCase;

class TokenDefinitionTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_set_keywords()
    {
        $definition = new TokenDefinition(
            'token',
            "\w+",
            null,
            false,
            false,
            false,
            null,
            null,
            [
                'foo',
                'bar' => 'BAR',
                new Keyword('baz', 'baz'),
            ]
        );

        $this->assertEquals('foo', $definition->getDefinitionFromText('foo')->getName());
        $this->assertEquals('bar', $definition->getDefinitionFromText('BAR')->getName());
        $this->assertEquals('baz', $definition->getDefinitionFromText('baz')->getName());
        $this->assertEquals('token', $definition->getDefinitionFromText('other')->getName());
    }

    /**
     * @test
     */
    public function it_throws_if_invalid_keyword_provided()
    {
        $this->expectExceptionMessage('Invalid keyword definition: keyword must be an instance of '
            .Keyword::class.', a string, or an array of strings.');

        new TokenDefinition(
            'token',
            "\w+",
            null,
            false,
            false,
            false,
            null,
            null,
            [
                1,
            ]
        );
    }

    /**
     * @test
     */
    public function it_throws_if_keyword_name_not_provided()
    {
        $this->expectExceptionMessage('Invalid keyword definition: please provide a unique name for each keyword.');

        new TokenDefinition(
            'token',
            "\w+",
            null,
            false,
            false,
            false,
            null,
            null,
            [
                ['foo'],
            ]
        );
    }

    /**
     * @test
     */
    public function it_throws_if_duplicate_keyword_name_provided()
    {
        $this->expectExceptionMessage("Duplicate keyword definition detected for 'foo'");

        new TokenDefinition(
            'token',
            "\w+",
            null,
            false,
            false,
            false,
            null,
            null,
            [
                'foo',
                'foo',
            ]
        );
    }

    /**
     * @test
     */
    public function it_throws_if_duplicate_keyword_provided()
    {
        $this->expectExceptionMessage("Duplicate keyword detected for 'foo'");

        new TokenDefinition(
            'token',
            "\w+",
            null,
            false,
            false,
            false,
            null,
            null,
            [
                'foo',
                'bar' => 'foo',
            ]
        );
    }
}
