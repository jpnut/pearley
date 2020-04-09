<?php

namespace JPNut\Pearley\Compiler;

class CompileResult
{
    /**
     * @var string[]
     */
    protected array $contents = [];

    /**
     * @var string[]
     */
    protected array $use = [];

    /**
     * @var string[]
     */
    protected array $configs = [];

    /**
     * @var \JPNut\Pearley\Compiler\CompileRule[]
     */
    protected array $rules = [];

    /**
     * @var \JPNut\Pearley\Compiler\Macro[]
     */
    protected array $macros = [];

    /**
     * @var string[]
     */
    protected array $macro_map = [];

    /**
     * @var string|null
     */
    protected ?string $start = null;

    /**
     * @var int[]
     */
    protected array $unique_rules = [];

    /**
     * @return array
     */
    public function getContents(): array
    {
        return $this->contents;
    }

    /**
     * @return array
     */
    public function getUse(): array
    {
        return $this->use;
    }

    /**
     * @return array
     */
    public function getConfigs(): array
    {
        return $this->configs;
    }

    /**
     * @return array
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * @return array
     */
    public function getMacros(): array
    {
        return $this->macros;
    }

    /**
     * @return string|null
     */
    public function getStart(): ?string
    {
        return $this->start;
    }

    /**
     * @param string $content
     *
     * @return \JPNut\Pearley\Compiler\CompileResult
     */
    public function addContent(string $content): self
    {
        $this->contents[] = $content;

        return $this;
    }

    /**
     * @param array $contents
     *
     * @return \JPNut\Pearley\Compiler\CompileResult
     */
    public function addContents(array $contents): self
    {
        $this->contents = array_merge($this->contents, $contents);

        return $this;
    }

    /**
     * @param array       $rules
     * @param string|null $componentName
     *
     * @return \JPNut\Pearley\Compiler\CompileResult
     */
    public function addRules(array $rules, ?string $componentName = null): self
    {
        $this->rules = array_merge($this->rules, $rules);

        if (is_null($this->start) && !is_null($componentName)) {
            $this->setStart($componentName);
        }

        return $this;
    }

    /**
     * @param array $configs
     *
     * @return \JPNut\Pearley\Compiler\CompileResult
     */
    public function mergeConfigs(array $configs): self
    {
        $this->configs = array_merge($this->configs, $configs);

        return $this;
    }

    /**
     * @param string $key
     * @param string $value
     *
     * @return \JPNut\Pearley\Compiler\CompileResult
     */
    public function addConfig(string $key, string $value): self
    {
        $this->configs[$key] = $value;

        return $this;
    }

    /**
     * @param array $macros
     *
     * @return \JPNut\Pearley\Compiler\CompileResult
     */
    public function mergeMacros(array $macros): self
    {
        $this->macros = array_merge($this->macros, $macros);

        return $this;
    }

    /**
     * @param string $name
     * @param array  $args
     * @param array  $rules
     *
     * @return \JPNut\Pearley\Compiler\CompileResult
     */
    public function addMacro(string $name, array $args, array $rules): self
    {
        $this->macros[$name] = new Macro($args, $rules);

        return $this;
    }

    /**
     * @param string $value
     *
     * @return \JPNut\Pearley\Compiler\CompileResult
     */
    public function addUse(string $value): self
    {
        $this->use[] = $value;

        return $this;
    }

    /**
     * @param string $start
     *
     * @return \JPNut\Pearley\Compiler\CompileResult
     */
    public function setStart(string $start): self
    {
        $this->start = $start;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function unique(string $name): string
    {
        $this->unique_rules[$name] = $number = ($this->unique_rules[$name] ?? 0) + 1;

        return "{$name}\${$number}";
    }

    /**
     * @param string $macro
     *
     * @return bool
     */
    public function hasMacro(string $macro): bool
    {
        return isset($this->macros[$macro]);
    }

    /**
     * @param string $macro
     *
     * @return \JPNut\Pearley\Compiler\Macro
     */
    public function getMacro(string $macro): Macro
    {
        return $this->macros[$macro];
    }

    /**
     * @param string $key
     * @param string $value
     *
     * @return \JPNut\Pearley\Compiler\CompileResult
     */
    public function addMacroMap(string $key, string $value): self
    {
        $this->macro_map[$key] = $value;

        return $this;
    }

    /**
     * @param string $mixin
     *
     * @return string|null
     */
    public function getMacroMap(string $mixin): ?string
    {
        return $this->macro_map[$mixin] ?? null;
    }

    /**
     * @return string[]
     */
    public function getAllMacroMaps(): array
    {
        return $this->macro_map;
    }
}
