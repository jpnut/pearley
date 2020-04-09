<?php

namespace JPNut\Pearley\Tests\CLI;

use JPNut\Pearley\CLI\Color;
use JPNut\Pearley\CLI\Command;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;

class CommandTest extends TestCase
{
    /**
     * @var  \org\bovigo\vfs\vfsStreamDirectory
     */
    protected vfsStreamDirectory $root;

    /**
     * set up test environment
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->root = vfsStream::setup('exampleDir');
    }

    /**
     * @test
     */
    public function it_throws_if_no_arguments_provided()
    {
        $this->expectExceptionMessage("Must provide some arguments. Use the help command to see a list of available commands.");

        $command = new Command;

        $command->run([]);
    }

    /**
     * @test
     */
    public function it_throws_if_command_is_not_recognised()
    {
        $this->expectExceptionMessage("Unrecognised command: 'foo'. Use the help command to see a list of available commands.");

        $command = new Command;

        $command->run(['foo']);
    }

    /**
     * @test
     */
    public function it_can_provide_help_with_list_of_commands()
    {
        $this->expectOutputString(join("\n", [
            Color::YELLOW."Commands:".Color::RESET,
            Color::GREEN."  compile                       ".Color::RESET." Compile a pearley grammar file into php\n",
        ]));

        $command = new Command;

        $command->run(['help']);
    }

    /**
     * @test
     */
    public function it_throws_if_command_for_help_is_not_recognised()
    {
        $this->expectExceptionMessage("Unrecognised command: 'foo'. Use the help command to see a list of available commands.");

        $command = new Command;

        $command->run(['help', 'foo']);
    }

    /**
     * @test
     */
    public function it_can_provide_help_with_args_for_specific_command()
    {
        $this->expectOutputString(join("\n", [
            Color::YELLOW."Usage:".Color::RESET,
            "  compile <file.ne.php> [options] \n",

            Color::YELLOW."Arguments:".Color::RESET,
            Color::GREEN."  <file.ne.php>                 ".Color::RESET." The pearley grammar file to be compiled\n",

            Color::YELLOW."Options:".Color::RESET,
            Color::GREEN."  -o --out [file.php]           ".Color::RESET." File to output to (defaults to stdout)",
            Color::GREEN."  -s --stub [grammar.stub]      ".Color::RESET." The stub to use when generating the php file",
            Color::GREEN."  -n --namespace [Name\\Space]   ".Color::RESET." The namespace to use for the generated class",
            Color::GREEN."  -c --class [Grammar]          ".Color::RESET." The class name to use for the generated file\n\n",

        ]));

        $command = new Command;

        $command->run(['help', 'compile']);
    }

    /**
     * @test
     */
    public function it_throws_if_no_arguments_passed_to_compile_command()
    {
        $this->expectExceptionMessage("Expected to receive 1 argument(s) for command 'compile' but received 0");

        $command = new Command;

        $command->run(['compile']);
    }

    /**
     * @test
     * @dataProvider grammars
     * @param  string  $grammar
     * @param  string  $namespace
     * @param  string  $class
     * @param  string  $expected
     */
    public function it_can_compile_grammars(string $grammar, string $namespace, string $class, string $expected)
    {
        $this->expectOutputString(file_get_contents($expected).PHP_EOL);

        $command = new Command;

        $command->run([
            'compile',
            $grammar,
            "-n",
            $namespace,
            "-c",
            $class,
            "-s",
            __DIR__."/../../src/stubs/grammar.stub"
        ]);
    }

    /**
     * @test
     * @dataProvider grammars
     * @param  string  $grammar
     * @param  string  $namespace
     * @param  string  $class
     * @param  string  $expected
     */
    public function it_can_write_compiled_grammars_to_file(
        string $grammar,
        string $namespace,
        string $class,
        string $expected
    ) {
        $command = new Command;

        $file_name = "{$class}.php";

        $qualified_file_name = "{$this->root->url()}/{$file_name}";

        $this->expectOutputString(Color::GREEN."Grammar saved to {$qualified_file_name}".Color::RESET."\n");

        $command->run([
            'compile',
            $grammar,
            "-n",
            $namespace,
            "-c",
            $class,
            "-o",
            $qualified_file_name
        ]);

        $this->assertTrue($this->root->hasChild($file_name));

        $this->assertEquals(file_get_contents($expected), $this->root->getChild($file_name)->getContent());
    }

    /**
     * @return array[]
     */
    public function grammars()
    {
        return [
            [
                __DIR__."/../Grammars/TestGrammar1.ne",
                "JPNut\\Pearley\\Tests\Grammars",
                "TestGrammar1",
                __DIR__."/../Grammars/TestGrammar1.php",
            ],
            [
                __DIR__."/../Grammars/TestGrammar2.ne",
                "JPNut\\Pearley\\Tests\Grammars",
                "TestGrammar2",
                __DIR__."/../Grammars/TestGrammar2.php",
            ],
            [
                __DIR__."/../Grammars/TestGrammar3.ne",
                "JPNut\\Pearley\\Tests\Grammars",
                "TestGrammar3",
                __DIR__."/../Grammars/TestGrammar3.php",
            ],
            [
                __DIR__."/../Grammars/TestGrammar4.ne",
                "JPNut\\Pearley\\Tests\Grammars",
                "TestGrammar4",
                __DIR__."/../Grammars/TestGrammar4.php",
            ],
            [
                __DIR__."/../Grammars/TestGrammar5.ne",
                "JPNut\\Pearley\\Tests\Grammars",
                "TestGrammar5",
                __DIR__."/../Grammars/TestGrammar5.php",
            ],
            [
                __DIR__."/../Grammars/TestGrammar6.ne",
                "JPNut\\Pearley\\Tests\Grammars",
                "TestGrammar6",
                __DIR__."/../Grammars/TestGrammar6.php",
            ],
        ];
    }
}