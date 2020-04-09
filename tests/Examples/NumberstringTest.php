<?php

namespace JPNut\Pearley\Tests\Examples;

use JPNut\Pearley\Examples\Numberstring;
use JPNut\Pearley\Parser\Parser;
use JPNut\Pearley\Parser\ParserConfig;
use PHPUnit\Framework\TestCase;

class NumberstringTest extends TestCase
{
    /**
     * @test
     * @dataProvider numberstrings
     * @param  string  $numberstring
     * @param  int  $number
     */
    public function it_can_parse_numberstring(string $numberstring, int $number)
    {
        $parser = new Parser(new ParserConfig(Numberstring::grammar()));

        $this->assertEquals($number, $parser->feed($numberstring)->getResults()[0]);
    }

    /**
     * @return array[]
     */
    public function numberstrings(): array
    {
        return [
            [
                "one",
                1
            ],
            [
                "twenty five",
                25
            ],
            [
                "ninety nine thousand four hundred five",
                99405
            ],
            [
                "nine billion eight hundred seventy six million five hundred fourty three thousand two hundred ten",
                9876543210
            ]
        ];
    }
}