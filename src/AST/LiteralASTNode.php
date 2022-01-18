<?php

declare(strict_types=1);

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
    abstract public function getValue(): mixed;
}
