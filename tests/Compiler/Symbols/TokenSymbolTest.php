<?php

namespace JPNut\Pearley\Tests\Compiler\Symbols;

use JPNut\Pearley\Compiler\CompileResult;
use JPNut\Pearley\Compiler\CompileRule;
use JPNut\Pearley\Compiler\Symbols\TokenSymbol;
use PHPUnit\Framework\TestCase;

class TokenSymbolTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_serialize()
    {
        $symbol = new TokenSymbol("foo");

        $rule = new CompileRule("bar", []);

        $result = new CompileResult;

        $this->assertEquals("foo", $symbol->serialize($rule, $result));
        $this->assertTrue($symbol->shouldWrap());
    }

    /**
     * @test
     */
    public function it_can_serialize_with_lexer()
    {
        $symbol = new TokenSymbol("foo");

        $rule = new CompileRule("bar", []);

        $result = new CompileResult;
        $result->addConfig("lexer", "lexer");

        $this->assertEquals("['value' => 'foo', 'type' => Symbol::TOKEN]", $symbol->serialize($rule, $result));
        $this->assertFalse($symbol->shouldWrap());
    }
}