<?php

namespace JPNut\Pearley\Tests\Examples;

use JPNut\Pearley\Examples\Json;
use JPNut\Pearley\Parser\Parser;
use JPNut\Pearley\Parser\ParserConfig;
use PHPUnit\Framework\TestCase;

class JsonTest extends TestCase
{
    /**
     * @test
     * @dataProvider jsons
     * @param  string  $json
     * @param  array  $expected
     */
    public function it_can_parse_json(string $json, array $expected)
    {
        $parser = new Parser(new ParserConfig(Json::grammar()));

        $this->assertEquals($expected, $parser->feed($json)->getResults()[0]);
    }

    /**
     * @return array[]
     */
    public function jsons(): array
    {
        return [
            [
                "{\"foo\": \"bar\"}",
                ["foo" => "bar"]
            ],
            [
                json_encode(["foo" => "bar"]),
                ["foo" => "bar"]
            ],
            [
                json_encode(["foo" => [1,2,3], "bar" => ["baz" => null]]),
                ["foo" => [1,2,3], "bar" => ["baz" => null]]
            ]
        ];
    }
}