<?php

namespace JPNut\Pearley\Tests\Lexer;

use JPNut\Pearley\Lexer\Lexer;
use JPNut\Pearley\Lexer\LexerConfig;
use JPNut\Pearley\Lexer\TokenDefinition;
use PHPUnit\Framework\TestCase;

class LexerTest extends TestCase
{
    /**
     * @test
     */
    public function it_handles_empty_rule_set()
    {
        $this->expectExceptionMessage('invalid syntax');

        $lexer = new Lexer(
            new LexerConfig(
                'foo',
                []
            )
        );

        $lexer->reset('nope!');

        $lexer->next();
    }

    /**
     * @test
     * @dataProvider missingConfigs
     *
     * @param \JPNut\Pearley\Lexer\LexerConfig $config
     */
    public function it_throws_if_missing_configs(LexerConfig $config)
    {
        $this->expectExceptionMessage("Missing config 'missing' in token definition 'thing1'");

        new Lexer($config);
    }

    /**
     * @test
     */
    public function it_throws_if_config_name_collision()
    {
        $this->expectExceptionMessage("Config name collision detected for name 'foo'. All configs must have a unique name");

        new Lexer(
            new LexerConfig(
                'foo',
                []
            ),
            new LexerConfig(
                'foo',
                []
            )
        );
    }

    /**
     * @test
     */
    public function it_throws_if_regex_skips_characters()
    {
        $this->expectExceptionMessage('invalid syntax');

        $lexer = new Lexer(
            new LexerConfig(
                'foo',
                [
                    'word'  => '[a-z]+',
                    'space' => '[ ]+',
                ]
            )
        );

        $lexer->reset('foo bar123baz');

        $this->assertEquals('foo', $lexer->next()->getValue());
        $this->assertEquals(' ', $lexer->next()->getValue());
        $this->assertEquals('bar', $lexer->next()->getValue());
        $lexer->next();
    }

    /**
     * @test
     */
    public function it_can_display_useful_error()
    {
        $this->expectExceptionMessage("invalid syntax at line 2 col 6:\n \n   bar 123\n       ^");

        $lexer = new Lexer(
            new LexerConfig(
                'foo',
                [
                    'word' => '[a-z]+',
                    TokenDefinition::initialise('ws', '[\s]+')
                        ->withLineBreaks()
                        ->create(),
                ]
            )
        );

        $lexer->reset("foo \n bar 123");

        $this->assertEquals('foo', $lexer->next()->getValue());
        $this->assertEquals(" \n ", $lexer->next()->getValue());
        $this->assertEquals('bar', $lexer->next()->getValue());
        $this->assertEquals(' ', $lexer->next()->getValue());
        $lexer->next();
    }

    /**
     * @test
     */
    public function it_can_display_useful_error_for_single_line()
    {
        $this->expectExceptionMessage("invalid syntax at line 1 col 5:\n \n  foo 123 bar\n      ^");

        $lexer = new Lexer(
            new LexerConfig(
                'foo',
                [
                    'word' => '[a-z]+',
                    TokenDefinition::initialise('ws', '[\s]+')
                        ->withLineBreaks()
                        ->create(),
                ]
            )
        );

        $lexer->reset('foo 123 bar');

        $this->assertEquals('foo', $lexer->next()->getValue());
        $this->assertEquals(' ', $lexer->next()->getValue());
        $lexer->next();
    }

    /**
     * @test
     */
    public function it_can_format_error_without_token()
    {
        $lexer = new Lexer(
            new LexerConfig(
                'foo',
                [
                    'word' => '[a-z]+',
                    TokenDefinition::initialise('ws', '[\s]+')
                        ->withLineBreaks()
                        ->create(),
                ]
            )
        );

        $lexer->reset('foo 123 bar');

        $lexer->next();

        $this->assertEquals("Error at line 1 col 4:\n \n  foo 123 bar\n     ^", $lexer->formatError());
    }

    /**
     * @test
     */
    public function it_can_match_basic_regex()
    {
        $lexer = new Lexer(
            new LexerConfig(
                'start',
                [
                    TokenDefinition::initialise('word', '[a-z]+')
                        ->create(),
                    'number' => '[0-9]+',
                    'space'  => '[ ]+',
                ]
            )
        );

        $lexer->reset('ducks are 123 bad');

        $results = [
            [
                'type'  => 'word',
                'value' => 'ducks',
            ],
            [
                'type'  => 'space',
                'value' => ' ',
            ],
            [
                'type'  => 'word',
                'value' => 'are',
            ],
            [
                'type'  => 'space',
                'value' => ' ',
            ],
            [
                'type'  => 'number',
                'value' => '123',
            ],
            [
                'type'  => 'space',
                'value' => ' ',
            ],
            [
                'type'  => 'word',
                'value' => 'bad',
            ],
        ];

        foreach ($results as $result) {
            $next = $lexer->next();

            $this->assertEquals($result['type'], $next->getType());
            $this->assertEquals($result['value'], $next->getValue());
        }
    }

    /**
     * @test
     */
    public function it_can_check_for_token_name()
    {
        $lexer = new Lexer(
            new LexerConfig(
                'start',
                [
                    'number' => '[0-9]+',
                ]
            ),
            new LexerConfig(
                'foo',
                [
                    'space' => '[ ]+',
                ]
            )
        );

        $this->assertTrue($lexer->has('number'));
        $this->assertTrue($lexer->has('space'));
    }

