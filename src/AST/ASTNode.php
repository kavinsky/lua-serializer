<?php

declare(strict_types=1);

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
    abstract public function getName(): string;
}
