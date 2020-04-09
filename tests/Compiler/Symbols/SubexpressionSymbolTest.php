<?php

namespace JPNut\Pearley\Tests\Compiler\Symbols;

use JPNut\Pearley\Compiler\CompileResult;
use JPNut\Pearley\Compiler\CompileRule;
use JPNut\Pearley\Compiler\LanguageRule;
use JPNut\Pearley\Compiler\Symbols\StringSymbol;
use JPNut\Pearley\Compiler\Symbols\SubexpressionSymbol;
use PHPUnit\Framework\TestCase;

class SubexpressionSymbolTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_serialize()
    {
        $symbol = new SubexpressionSymbol([new LanguageRule([])]);

        $rule = new CompileRule("bar", []);

        $result = new CompileResult;

        $this->assertEquals("bar\$subexpression\$1", $symbol->serialize($rule, $result));
        $this->assertTrue($symbol->shouldWrap());
    }

    /**
     * @test
     */
    public function it_memoizes_serialized_value()
    {
        $symbol = new SubexpressionSymbol([new LanguageRule([])]);

        $rule = new CompileRule("bar", []);

        $result = new CompileResult;

        $this->assertEquals("bar\$subexpression\$1", $symbol->serialize($rule, $result));
        $this->assertEquals("bar\$subexpression\$1", $symbol->serialize($rule, $result));
    }

    /**
     * @test
     */
    public function it_can_generate_compile_rules()
    {
        $symbol = new SubexpressionSymbol([new LanguageRule([$string = new StringSymbol("foo")])]);

        $rule = new CompileRule("bar", []);

        $result = new CompileResult;

        $rules = $symbol->generateCompileRules($rule, $result);

        $this->assertEquals("bar\$subexpression\$1", $rules[0]->getName());
        $this->assertCount(1, $rules[0]->getSymbols());
        $this->assertSame($string, $rules[0]->getSymbols()[0]);
        $this->assertNull($rules[0]->getPostprocess());
    }
}