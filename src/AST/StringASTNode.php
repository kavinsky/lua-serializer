<?php

declare(strict_types=1);

namespace Kavinsky\Lua\AST;

/**
 * Class StringASTNode
 *
 * @author  Koen Vlaswinkel <koen@vlaswinkel.info>
 * @author  Ignacio Muñoz Fernandez <nmunozfernandez@gmail.com>
 * @package Kavinsky\Lua\AST
 */
class StringASTNode extends LiteralASTNode
{
    public const NAME = 'string';

    /**
     * @var string
     */
    private string $value;

    /**
     * StringASTNode constructor.
     *
     * @param string $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return self::NAME;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }
}
