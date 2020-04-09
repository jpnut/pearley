<?php

namespace JPNut\Pearley\Parser\Contracts;

interface LineBreaks
{
    /**
     * @param string $text
     *
     * @return \JPNut\Pearley\Parser\Contracts\LineBreaks
     */
    public function calculate(string $text): self;

    /**
     * @return int
     */
    public function getTotal(): int;

    /**
     * @return int
     */
    public function getLastBreakIndex(): int;
}
