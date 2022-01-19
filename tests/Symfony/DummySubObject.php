<?php

declare(strict_types=1);

namespace Kavinsky\Lua\Tests\Symfony;

class DummySubObject
{
    private string $megaString;

    public function getMegaString(): string
    {
        return $this->megaString;
    }

    public function setMegaString(string $megaString): void
    {
        $this->megaString = $megaString;
    }
}
