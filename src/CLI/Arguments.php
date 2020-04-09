<?php

namespace JPNut\Pearley\CLI;

use InvalidArgumentException;

class Arguments
{
    /**
     * @var string
     */
    protected string $command;

    /**
     * @var array
     */
    protected array $available_arguments;

    /**
     * @var array
     */
    protected array $available_options;

    /**
     * @var array
     */
    protected array $arguments = [];

    /**
     * @var array
     */
    protected array $options = [];

    /**
     * @var array
     */
    protected array $short_options;

    /**
     * @var array
     */
    protected array $long_options;

    /**
     * @param  string  $command
     * @param  array  $arguments
     * @param  array  $options
     */
    public function __construct(string $command, array $arguments = [], array $options = [])
    {
        $this->command             = $command;
        $this->available_arguments = $arguments;
        $this->available_options   = $options;
        $this->short_options       = $this->shortOptions($options);
        $this->long_options        = $this->longOptions($options);

    }

    /**
     * @param  array  $argv
     * @return \JPNut\Pearley\CLI\Arguments
     */
    public function read(array $argv): Arguments
    {
        reset($argv);

        while (($arg = current($argv)) !== false) {
            next($argv);

            if ($arg === '') {
                continue;
            }

            if (preg_match('/^(?:--([^\s=]+)[\s]*=(.+))$/', $arg, $matches)) {
                $this->setOption($this->long_options[$matches[1]], $matches[2]);

                continue;
            }

            if (preg_match('/^(?:--([^\s=]+))$/', $arg, $matches)) {
                $value = current($argv);
                next($argv);

                $this->setOption($this->long_options[$matches[1]], $value);

                continue;
            }

            if (preg_match('/^(?:-([^\s=]+)[\s]*=(.+))$/', $arg, $matches)) {
                $this->setOption($this->short_options[$matches[1]], $matches[2]);

                continue;
            }

            if (preg_match('/^(?:-([^\s=]+))$/', $arg, $matches)) {
                $value = current($argv);
                next($argv);

                $this->setOption($this->short_options[$matches[1]], $value);

                continue;
            }

            $this->setArgument($arg);
        }

        if (($received = count($this->arguments)) !== ($total = count($this->available_arguments))) {
            throw new InvalidArgumentException(
                "Expected to receive {$total} argument(s) for command '{$this->command}' but received {$received}"
            );
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param  string  $key
     * @param  string  $value
     */
    protected function setOption(string $key, string $value): void
    {
        if (array_key_exists($key, $this->available_options)) {
            $this->options[$key] = $value;
        }
    }

    /**
     * @param  string  $argument
     */
    protected function setArgument(string $argument): void
    {
        if (($total = count($this->available_arguments)) > count($this->arguments)) {
            $this->arguments[] = $argument;

            return;
        }

        throw new InvalidArgumentException(
            "Too many arguments. Expected to receive {$total} argument(s) for command '{$this->command}'."
        );
    }

    /**
     * @param  array  $options
     * @return array
     */
    protected function shortOptions(array $options): array
    {
        $shortOptions = [];

        foreach ($options as $name => $option) {
            if (!isset($option['short'])) {
                continue;
            }

            $shortOptions[$option['short']] = $name;
        }

        return $shortOptions;
    }

    /**
     * @param  array  $options
     * @return array
     */
    protected function longOptions(array $options): array
    {
        $longOptions = [];

        foreach ($options as $name => $option) {
            if (!isset($option['long'])) {
                continue;
            }

            $longOptions[$option['long']] = $name;
        }

        return $longOptions;
    }
}