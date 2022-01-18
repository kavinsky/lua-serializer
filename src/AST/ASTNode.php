<?php

namespace Kavinsky\Lua\AST;

/**
 * Class ASTNode
 *
 * @author  Koen Vlaswinkel <koen@vlaswinkel.info>
 * @author  Ignacio Muñoz Fernandez <nmunozfernandez@gmail.com>
 * @package Kavinsky\Lua\AST
 */
abstract class ASTNode
{
    /**
     * @return string
     */
    public abstract function getName();
}
