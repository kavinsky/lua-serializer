<?php

namespace Kavinsky\Lua\AST;

/**
 * Class BoolASTNode
 *
 * @author  Koen Vlaswinkel <koen@vlaswinkel.info>
 * @author  Ignacio Muñoz Fernandez <nmunozfernandez@gmail.com>
 * @package Kavinsky\Lua\AST
 */
class BoolASTNode extends LiteralASTNode
{
    const NAME = 'bool';

    /**
     * @var bool
     */
    private $value;

    /**
     * BoolASTNode constructor.
     *
     * @param bool $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * @return bool
     */
    public function getValue()
    {
        return $this->value;
    }
}
