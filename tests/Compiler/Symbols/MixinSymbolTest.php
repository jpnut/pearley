<?php

namespace JPNut\Pearley\Tests\Compiler\Symbols;

use JPNut\Pearley\Compiler\CompileResult;
use JPNut\Pearley\Compiler\CompileRule;
use JPNut\Pearley\Compiler\Symbols\MixinSymbol;
use PHPUnit\Framework\TestCase;

class MixinSymbolTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_serialize()
    {
        $symbol = new MixinSymbol("foo");

        $rule = new CompileRule("bar\$macrocall\$1", []);

        $result = new CompileResult();

        $result->addMacroMap("bar-foo", "baz");

        $this->assertEquals("baz", $symbol->serialize($rule, $result));
        $this->assertTrue($symbol->shouldWrap());
    }

    /**
     * @test
     */
    public function it_throws_if_invalid_rule_provided()
    {
        $this->expectExceptionMessage("Could not parse rule name: expecting '\$macrocall\$\d+' but none found.");

        $symbol = new MixinSymbol("foo");

        $rule = new CompileRule("bar", []);

        $result = new CompileResult();

        $result->addMacroMap("bar-foo", "baz");

        $symbol->serialize($rule, $result);
    }
}