<?php

namespace JPNut\Pearley\Tests\Parser;

use JPNut\Pearley\Parser\Parser;
use JPNut\Pearley\Parser\ParserConfig;
use JPNut\Pearley\Tests\Grammars\TestGrammar1;
use JPNut\Pearley\Tests\Grammars\TestGrammar2;
use JPNut\Pearley\Tests\Grammars\TestGrammar3;
use JPNut\Pearley\Tests\Grammars\TestGrammar4;
use JPNut\Pearley\Tests\Grammars\TestGrammar5;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    /**
     * @test
     */
    public function it_shows_line_number_in_errors()
    {
        $parser = new Parser(
            new ParserConfig(
                TestGrammar1::grammar()
            )
        );

        $this->expectExceptionMessage("line 2 col 3:\n \n  12!\n");

        $parser->feed("abc\n12!");
    }

    /**
     * @test
     */
    public function it_shows_useful_error_with_state_stack_info()
    {
        $parser = new Parser(
            new ParserConfig(
                TestGrammar1::grammar(),
            )
        );

        $expectedError = implode("\n", [
            'Syntax error at line 2 col 3:',
            ' ',
            '  12!',
            '    ^',
            'Unexpected "!". Instead, I was expecting to see one of the following:',
            '',
            'A character matching [a-z0-9] based on:',
            '    x →  ●  [a-z0-9]',
            '    y$ebnf$1 → y$ebnf$1  ●  x',
            '    y →  ●  y$ebnf$1',
            'A "\\n" based on:',
            '    x →  ●  "\\n"',
            '    y$ebnf$1 → y$ebnf$1  ●  x',
            '    y →  ●  y$ebnf$1',
            '',
        ]);

        $this->expectExceptionMessage($expectedError);

        $parser->feed("abc\n12!");
    }

    /**
     * @test
     */
    public function it_collapses_identical_consecutive_lines()
    {
        $parser = new Parser(
            new ParserConfig(
                TestGrammar2::grammar()
            )
        );

        $this->expectExceptionMessage("ws → wsc  ●  ws\n    ↑ ︎3 more lines identical to this");

        $parser->feed('    b');
    }

    /**
     * @test
     */
    public function it_does_not_infinitely_recurse_on_self_referential_states()
    {
        $parser = new Parser(
            new ParserConfig(
                TestGrammar3::grammar()
            )
        );

        $this->expectExceptionMessage('Unexpected "b"');

        $parser->feed('    b');
    }

    /**
     * @test
     */
    public function it_can_save_state()
    {
        $parser = new Parser(
            new ParserConfig(
                TestGrammar4::grammar(),
                null,
                true
            )
        );

        $first = 'this is foo';

        $parser->feed($first);

        $this->assertEquals(11, $parser->getCurrent());
        $this->assertCount(12, $parser->getTable());

        $col = $parser->save();

        $this->assertEquals(11, $col->getIndex());
        $this->assertEquals(11, $col->getLexerState()->getCol());
    }

    /**
     * @test
     */
    public function it_can_rewind()
    {
        $parser = new Parser(
            new ParserConfig(
                TestGrammar4::grammar(),
                null,
                true
            )
        );

        $first = 'this is foo';
        $second = ' and this is bar';

        $parser->feed($first);

        $this->assertEquals(11, $parser->getCurrent());
        $this->assertCount(12, $parser->getTable());

        $parser->feed($second);

        $parser->rewind(strlen($first));

        $this->assertEquals(11, $parser->getCurrent());
        $this->assertCount(12, $parser->getTable());

        $this->assertEquals(['this is foo'], $parser->getResults());
    }

    /**
     * @test
     */
    public function it_wont_rewind_without_keep_history_option()
    {
        $this->expectExceptionMessage('Enable history to enable rewinding');

        $parser = new Parser(
            new ParserConfig(
                TestGrammar4::grammar(),
                null,
                false
            )
        );

        $parser->rewind(0);
    }

    /**
     * @test
     */
    public function it_restores_line_numbers()
    {
        $parser = new Parser(
            new ParserConfig(
                TestGrammar1::grammar(),
            )
        );

        $parser->feed("abc\n");

        $this->assertEquals(2, ($foo = $parser->save())->getLexerState()->getLine());

        $parser->feed("1234\n");

        $col = $parser->save();

        $this->assertEquals(3, $col->getLexerState()->getLine());

        $parser->feed('q');

        $parser->restore($col);

        $this->assertEquals(3, $parser->getLexerState()->getLine());
    }

    /**
     * @test
     */
    public function it_restores_column_number()
    {
        $parser = new Parser(
            new ParserConfig(
                TestGrammar1::grammar(),
            )
        );

        $parser->feed("foo\nbar");

        $col = $parser->save();

        $this->assertEquals(2, $col->getLexerState()->getLine());
        $this->assertEquals(3, $col->getLexerState()->getCol());

        $parser->feed('123');

        $this->assertEquals(6, $parser->getLexerState()->getCol());

        $parser->restore($col);

        $this->assertEquals(2, $parser->getLexerState()->getLine());
        $this->assertEquals(3, $parser->getLexerState()->getCol());

        $parser->feed('456');

        $this->assertEquals(6, $parser->getLexerState()->getCol());
    }

    /**
     * @test
     */
    public function it_can_parse_using_lexer()
    {
        $parser = new Parser(
            new ParserConfig(
                TestGrammar5::grammar()
            )
        );

        $parser->feed('this is foo');

        $this->assertEquals('this is foo', $parser->getResults()[0]);
    }

    /**
     * @test
     */
    public function it_throws_error_with_lexer_token()
    {
        $expectedError = implode("\n", [
            'Syntax error at line 1 col 2:',
            ' ',
            '  t1 error',
            '   ^',
            'Unexpected number token: "1". Instead, I was expecting to see one of the following:',
            '',
            'A ws token based on:',
            '    ws →  ●  %ws',
            '    blocks → blocks  ●  ws block',
            '    blocks →  ●  blocks ws block',
            '',
        ]);

        $this->expectExceptionMessage($expectedError);

        $parser = new Parser(
            new ParserConfig(
                TestGrammar5::grammar()
            )
        );

        $parser->feed('t1 error');
    }
}
