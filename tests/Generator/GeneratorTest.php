<?php

namespace JPNut\Pearley\Tests\Generator;

use JPNut\Pearley\Generator\Generator;
use JPNut\Pearley\Generator\GeneratorConfig;
use PHPUnit\Framework\TestCase;

class GeneratorTest extends TestCase
{
    /**
     * @test
     * @dataProvider grammars
     *
     * @param string                                   $file
     * @param \JPNut\Pearley\Generator\GeneratorConfig $config
     * @param string                                   $expected
     */
    public function it_can_generate_grammars(string $file, GeneratorConfig $config, string $expected)
    {
        $generator = new Generator($config);

        $this->assertEquals(file_get_contents($expected), $generator->generateFromFile($file));
    }

    /**
     * @return array[]
     */
    public function grammars()
    {
        return [
            [
                __DIR__.'/../Grammars/TestGrammar1.ne',
                GeneratorConfig::initialise()
                    ->withNamespace("JPNut\\Pearley\\Tests\Grammars")
                    ->withClass('TestGrammar1')
                    ->create(),
                __DIR__.'/../Grammars/TestGrammar1.php',
            ],
            [
                __DIR__.'/../Grammars/TestGrammar2.ne',
                GeneratorConfig::initialise()
                    ->withNamespace("JPNut\\Pearley\\Tests\Grammars")
                    ->withClass('TestGrammar2')
                    ->create(),
                __DIR__.'/../Grammars/TestGrammar2.php',
            ],
            [
                __DIR__.'/../Grammars/TestGrammar3.ne',
                GeneratorConfig::initialise()
                    ->withNamespace("JPNut\\Pearley\\Tests\Grammars")
                    ->withClass('TestGrammar3')
                    ->create(),
                __DIR__.'/../Grammars/TestGrammar3.php',
            ],
            [
                __DIR__.'/../Grammars/TestGrammar4.ne',
                GeneratorConfig::initialise()
                    ->withNamespace("JPNut\\Pearley\\Tests\Grammars")
                    ->withClass('TestGrammar4')
                    ->create(),
                __DIR__.'/../Grammars/TestGrammar4.php',
            ],
            [
                __DIR__.'/../Grammars/TestGrammar5.ne',
                GeneratorConfig::initialise()
                    ->withNamespace("JPNut\\Pearley\\Tests\Grammars")
                    ->withClass('TestGrammar5')
                    ->create(),
                __DIR__.'/../Grammars/TestGrammar5.php',
            ],
            [
                __DIR__.'/../Grammars/TestGrammar6.ne',
                GeneratorConfig::initialise()
                    ->withNamespace("JPNut\\Pearley\\Tests\Grammars")
                    ->withClass('TestGrammar6')
                    ->create(),
                __DIR__.'/../Grammars/TestGrammar6.php',
            ],
        ];
    }
}
