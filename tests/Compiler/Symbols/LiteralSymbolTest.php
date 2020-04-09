<?php

namespace JPNut\Pearley\Tests\Compiler\Symbols;

use JPNut\Pearley\Compiler\CompileResult;
use JPNut\Pearley\Compiler\CompileRule;
use JPNut\Pearley\Compiler\Symbols\LiteralSymbol;
use PHPUnit\Framework\TestCase;

class LiteralSymbolTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_serialize()
    {
        $symbol = new LiteralSymbol("foo");

        $rule = new CompileRule("bar", []);

        $result = new CompileResult;

        $this->assertEquals("bar\$string\$1", $symbol->serialize($rule, $result));
        $this->assertTrue($symbol->shouldWrap());
    }

    /**
     * @test
     */
    public function it_memoizes_serialized_value()
    {
        $symbol = new LiteralSymbol("foo");

        $rule = new CompileRule("bar", []);

        $result = new CompileResult;

        $this->assertEquals("bar\$string\$1", $symbol->serialize($rule, $result));
        $this->assertEquals("bar\$string\$1", $symbol->serialize($rule, $result));
        $this->assertTrue($symbol->shouldWrap());
    }

    /**
     * @test
     */
    public function it_serializes_empty_string_as_null()
    {
        $symbol = new LiteralSymbol("");

        $rule = new CompileRule("bar", []);

        $result = new CompileResult;

        $this->assertNull($symbol->serialize($rule, $result));
        $this->assertFalse($symbol->shouldWrap());
    }

    /**
     * @test
     */
    public function it_serializes_new_line_as_literal()
    {
        $symbol = new LiteralSymbol('\n');

        $rule = new CompileRule("bar", []);

        $result = new CompileResult;

        $this->assertEquals("['value' => \"\\n\", 'type' => Symbol::LITERAL]", $symbol->serialize($rule, $result));
        $this->assertFalse($symbol->shouldWrap());
    }

    /**
     * @test
     */
    public function it_serializes_single_character_as_literal()
    {
        $symbol = new LiteralSymbol('f');

        $rule = new CompileRule("bar", []);

        $result = new CompileResult;

        $this->assertEquals("['value' => \"f\", 'type' => Symbol::LITERAL]", $symbol->serialize($rule, $result));
        $this->assertFalse($symbol->shouldWrap());
    }

    /**
     * @test
     */
    public function it_serializes_multiple_character_string_as_literal_if_lexer_exists()
    {
        $symbol = new LiteralSymbol('foo');

        $rule = new CompileRule("bar", []);

        $result = new CompileResult;

        $result->addConfig("lexer", "lexer");

        $this->assertEquals("['value' => \"foo\", 'type' => Symbol::LITERAL]", $symbol->serialize($rule, $result));
        $this->assertFalse($symbol->shouldWrap());
    }

    /**
     * @test
     */
    public function it_can_generate_compile_rules_for_multiple_character_string()
    {
        $symbol = new LiteralSymbol('foo');

        $rule = new CompileRule("bar", []);

        $result = new CompileResult;

        $rules = $symbol->generateCompileRules($rule, $result);

        $this->assertEquals("bar\$string\$1", $rules[0]->getName());
        $this->assertCount(3, $rules[0]->getSymbols());

        $this->assertInstanceOf(LiteralSymbol::class, $rules[0]->getSymbols()[0]);
        $this->assertEquals("f", $rules[0]->getSymbols()[0]->getLiteral());
        $this->assertInstanceOf(LiteralSymbol::class, $rules[0]->getSymbols()[1]);
        $this->assertEquals("o", $rules[0]->getSymbols()[1]->getLiteral());
        $this->assertInstanceOf(LiteralSymbol::class, $rules[0]->getSymbols()[2]);
        $this->assertEquals("o", $rules[0]->getSymbols()[2]->getLiteral());

        $this->assertNotNull($rules[0]->getPostprocess());
        $this->assertEquals("function (\$d) { return join('', \$d); }", $rules[0]->getPostprocess()->getValue());
    }
}