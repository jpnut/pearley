<?php

namespace JPNut\Pearley\Compiler;

use InvalidArgumentException;

class PostProcessor
{
    protected const BUILTIN = [
        self::JOINER    => "function (\$d) { return join('', \$d); }",
        self::ARRCONCAT => "function (\$d) { return [\$d[0], ...\$d[1]]; }",
        self::ARRPUSH   => "function (\$d) {return [...\$d[0], \$d[1]]; }",
        self::NULLER    => "function (\$d) { return null; }",
        self::ID        => "\$id",
    ];

    public const JOINER    = "joiner";
    public const ARRCONCAT = "arrconcat";
    public const ARRPUSH   = "arrpush";
    public const NULLER    = "nuller";
    public const ID        = "id";

    /**
     * @var string|null
     */
    protected ?string $value;

    /**
     * @param  string|null  $value
     */
    public function __construct(?string $value)
    {
        $this->value = trim($value);
    }

    /**
     * @return string|null
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * @param  string  $builtin
     * @return \JPNut\Pearley\Compiler\PostProcessor
     */
    public static function builtin(string $builtin): self
    {
        if (!isset(static::BUILTIN[$builtin])) {
            throw new InvalidArgumentException("Builtin function '{$builtin}' not recognised.");
        }

        return new self(static::BUILTIN[$builtin]);
    }
}