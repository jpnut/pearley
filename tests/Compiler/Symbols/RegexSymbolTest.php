<?php

namespace JPNut\Pearley\Tests\Compiler\Symbols;

use JPNut\Pearley\Compiler\CompileResult;
use JPNut\Pearley\Compiler\CompileRule;
use JPNut\Pearley\Compiler\Symbols\RegexSymbol;
use JPNut\Pearley\Parser\RegExp;
use PHPUnit\Framework\TestCase;

class RegexSymbolTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_serialize()
    {
        $symbol = new RegexSymbol(new RegExp("foo"));

        $rule = new CompileRule("bar", []);

        $result = new CompileResult();

        $this->assertEquals("['value' => 'foo', 'type' => Symbol::REGEX]", $symbol->serialize($rule, $result));
        $this->assertFalse($symbol->shouldWrap());
    }
}