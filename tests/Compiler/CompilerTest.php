<?php

namespace JPNut\Pearley\Tests\Compiler;

use JPNut\Pearley\Compiler\Compiler;
use JPNut\Pearley\Compiler\CompileResult;
use JPNut\Pearley\Compiler\Contracts\Component;
use PHPUnit\Framework\TestCase;

class CompilerTest extends TestCase
{
    /**
     * @test
     */
    public function it_throws_if_file_does_not_exist()
    {
        $this->expectExceptionMessage("File 'NON_EXISTANT_FILE.txt' could not be found.");

        $compiler = new Compiler();

        $compiler->parseAndCompileFromFile('NON_EXISTANT_FILE.txt');
    }

    /**
     * @test
     */
    public function it_throws_if_invalid_compile_component_supplied()
    {
        $this->expectExceptionMessage('Invalid compile component: expected instance of '.Component::class);

        $compiler = new Compiler();

        $compiler->compile(['foo']);
    }

    /**
     * @test
     */
    public function it_throws_if_unknown_compile_component_supplied()
    {
        $compiler = new Compiler();

        $component = new class() implements Component {
            //
        };

        $this->expectExceptionMessage('Unrecognised compile component: '.get_class($component));

        $compiler->compile([$component]);
    }

    /**
     * @test
     */
    public function it_can_compile_complex_grammar()
    {
        $compiler = new Compiler();

        $this->assertInstanceOf(
            CompileResult::class,
            $compiler->parseAndCompileFromFile(__DIR__.'/../Grammars/TestGrammar6.ne')
        );
    }
}
