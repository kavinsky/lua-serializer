<?php

declare(strict_types=1);

namespace Kavinsky\Lua\Tests\Symfony;

use Symfony\Component\Serializer\Annotation\SerializedName;

class DummyObject
{
    #[SerializedName('my_string')]
    private string $dummyString;

    private int $dummyInt;

    private float $dummyFloat;

    private bool $dummyBool;

    /**
     * @param string[] $dummyString
     */
    private array $dummyArray;

    private DummySubObject $dummySubObject;

    public function setDummyString(string $dummyString): void
    {
        $this->dummyString = $dummyString;
    }

    public function getDummyString(): string
    {
        return $this->dummyString;
    }

    public function setDummyInt(int $dummyInt): void
    {
        $this->dummyInt = $dummyInt;
    }

    public function getDummyInt(): int
    {
        return $this->dummyInt;
    }

    public function setDummyFloat(float $dummyFloat): void
    {
        $this->dummyFloat = $dummyFloat;
    }

    public function getDummyFloat(): float
    {
        return $this->dummyFloat;
    }

    public function isDummyBool(): bool
    {
        return $this->dummyBool;
    }

    public function setDummyBool(bool $bool): void
    {
        $this->dummyBool = $bool;
    }

    public function getDummyBool(): bool
    {
        return $this->dummyBool;
    }

    public function setDummyArray(array $arr): void
    {
        $this->dummyArray = $arr;
    }

    public function getDummyArray(): array
    {
        return $this->dummyArray;
    }

    public function setDummySubObject(DummySubObject $dummySubObject): void
    {
        $this->dummySubObject = $dummySubObject;
    }

    public function getDummySubObject(): DummySubObject
    {
        return $this->dummySubObject;
    }
}
