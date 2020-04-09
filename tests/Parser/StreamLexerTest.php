<?php

namespace JPNut\Pearley\Tests\Parser;

use JPNut\Pearley\Parser\StreamLexer;
use PHPUnit\Framework\TestCase;

class StreamLexerTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_tokenize_string()
    {
        $lexer = new StreamLexer();

        $lexer->reset("foo\nbar");

        $expected = [
            ["f", 0, 0, 1, 0],
            ["o", 1, 0, 1, 1],
            ["o", 2, 0, 1, 2],
            ["\n", 3, 1, 1, 3],
            ["b", 4, 0, 2, 0],
            ["a", 5, 0, 2, 1],
            ["r", 6, 0, 2, 2],
        ];

        $actual = [];

        foreach ($expected as $element) {
            $token = $lexer->next();

            $actual[] = [
                $token->getText(),
                $token->getOffset(),
                $token->getLineBreaks(),
                $token->getLine(),
                $token->getCol(),
            ];
        }

        $this->assertSame($expected, $actual);

        $state = $lexer->save();

        $this->assertEquals(7, $state->getIndex());
        $this->assertEquals(2, $state->getLine());
        $this->assertEquals(4, $state->getLastLineBreak());
        $this->assertEquals(3, $state->getCol());
    }

    /**
     * @test
     */
    public function it_can_reset_multiple_times()
    {
        $lexer = new StreamLexer();

        $string1 = "foo\nbar";

        $lexer->reset($string1);

        for ($i = 0; $i < strlen($string1); $i++) {
            $lexer->next();
        }

        $string2 = "and\nbaz";

        $lexer->reset($string2);

        $expected = [
            ["a", 0, 0, 1, 0],
            ["n", 1, 0, 1, 1],
            ["d", 2, 0, 1, 2],
            ["\n", 3, 1, 1, 3],
            ["b", 4, 0, 2, 0],
            ["a", 5, 0, 2, 1],
            ["z", 6, 0, 2, 2],
        ];

        $actual = [];

        foreach ($expected as $element) {
            $token = $lexer->next();

            $actual[] = [
                $token->getText(),
                $token->getOffset(),
                $token->getLineBreaks(),
                $token->getLine(),
                $token->getCol(),
            ];
        }

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function it_can_reset_with_a_given_state()
    {
        $lexer = new StreamLexer();

        $string1 = "foo\nbar";

        $lexer->reset($string1);

        for ($i = 0; $i < strlen($string1); $i++) {
            $lexer->next();
        }

        $state1 = $lexer->save();

        $this->assertEquals(7, $state1->getIndex());
        $this->assertEquals(2, $state1->getLine());
        $this->assertEquals(4, $state1->getLastLineBreak());
        $this->assertEquals(3, $state1->getCol());

        $string2 = "and\nbaz";

        $lexer->reset($string2, $state1);

        $expected = [
            ["a", 0, 0, 2, 3],
            ["n", 1, 0, 2, 4],
            ["d", 2, 0, 2, 5],
            ["\n", 3, 1, 2, 6],
            ["b", 4, 0, 3, 0],
            ["a", 5, 0, 3, 1],
            ["z", 6, 0, 3, 2],
        ];

        $actual = [];

        foreach ($expected as $element) {
            $token = $lexer->next();

            $actual[] = [
                $token->getText(),
                $token->getOffset(),
                $token->getLineBreaks(),
                $token->getLine(),
                $token->getCol(),
            ];
        }

        $this->assertSame($expected, $actual);

        $state2 = $lexer->save();

        $this->assertEquals(7, $state2->getIndex());
        $this->assertEquals(3, $state2->getLine());
        $this->assertEquals(4, $state2->getLastLineBreak());
        $this->assertEquals(3, $state2->getCol());
    }
}