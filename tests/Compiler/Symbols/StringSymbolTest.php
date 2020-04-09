<?php

namespace JPNut\Pearley\Tests\Compiler\Symbols;

use JPNut\Pearley\Compiler\CompileResult;
use JPNut\Pearley\Compiler\CompileRule;
use JPNut\Pearley\Compiler\Symbols\StringSymbol;
use PHPUnit\Framework\TestCase;

class StringSymbolTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_serialize()
    {
        $symbol = new StringSymbol("foo");

        $rule = new CompileRule("bar", []);

        $result = new CompileResult();

        $this->assertEquals("foo", $symbol->serialize($rule, $result));
        $this->assertTrue($symbol->shouldWrap());
    }

    /**
     * @test
     */
    public function it_serializes_null_string_as_null()
    {
        $symbol = new StringSymbol("null");

        $rule = new CompileRule("bar", []);

        $result = new CompileResult;

        $this->assertNull($symbol->serialize($rule, $result));
        $this->assertFalse($symbol->shouldWrap());
    }
}