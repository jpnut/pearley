<?php

namespace JPNut\Pearley\Tests\Parser;

use JPNut\Pearley\Parser\RegExp;
use PHPUnit\Framework\TestCase;

class RegExpTest extends TestCase
{
    /**
     * @test
     * @dataProvider regexPatterns
     *
     * @param string $input
     * @param string $pattern
     * @param string $qualifiedPattern
     */
    public function it_can_trim_pattern(string $input, string $pattern, string $qualifiedPattern)
    {
        $regexp = new RegExp($input);

        $this->assertEquals($pattern, $regexp->getPattern());
        $this->assertEquals($qualifiedPattern, $regexp->getQualifiedPattern());
    }

    /**
     * @return string[][]
     */
    public function regexPatterns()
    {
        return [
            [
                'foo',
                'foo',
                '/foo/',
            ],
            [
                '/foo/',
                '/foo/',
                '//foo//',
            ],
            [
                '/foo',
                '/foo',
                '//foo/',
            ],
            [
                'foo/',
                'foo/',
                '/foo//',
            ],
            [
                "\/foo\/",
                "\/foo\/",
                "/\/foo\//",
            ],
        ];
    }
}
