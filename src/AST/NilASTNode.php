<?php

namespace Kavinsky\Lua\AST;

/**
 * Class NilASTNode
 *
 * @author  Koen Vlaswinkel <koen@vlaswinkel.info>
 * @author  Ignacio Muñoz Fernandez <nmunozfernandez@gmail.com>
 * @package Kavinsky\Lua\AST
 */
class NilASTNode extends ASTNode
{
    public const NAME = 'nil';

    /**
     * @return string
     */
    public function getName(): string
    {
        return self::NAME;
    }
}
