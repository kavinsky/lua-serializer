<?php

namespace Kavinsky\Lua\AST;

/**
 * Class LiteralASTNode
 *
 * @author  Koen Vlaswinkel <koen@vlaswinkel.info>
 * @author  Ignacio Muñoz Fernandez <nmunozfernandez@gmail.com>
 * @package Kavinsky\Lua\AST
 */
abstract class LiteralASTNode extends ASTNode
{
    public abstract function getValue(): mixed;
}
