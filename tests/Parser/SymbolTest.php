<?php

namespace JPNut\Pearley\Tests\Parser;

use JPNut\Pearley\Parser\Symbol;
use PHPUnit\Framework\TestCase;

class SymbolTest extends TestCase
{
    /**
     * @test
     */
    public function it_defaults_to_nonterminal_type()
    {
        $symbol = new Symbol("foo");

        $this->assertEquals(Symbol::NONTERMINAL, $symbol->getType());
    }

    /**
     * @test
     */
    public function it_throws_if_unrecognised_type()
    {
        $this->expectExceptionMessage("Symbol type '-1' not recognised.");

        new Symbol("foo", -1);
    }

    /**
     * @test
     */
    public function it_can_stringify_symbol_value()
    {
        $nonterminal = new Symbol("foo", Symbol::NONTERMINAL);

        $this->assertEquals("foo", (string) $nonterminal);

        $regex = new Symbol("foo", Symbol::REGEX);

        $this->assertEquals("foo", (string) $regex);

        $literal = new Symbol("foo", Symbol::LITERAL);

        $this->assertEquals("\"foo\"", (string) $literal);

        $token = new Symbol("foo", Symbol::TOKEN);

        $this->assertEquals("%foo", (string) $token);
    }
}