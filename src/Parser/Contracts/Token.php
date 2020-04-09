<?php

namespace JPNut\Pearley\Parser\Contracts;

interface Token
{
    /**
     * @return string
     */
    public function getText(): string;

    /**
     * @return mixed
     */
    public function getValue();

    /**
     * @return int
     */
    public function getOffset(): int;

    /**
     * @return int
     */
    public function getLineBreaks(): int;

    /**
     * @return int
     */
    public function getLine(): int;

    /**
     * @return int
     */
    public function getCol(): int;

    /**
     * @return string|null
     */
    public function getType(): ?string;
}