    /**
     * @test
     */
    public function it_can_reset_multiple_times()
    {
        $lexer = new Lexer(
            new LexerConfig(
                'start',
                [
                    'word'  => '[a-z]+',
                    'space' => '[ ]+',
                ]
            ),
        );

        $lexer->reset('hello world');

        $this->assertEquals('hello', $lexer->next()->getValue());
        $this->assertEquals(' ', $lexer->next()->getValue());

        $lexer->reset('foo bar');

        $this->assertEquals('foo', $lexer->next()->getValue());
    }

    /**
     * @test
     */
    public function it_can_reset_from_previous_state()
    {
        $lexer = new Lexer(
            new LexerConfig(
                'start',
                [
                    'word' => '[a-z]+',
                    TokenDefinition::initialise('ws', '\s+')
                        ->withLineBreaks()
                        ->create(),
                ]
            ),
        );

        $string = "foo\nbar\nbaz";

        $lexer->reset($string);

        $this->assertEquals('foo', $lexer->next()->getValue());
        $this->assertEquals("\n", $lexer->next()->getValue());

        $state = $lexer->save();

        $this->assertEquals(2, $state->getLine());

        $this->assertEquals('bar', $lexer->next()->getValue());
        $this->assertEquals("\n", $lexer->next()->getValue());

        $this->assertEquals(3, $lexer->save()->getLine());

        $lexer->reset($string, $state);

        $next = $lexer->next();

        $this->assertEquals('foo', $next->getValue());
        $this->assertEquals(2, $next->getLine());
    }

    /**
     * @test
     */
    public function it_returns_null_at_end_of_buffer()
    {
        $lexer = new Lexer(
            new LexerConfig(
                'start',
                [
                    'word'  => '[a-z]+',
                    'space' => '[ ]+',
                ]
            ),
        );

        $lexer->reset('hello world');

        $this->assertEquals('hello', $lexer->next()->getValue());
        $this->assertEquals(' ', $lexer->next()->getValue());
        $this->assertEquals('world', $lexer->next()->getValue());
        $this->assertNull($lexer->next());
    }

    /**
     * @test
     */
    public function it_can_go_between_states()
    {
        $lexer = new Lexer(
            new LexerConfig(
                'main',
                [
                    TokenDefinition::initialise('word', '[a-z]+')
                        ->shouldPushTo('after_word')
                        ->create(),
                    TokenDefinition::initialise('open_paren', '\(')
                        ->withNext('parens')
                        ->create(),
                ]
            ),
            new LexerConfig(
                'after_word',
                [
                    TokenDefinition::initialise('space', '[ ]+')
                        ->shouldPop()
                        ->create(),
                ]
            ),
            new LexerConfig(
                'parens',
                [
                    TokenDefinition::initialise('number', '[0-9]+')
                        ->create(),
                    TokenDefinition::initialise('close_paren', '\)')
                        ->withNext('after_parens')
                        ->create(),
                ]
            ),
            new LexerConfig(
                'after_parens',
                [
                    TokenDefinition::initialise('space', '[ ]+')
                        ->withNext('main')
                        ->create(),
                ]
            ),
        );

        $lexer->reset('foo (123) bar baz');

        $results = ['foo', ' ', '(', '123', ')', ' ', 'bar', ' ', 'baz'];

        foreach ($results as $result) {
            $this->assertEquals($result, $lexer->next()->getValue());
        }
    }

    /**
     * @test
     * @dataProvider keywords
     *
     * @param \JPNut\Pearley\Lexer\Lexer $lexer
     * @param string                     $value
     * @param string                     $type
     */
    public function it_can_handle_keywords(Lexer $lexer, string $value, string $type)
    {
        $next = $lexer->next();

        $this->assertEquals($value, $next->getValue());
        $this->assertEquals($type, $next->getType());
    }

    /**
     * @test
     */
    public function it_can_map_values()
    {
        $lexer = new Lexer(
            new LexerConfig(
                'start',
                [
                    'word' => '[a-z]+',
                    TokenDefinition::initialise('space', '[ ]+')
                        ->withValueMap(fn () => ' space ')
                        ->create(),
                ]
            ),
        );

        $lexer->reset('hello world');

        $this->assertEquals('hello', $lexer->next()->getValue());
        $this->assertEquals(' space ', $lexer->next()->getValue());
        $this->assertEquals('world', $lexer->next()->getValue());
    }

    /**
     * @return \JPNut\Pearley\Lexer\LexerConfig[][]
     */
    public function missingConfigs()
    {
        return [
            [
                new LexerConfig(
                    'start',
                    [
                        TokenDefinition::initialise('thing1', '=')
                            ->withNext('missing')
                            ->create(),
                    ]
                ),
            ],
            [
                new LexerConfig(
                    'start',
                    [
                        TokenDefinition::initialise('thing1', '=')
                            ->shouldPushTo('missing')
                            ->create(),
                    ]
                ),
            ],
        ];
    }

    /**
     * @return mixed[][]
     */
    public function keywords()
    {
        $lexer = new Lexer(
            new LexerConfig(
                'main',
                [
                    TokenDefinition::initialise('word', '[a-z]+')
                        ->withKeywords(['foo', 'bar', 'baz'])
                        ->create(),
                    TokenDefinition::initialise('ws', '[\s]+')
                        ->withLineBreaks()
                        ->create(),
                ]
            )
        );

        $lexer->reset('foo bar baz other');

        return [
            [
                $lexer,
                'foo',
                'foo',
            ],
            [
                $lexer,
                ' ',
                'ws',
            ],
            [
                $lexer,
                'bar',
                'bar',
            ],
            [
                $lexer,
                ' ',
                'ws',
            ],
            [
                $lexer,
                'baz',
                'baz',
            ],
            [
                $lexer,
                ' ',
                'ws',
            ],
            [
                $lexer,
                'other',
                'word',
            ],
        ];
    }
}
