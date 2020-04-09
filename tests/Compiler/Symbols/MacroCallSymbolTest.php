<?php

namespace JPNut\Pearley\Tests\Compiler\Symbols;

use JPNut\Pearley\Compiler\CompileResult;
use JPNut\Pearley\Compiler\CompileRule;
use JPNut\Pearley\Compiler\LanguageRule;
use JPNut\Pearley\Compiler\Symbols\MacroCallSymbol;
use JPNut\Pearley\Compiler\Symbols\StringSymbol;
use PHPUnit\Framework\TestCase;

class MacroCallSymbolTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_serialize()
    {
        $symbol = new MacroCallSymbol("inBrackets", [new LanguageRule([new StringSymbol("X")])]);

        $rule = new CompileRule("bar", []);

        $result = new CompileResult;

        $this->assertEquals("bar\$macrocall\$1", $symbol->serialize($rule, $result));
        $this->assertTrue($symbol->shouldWrap());
    }

    /**
     * @test
     */
    public function it_memoizes_serialized_value()
    {
        $symbol = new MacroCallSymbol("inBrackets", [new LanguageRule([new StringSymbol("foo")])]);

        $rule = new CompileRule("bar", []);

        $result = new CompileResult;

        $this->assertEquals("bar\$macrocall\$1", $symbol->serialize($rule, $result));
        $this->assertEquals("bar\$macrocall\$1", $symbol->serialize($rule, $result));
    }

    /**
     * @test
     */
    public function it_throws_if_macro_does_not_exist()
    {
        $this->expectExceptionMessage("Unknown macro: inBrackets");

        $symbol = new MacroCallSymbol("inBrackets", [new LanguageRule([new StringSymbol("X")])]);

        $rule = new CompileRule("bar", []);

        $result = new CompileResult;

        $symbol->generateCompileRules($rule, $result);
    }

    /**
     * @test
     */
    public function it_throws_if_wrong_number_of_arguments_supplied()
    {
        $this->expectExceptionMessage("Argument count mismatch for macro inBrackets: expected 1 but received 2.");

        $symbol = new MacroCallSymbol("inBrackets", [
            new LanguageRule([new StringSymbol("foo")]),
            new LanguageRule([new StringSymbol("bar")])
        ]);

        $rule = new CompileRule("bar", []);

        $result = new CompileResult;

        $result->addMacro("inBrackets", ["X"], []);

        $symbol->generateCompileRules($rule, $result);
    }

    /**
     * @test
     */
    public function it_can_generate_compile_rules()
    {
        $symbol = new MacroCallSymbol("inBrackets", [
            new LanguageRule([$foo_symbol = new StringSymbol("foo")]),
        ]);

        $rule = new CompileRule("bar", []);

        $result = new CompileResult;

        $result->addMacro("inBrackets", ["X"], [
            new LanguageRule([$baz_symbol = new StringSymbol("baz")]),
        ]);

        $rules = $symbol->generateCompileRules($rule, $result);

        $this->assertEquals("bar\$macrocall\$2", $rules[0]->getName());
        $this->assertCount(1, $rules[0]->getSymbols());
        $this->assertSame($foo_symbol, $rules[0]->getSymbols()[0]);
        $this->assertNull($rules[0]->getPostprocess());

        $this->assertEquals("bar\$macrocall\$1", $rules[1]->getName());
        $this->assertCount(1, $rules[1]->getSymbols());
        $this->assertSame($baz_symbol, $rules[1]->getSymbols()[0]);
        $this->assertNull($rules[1]->getPostprocess());
    }
}