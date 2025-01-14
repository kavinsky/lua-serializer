<?php

declare(strict_types=1);

namespace Kavinsky\Lua;

/**
 * Class Token
 *
 * @author  Koen Vlaswinkel <koen@vlaswinkel.info>
 * @author  Ignacio Muñoz Fernandez <nmunozfernandez@gmail.com>
 * @package Kavinsky\Lua
 */
class Token
{
    public const TYPE_STRING = 1;
    public const TYPE_NUMBER = 2;
    public const TYPE_PUNCTUATION = 3;
    public const TYPE_IDENTIFIER = 4;
    public const TYPE_KEYWORD = 5;

    /**
     * @var int
     */
    private $type;
    /**
     * @var string
     */
    private $value;

    /**
     * Token constructor.
     *
     * @param int    $type
     * @param string $value
     */
    public function __construct($type, $value)
    {
        $this->type = $type;
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}
