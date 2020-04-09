<?php

namespace JPNut\Pearley\Tests\Parser;

use JPNut\Pearley\Parser\Rule;
use JPNut\Pearley\Parser\Symbol;
use PHPUnit\Framework\TestCase;

class RuleTest extends TestCase
{
    /**
     * @test
     */
    public function it_uses_provided_postprocessor()
    {
        $rule = new Rule("foo", [], fn($data, $reference) => "{$data}-baz-{$reference}");

        $this->assertEquals("bar-baz-0", $rule->postprocess("bar", 0));
    }

    /**
     * @test
     */
    public function it_uses_fallback_postprocessor_if_none_provided()
    {
        $rule = new Rule("foo", []);

        $this->assertEquals("bar", $rule->postprocess("bar", 0));
    }

    /**
     * @test
     */
    public function it_can_retrieve_specific_symbol()
    {
        $rule = new Rule("foo", ['bar', 'baz']);

        $this->assertEquals('bar', $rule->getSymbol(0));
        $this->assertEquals('baz', $rule->getSymbol(1));
    }

    /**
     * @test
     */
    public function it_returns_correct_complete_status()
    {
        $rule = new Rule("foo", ['bar', 'baz']);

        $this->assertEquals(false, $rule->isComplete(0));
        $this->assertEquals(false, $rule->isComplete(1));
        $this->assertEquals(true, $rule->isComplete(2));
    }

    /**
     * @test
     */
    public function it_can_map_rule_to_string()
    {
        $rule = new Rule("foo", ['bar', 'baz']);

        $this->assertEquals("foo →  ●  bar baz", $rule->toString(0));
        $this->assertEquals("foo → bar  ●  baz", $rule->toString(1));
        $this->assertEquals("foo → bar baz  ● ", $rule->toString(2));
    }

    /**
     * @test
     */
    public function it_can_parse_symbol_from_instance()
    {
        $rule = new Rule("foo", [$bar = new Symbol("bar")]);

        $this->assertEquals($bar, $rule->getSymbol(0));
    }

    /**
     * @test
     */
    public function it_can_parse_symbol_from_string()
    {
        $rule = new Rule("foo", ["bar"]);

        $this->assertEquals("bar", $rule->getSymbol(0)->getValue());
    }

    /**
     * @test
     */
    public function it_can_parse_symbol_from_array()
    {
        $rule = new Rule("foo", [["value" => "bar"]]);

        $this->assertEquals("bar", $rule->getSymbol(0)->getValue());
        $this->assertEquals(Symbol::NONTERMINAL, $rule->getSymbol(0)->getType());

        $rule2 = new Rule("foo", [["value" => "bar", "type" => Symbol::REGEX]]);

        $this->assertEquals("bar", $rule2->getSymbol(0)->getValue());
        $this->assertEquals(Symbol::REGEX, $rule2->getSymbol(0)->getType());
    }

    /**
     * @test
     */
    public function it_throws_if_symbol_is_invalid_type()
    {
        $this->expectExceptionMessage(
            "Invalid symbol provided: symbol must be a string, an array or an instance of ".Symbol::class
        );

        new Rule("foo", [1]);
    }

    /**
     * @test
     */
    public function it_throws_if_array_symbol_value_missing()
    {
        $this->expectExceptionMessage("Symbol value is required");

        new Rule("foo", [[]]);
    }

    /**
     * @test
     */
    public function it_throws_if_array_symbol_value_is_not_string()
    {
        $this->expectExceptionMessage("Symbol value must be a string");

        new Rule("foo", [["value" => 1]]);
    }

    /**
     * @test
     */
    public function it_throws_if_array_symbol_type_is_invalid()
    {
        $this->expectExceptionMessage("Symbol type must be either: an integer or null");

        new Rule("foo", [["value" => "bar", "type" => "baz"]]);
    }
}