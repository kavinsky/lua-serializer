<?php

declare(strict_types=1);

namespace Kavinsky\Lua\AST;

/**
 * Class NumberASTNode
 *
 * @author  Koen Vlaswinkel <koen@vlaswinkel.info>
 * @author  Ignacio Muñoz Fernandez <nmunozfernandez@gmail.com>
 * @package Kavinsky\Lua\AST
 */
class NumberASTNode extends LiteralASTNode
{
    public const NAME = 'number';

    /**
     * @var int|float
     */
    private float|int $value;

    /**
     * NumberASTNode constructor.
     *
     * @param float|int $value
     */
    public function __construct(float|int $value)
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
     * @return float|int
     */
    public function getValue(): float|int
    {
        return $this->value;
    }
}
