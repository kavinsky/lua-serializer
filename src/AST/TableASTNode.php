<?php

declare(strict_types=1);

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
    public const NAME = 'table';

    /**
     * @var TableEntryASTNode[]
     */
    private array $entries;

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
    public function getName(): string
    {
        return self::NAME;
    }

    /**
     * @return TableEntryASTNode[]
     */
    public function getEntries(): array
    {
        return $this->entries;
    }
}
