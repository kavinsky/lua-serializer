<?php

namespace Kavinsky\Lua\AST;

/**
 * Class TableEntryASTNode
 *
 * @author  Koen Vlaswinkel <koen@vlaswinkel.info>
 * @author  Ignacio Muñoz Fernandez <nmunozfernandez@gmail.com>
 * @package Kavinsky\Lua\AST
 */
class TableEntryASTNode extends ASTNode
{
    public const NAME = 'table_entry';

    /**
     * @var ASTNode|null
     */
    private ?ASTNode $key;

    /**
     * @var ASTNode
     */
    private ASTNode $value;

    /**
     * TableEntryASTNode constructor.
     *
     * @param null|ASTNode $key
     * @param ASTNode      $value
     */
    public function __construct(ASTNode $value, ASTNode $key = null)
    {
        $this->value = $value;
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return self::NAME;
    }

    /**
     * @return null|ASTNode
     */
    public function getKey(): ?ASTNode
    {
        return $this->key;
    }

    /**
     * @return ASTNode
     */
    public function getValue(): ASTNode
    {
        return $this->value;
    }

    /**
     * @return bool
     */
    public function hasKey(): bool
    {
        return $this->key !== null;
    }
}
