<?php

namespace JPNut\Pearley\Tests\Examples;

use JPNut\Pearley\Examples\Macros;
use JPNut\Pearley\Parser\Parser;
use JPNut\Pearley\Parser\ParserConfig;
use PHPUnit\Framework\TestCase;

class MacrosTest extends TestCase
{
    /**
     * @test
     * @dataProvider macros
     * @param  string  $macro
     * @param  string  $expected
     */
    public function it_can_parse_macro(string $macro, string $expected)
    {
        $parser = new Parser(new ParserConfig(Macros::grammar()));

        $this->assertEquals($expected, $parser->feed($macro)->getResults()[0]);
    }

    /**
     * @return array[]
     */
    public function macros(): array
    {
        return [
            [
                "Cows oink.",
                "Cows oink."
            ],
            [
                "Cows moo!",
                "Cows moo!",
            ],
            [
                "Cows baa.",
                "Cows baa.",
            ],
        ];
    }
}