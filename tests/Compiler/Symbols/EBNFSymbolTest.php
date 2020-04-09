<?php

namespace JPNut\Pearley\Tests\Compiler\Symbols;

use JPNut\Pearley\Compiler\CompileResult;
use JPNut\Pearley\Compiler\CompileRule;
use JPNut\Pearley\Compiler\Symbols\EBNFSymbol;
use JPNut\Pearley\Compiler\Symbols\StringSymbol;
use PHPUnit\Framework\TestCase;

class EBNFSymbolTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_serialize()
    {
        $symbol = new EBNFSymbol(":+", new StringSymbol("foo"));

        $rule = new CompileRule("bar", []);

        $result = new CompileResult;

        $this->assertEquals("bar\$ebnf\$1", $symbol->serialize($rule, $result));
        $this->assertTrue($symbol->shouldWrap());
    }

    /**
     * @test
     */
    public function it_memoizes_serialized_value()
    {
        $symbol = new EBNFSymbol(":+", new StringSymbol("foo"));

        $rule = new CompileRule("bar", []);

        $result = new CompileResult;

        $this->assertEquals("bar\$ebnf\$1", $symbol->serialize($rule, $result));
        $this->assertEquals("bar\$ebnf\$1", $symbol->serialize($rule, $result));
    }

    /**
     * @test
     */
    public function it_throws_if_unrecognised_ebnf_token()
    {
        $this->expectExceptionMessage("Unrecognised EBNF token ':!'.");

        $symbol = new EBNFSymbol(":!", $string = new StringSymbol("foo"));

        $rule = new CompileRule("bar", []);

        $result = new CompileResult;

        $symbol->generateCompileRules($rule, $result);
    }

    /**
     * @test
     */
    public function it_can_generate_compile_rules_for_plus()
    {
        $symbol = new EBNFSymbol(":+", $string = new StringSymbol("foo"));

        $rule = new CompileRule("bar", []);

        $result = new CompileResult;

        $rules = $symbol->generateCompileRules($rule, $result);

        $this->assertEquals("bar\$ebnf\$1", $rules[0]->getName());
        $this->assertCount(1, $rules[0]->getSymbols());
        $this->assertSame($string, $rules[0]->getSymbols()[0]);
        $this->assertNull($rules[0]->getPostprocess());

        $this->assertEquals("bar\$ebnf\$1", $rules[1]->getName());
        $this->assertCount(2, $rules[1]->getSymbols());
        $this->assertInstanceOf(StringSymbol::class, $rules[1]->getSymbols()[0]);
        $this->assertEquals("bar\$ebnf\$1", $rules[1]->getSymbols()[0]->getString());
        $this->assertSame($string, $rules[1]->getSymbols()[1]);
        $this->assertNotNull($rules[1]->getPostprocess());
        $this->assertEquals("function (\$d) {return [...\$d[0], \$d[1]]; }", $rules[1]->getPostprocess()->getValue());
    }

    /**
     * @test
     */
    public function it_can_generate_compile_rules_for_star()
    {
        $symbol = new EBNFSymbol(":*", $string = new StringSymbol("foo"));

        $rule = new CompileRule("bar", []);

        $result = new CompileResult;

        $rules = $symbol->generateCompileRules($rule, $result);

        $this->assertEquals("bar\$ebnf\$1", $rules[0]->getName());
        $this->assertEmpty($rules[0]->getSymbols());
        $this->assertNull($rules[0]->getPostprocess());

        $this->assertEquals("bar\$ebnf\$1", $rules[1]->getName());
        $this->assertCount(2, $rules[1]->getSymbols());
        $this->assertInstanceOf(StringSymbol::class, $rules[1]->getSymbols()[0]);
        $this->assertEquals("bar\$ebnf\$1", $rules[1]->getSymbols()[0]->getString());
        $this->assertSame($string, $rules[1]->getSymbols()[1]);
        $this->assertNotNull($rules[1]->getPostprocess());
        $this->assertEquals("function (\$d) {return [...\$d[0], \$d[1]]; }", $rules[1]->getPostprocess()->getValue());
    }

    /**
     * @test
     */
    public function it_can_generate_compile_rules_for_opt()
    {
        $symbol = new EBNFSymbol(":?", $string = new StringSymbol("foo"));

        $rule = new CompileRule("bar", []);

        $result = new CompileResult;

        $rules = $symbol->generateCompileRules($rule, $result);

        $this->assertEquals("bar\$ebnf\$1", $rules[0]->getName());
        $this->assertCount(1, $rules[0]->getSymbols());
        $this->assertSame($string, $rules[0]->getSymbols()[0]);
        $this->assertNotNull($rules[0]->getPostprocess());
        $this->assertEquals("\$id", $rules[0]->getPostprocess()->getValue());

        $this->assertEquals("bar\$ebnf\$1", $rules[1]->getName());
        $this->assertEmpty($rules[1]->getSymbols());
        $this->assertNotNull($rules[1]->getPostprocess());
        $this->assertEquals("function (\$d) { return null; }", $rules[1]->getPostprocess()->getValue());
    }
}