<?php

namespace JPNut\Pearley\Tests\Compiler;

use JPNut\Pearley\Compiler\PostProcessor;
use PHPUnit\Framework\TestCase;

class PostProcessorTest extends TestCase
{
    /**
     * @test
     */
    public function it_throws_if_builtin_does_not_exist()
    {
        $this->expectExceptionMessage("Builtin function 'foo' not recognised.");

        PostProcessor::builtin("foo");
    }
}