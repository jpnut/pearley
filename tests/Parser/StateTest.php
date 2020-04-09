<?php

namespace JPNut\Pearley\Tests\Parser;

use ArrayObject;
use JPNut\Pearley\Parser\Rule;
use JPNut\Pearley\Parser\State;
use PHPUnit\Framework\TestCase;

class StateTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_get_next_state()
    {
        $state = new State(
            $foo = new Rule('foo', []),
            0,
            0,
            $stateWantedBy = new ArrayObject([]),
            [0]
        );

        $right = new State(
            new Rule('bar', []),
            2,
            2,
            new ArrayObject([]),
            [2]
        );

        $next = $state->nextState($right);

        $this->assertSame($foo, $next->getRule());
        $this->assertEquals(1, $next->getDot());
        $this->assertEquals(0, $next->getReference());
        $this->assertSame($stateWantedBy, $next->getWantedBy());
        $this->assertSame($state, $next->getLeft());
        $this->assertSame($right, $next->getRight());
        $this->assertEquals([], $next->getData());
    }

    /**
     * @test
     */
    public function it_can_build_complex_data_array()
    {
        $state = new State(
            $foo = new Rule('state', ['foo', 'bar', 'baz']),
            0,
            0,
            $stateWantedBy = new ArrayObject([]),
            []
        );

        $state1 = new State(
            new Rule('state1', []),
            0,
            0,
            new ArrayObject([]),
            [1]
        );

        $state2 = new State(
            new Rule('state2', []),
            0,
            0,
            new ArrayObject([]),
            [2]
        );

        $state3 = new State(
            new Rule('state3', []),
            0,
            0,
            new ArrayObject([]),
            [3]
        );

        $next = $state->nextState($state1)->nextState($state2)->nextState($state3);

        $this->assertEquals(3, $next->getDot());
        $this->assertEquals(0, $next->getReference());
        $this->assertSame($stateWantedBy, $next->getWantedBy());
        $this->assertSame($state3, $next->getRight());
        $this->assertEquals([[1], [2], [3]], $next->getData());
    }

    /**
     * @test
     */
    public function it_can_postprocess_data()
    {
        $state = new State(
            $foo = new Rule('foo', [], fn () => 'bar'),
            0,
            0,
            $stateWantedBy = new ArrayObject([]),
            []
        );

        $state->finish();

        $this->assertEquals('bar', $state->getData());
    }

    /**
     * @test
     */
    public function it_can_cast_state_to_string()
    {
        $state = new State(
            $foo = new Rule('foo', ['bar']),
            0,
            0,
            $stateWantedBy = new ArrayObject([]),
            []
        );

        $this->assertEquals('{foo →  ●  bar}, from: 0', (string) $state);
    }
}
