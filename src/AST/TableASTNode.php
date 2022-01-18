<?php

namespace Kavinsky\Lua\AST;

/**
 * Class TableASTNode
 *
 * @author  Koen Vlaswinkel <koen@vlaswinkel.info>
 * @author  Ignacio Muñoz Fernandez <nmunozfernandez@gmail.com>
 * @package Kavinsky\Lua\AST
 */
class TableASTNode extends ASTNode
{
    const NAME = 'table';

    /**
     * @var TableEntryASTNode[]
     */
    private $entries;

    /**
     * TableASTNode constructor.
     *
     * @param TableEntryASTNode[] $entries
     */
    public function __construct(array $entries)
    {
        $this->entries = $entries;
    }


    /**
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * @return TableEntryASTNode[]
     */
    public function getEntries()
    {
        return $this->entries;
    }
}
