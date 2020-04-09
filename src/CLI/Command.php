<?php

namespace JPNut\Pearley\CLI;

use InvalidArgumentException;
use JPNut\Pearley\Compiler\Compiler;
use JPNut\Pearley\Generator\Generator;
use JPNut\Pearley\Generator\GeneratorConfig;

class Command
{
    protected const COMMANDS = [
        "compile" => [
            "description" => "Compile a pearley grammar file into php",
            "arguments"   => [
                '<file.ne.php>' => "The pearley grammar file to be compiled",
            ],
            "options"     => [
                'out'       => [
                    'short'       => 'o',
                    'long'        => 'out',
                    'example'     => '[file.php]',
                    'description' => "File to output to (defaults to stdout)"
                ],
                'stub'      => [
                    'short'       => 's',
                    'long'        => 'stub',
                    'example'     => '[grammar.stub]',
                    'description' => "The stub to use when generating the php file"
                ],
                'namespace' => [
                    'short'       => 'n',
                    'long'        => 'namespace',
                    'example'     => '[Name\Space]',
                    'description' => "The namespace to use for the generated class"
                ],
                'class'     => [
                    'short'       => 'c',
                    'long'        => 'class',
                    'example'     => '[Grammar]',
                    'description' => "The class name to use for the generated file"
                ],
            ],
        ],
    ];

    public static function main(): void
    {
        try {
            (new static)->run(array_slice($_SERVER['argv'], 1));
        } catch (InvalidArgumentException $e) {
            print (Color::RED."Pearley error: {$e->getMessage()}".Color::RESET);

            exit(1);
        }

        exit(0);
    }

    /**
     * @param  array  $argv
     */
    public function run(array $argv): void
    {
        $this->handleArguments($argv);
    }

    /**
     * @param  array  $argv
     */
    protected function handleArguments(array $argv)
    {
        if (empty($argv)) {
            throw new InvalidArgumentException(
                "Must provide some arguments. Use the help command to see a list of available commands."
            );
        }

        $action = array_shift($argv);

        switch ($action) {
            case "help":
                $this->help($argv);

                break;
            case "compile":
                $this->compile($argv);

                break;
            default:
                $this->unrecognisedCommand($action);
        }
    }

    /**
     * @param  string  $string
     */
    protected function print(string $string): void
    {
        print $string.PHP_EOL;
    }

    /**
     * @param  string  $string
     */
    protected function printHighlighted(string $string)
    {
        $this->print(Color::YELLOW.$string.Color::RESET);
    }

    /**
     * @param  array  $printables
     */
    protected function printList(array $printables)
    {
        $mask = Color::GREEN."  %-30s".Color::RESET." %s";

        foreach ($printables as $key => $value) {
            $this->print(sprintf($mask, $key, $value));
        }
    }

    /**
     * @param  array  $argv
     */
    protected function help(array $argv): void
    {
        if (!empty($argv)) {
            $name = array_shift($argv);

            if (!isset(static::COMMANDS[$name])) {
                $this->unrecognisedCommand($name);
            }

            $this->printHighlighted("Usage:");

            $command = static::COMMANDS[$name];

            $this->print(
                "  {$name} "
                .(!empty($command['arguments']) ? join(' ', array_keys($command['arguments'])).' ' : "")
                .(!empty($command['options']) ? "[options] " : "")
            );
            $this->print("");

            $this->printHighlighted("Arguments:");
            $this->printList($command['arguments']);
            $this->print("");

            $this->printHighlighted("Options:");
            $this->printOptions($command['options']);
            $this->print("");

            return;
        }

        $this->printHighlighted("Commands:");

        $commands = [];

        foreach (static::COMMANDS as $name => $command) {
            $commands[$name] = $command['description'];
        }

        $this->printList($commands);
    }

    /**
     * @param  array  $options
     */
    protected function printOptions(array $options)
    {
        $printable_options = [];

        foreach ($options as $option) {
            $printable_options["-{$option['short']} --{$option['long']} {$option['example']}"] = $option['description'];
        }

        $this->printList($printable_options);
    }

    /**
     * @param  string  $command
     */
    protected function unrecognisedCommand(string $command)
    {
        throw new InvalidArgumentException(
            "Unrecognised command: '{$command}'. Use the help command to see a list of available commands."
        );
    }

    /**
     * @param  array  $argv
     */
    protected function compile(array $argv)
    {
        $arguments = $this->arguments("compile", $argv);

        $fileName = $arguments->getArguments()[0];

        $options = $arguments->getOptions();

        $compiler = new Compiler;

        $generatorConfig = GeneratorConfig::initialise();

        if (isset($options['stub'])) {
            $generatorConfig->withStub($options['stub']);
        }

        if (isset($options['namespace'])) {
            $generatorConfig->withNamespace($options['namespace']);
        }

        $generatorConfig->withClass($options['class'] ?? $this->defaultClass($fileName));

        $result = (new Generator($generatorConfig->create()))
            ->generate($compiler->parseAndCompileFromFile($fileName));

        $this->outputCompiledFile($options, $result);
    }

    /**
     * @param  array  $options
     * @param  string  $result
     */
    protected function outputCompiledFile(array $options, string $result)
    {
        if (isset($options['out'])) {
            $dirname = dirname($options['out']);

            if (!is_dir($dirname)) {
                mkdir($dirname, 0777, true);
            }

            $out = fopen($options['out'], "w");

            fwrite($out, $result);

            fclose($out);

            $this->print(Color::GREEN."Grammar saved to {$options['out']}".Color::RESET);

            return;
        }

        $this->print($result);
    }

    /**
     * @param  string  $command
     * @param  array  $argv
     * @return \JPNut\Pearley\CLI\Arguments
     */
    protected function arguments(string $command, array $argv): Arguments
    {
        return (new Arguments(
            $command,
            static::COMMANDS[$command]["arguments"],
            static::COMMANDS[$command]["options"]
        ))->read($argv);
    }

    /**
     * @param  string  $fileName
     * @return string
     */
    protected function defaultClass(string $fileName): string
    {
        return ucfirst(preg_replace('/\s+/', '', explode('.', basename($fileName))[0]));
    }
}