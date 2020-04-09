<?php

namespace JPNut\Pearley\Tests\Lexer;

use JPNut\Pearley\Lexer\LexerRegex;
use PHPUnit\Framework\TestCase;

class LexerRegexTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_match_with_offset()
    {
        $regex = new LexerRegex('/bar/');

        $matches = $regex->match('foobarbaz');

        $this->assertEquals('bar', $matches[0][0]);
        $this->assertEquals(3, $matches[0][1]);
    }

    /**
     * @test
     */
    public function it_maintains_position_for_multiple_matches()
    {
        $regex = new LexerRegex('/bar/');

        $string = 'foobarbazbar';

        $matches = $regex->match($string);

        $this->assertEquals('bar', $matches[0][0]);
        $this->assertEquals(3, $matches[0][1]);

        $this->assertEquals(4, $regex->getLastIndex());

        $matches = $regex->match($string);

        $this->assertEquals('bar', $matches[0][0]);
        $this->assertEquals(5, $matches[0][1]);

        $this->assertEquals(10, $regex->getLastIndex());
    }
}