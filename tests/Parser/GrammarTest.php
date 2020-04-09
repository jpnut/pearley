<?php

namespace JPNut\Pearley\Tests\Parser;

use JPNut\Pearley\Parser\Grammar;
use JPNut\Pearley\Parser\Rule;
use PHPUnit\Framework\TestCase;

class GrammarTest extends TestCase
{
    /**
     * @test
     */
    public function it_throws_if_rule_is_not_rule_instance_or_array()
    {
        $this->expectExceptionMessage("Invalid rule provided: rule must be array or instance of JPNut\Pearley\Parser\Rule");

        new Grammar([1]);
    }

    /**
     * @test
     */
    public function it_throws_if_rule_name_missing()
    {
        $this->expectExceptionMessage('Rule name is required');

        new Grammar([[]]);
    }

    /**
     * @test
     */
    public function it_throws_if_rule_name_is_not_string()
    {
        $this->expectExceptionMessage('Rule name must be a string');

        new Grammar([['name' => 1]]);
    }

    /**
     * @test
     */
    public function it_throws_if_symbols_is_not_array()
    {
        $this->expectExceptionMessage('Rule symbols must be either: an array or null');

        new Grammar([['name' => 'foo', 'symbols' => 1]]);
    }

    /**
     * @test
     */
    public function it_throws_if_postprocess_is_not_closure()
    {
        $this->expectExceptionMessage('Rule postprocess must be either: a closure or null');

        new Grammar([['name' => 'foo', 'symbols' => [], 'postprocess' => 1]]);
    }

    /**
     * @test
     */
    public function it_can_use_existing_rule_instance()
    {
        $rule = new Rule('foo', []);

        $grammar = new Grammar([$rule]);

        $this->assertSame($rule, $grammar->getRulesByName('foo')[0]);
    }
}
