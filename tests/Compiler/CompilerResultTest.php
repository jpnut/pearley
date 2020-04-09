<?php

namespace JPNut\Pearley\Tests\Compiler;

use JPNut\Pearley\Compiler\CompileResult;
use PHPUnit\Framework\TestCase;

class CompilerResultTest extends TestCase
{
    /**
     * @test
     */
    public function it_generates_unique_rule_name()
    {
        $result = new CompileResult;

        $this->assertEquals("foo\$1", $result->unique("foo"));
        $this->assertEquals("foo\$2", $result->unique("foo"));
    }
}