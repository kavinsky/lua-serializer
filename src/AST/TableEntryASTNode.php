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
    const NAME = 'table_entry';

    /**
     * @var ASTNode|null
     */
    private $key;
    /**
     * @var ASTNode
     */
    private $value;

    /**
     * TableEntryASTNode constructor.
     *
     * @param null|ASTNode $key
     * @param ASTNode      $value
     */
    public function __construct(ASTNode $value, ASTNode $key = null)
    {
        $this->value = $value;
        $this->key   = $key;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * @return null|ASTNode
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return ASTNode
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return bool
     */
    public function hasKey()
    {
        return $this->key !== null;
    }
}
