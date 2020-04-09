<?php

namespace JPNut\Pearley\Tests\CLI;

use JPNut\Pearley\CLI\Arguments;
use PHPUnit\Framework\TestCase;

class ArgumentsTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_parse_arguments()
    {
        $arguments = new Arguments(
            'command',
            ['arg1' => 'Argument 1', 'arg2' => 'Argument 2'],
            [
                'opt1' => [
                    'short' => 'o1',
                    'long'  => 'option1',
                ],
                'opt2' => [
                    'short' => 'o2',
                    'long'  => 'option2',
                ],
                'opt3' => [
                    'short' => 'o3',
                    'long'  => 'option3',
                ],
                'opt4' => [
                    'short' => 'o4',
                    'long'  => 'option4',
                ],
            ]
        );

        $arguments->read(explode(' ', 'foo bar --option1 value1 -o2 value2 --option3=value3 -o4=value4'));

        $this->assertEquals([
            'foo',
            'bar',
        ], $arguments->getArguments());

        $this->assertEquals([
            'opt1' => 'value1',
            'opt2' => 'value2',
            'opt3' => 'value3',
            'opt4' => 'value4',
        ], $arguments->getOptions());
    }

    /**
     * @test
     */
    public function it_throws_if_not_enough_args()
    {
        $this->expectExceptionMessage("Expected to receive 2 argument(s) for command 'command' but received 1");

        $arguments = new Arguments(
            'command',
            ['arg1' => 'Argument 1', 'arg2' => 'Argument 2'],
        );

        $arguments->read(explode(' ', 'foo'));
    }

    /**
     * @test
     */
    public function it_throws_if_too_many_args()
    {
        $this->expectExceptionMessage("Too many arguments. Expected to receive 2 argument(s) for command 'command'.");

        $arguments = new Arguments(
            'command',
            ['arg1' => 'Argument 1', 'arg2' => 'Argument 2'],
        );

        $arguments->read(explode(' ', 'foo bar baz'));
    }
}
