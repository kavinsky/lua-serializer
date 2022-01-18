<?php

declare(strict_types=1);

namespace Kavinsky\Lua;

use Kavinsky\Lua\AST\ASTNode;
use Kavinsky\Lua\AST\LiteralASTNode;
use Kavinsky\Lua\AST\NilASTNode;
use Kavinsky\Lua\AST\TableASTNode;
use Kavinsky\Lua\AST\TableEntryASTNode;

/**
 * Class LuaToPhpConverter
 *
 * @author  Koen Vlaswinkel <koen@vlaswinkel.info>
 * @author  Ignacio Muñoz Fernandez <nmunozfernandez@gmail.com>
 * @package Kavinsky\Lua
 */
class Unserializer
{
    /**
     * @param ASTNode $input
     *
     * @return mixed
     * @throws ParseException
     */
    public function unserialize(ASTNode $input): mixed
    {
        if ($input instanceof TableASTNode) {
            return self::parseTable($input);
        }

        if ($input instanceof LiteralASTNode) {
            return $input->getValue();
        }

        if ($input instanceof NilASTNode) {
            return null;
        }

        throw new ParseException("Unexpected AST node: " . $input->getName());
    }

    /**
     * @return array
     * @throws ParseException
     */
    private function parseTable(TableASTNode $input): array
    {
        $data = [];
        foreach ($input->getEntries() as $token) {
            if (! ($token instanceof TableEntryASTNode)) {
                throw new ParseException("Unexpected token: " . gettype($token));
            }

            $value = $this->unserialize($token->getValue());
            if ($token->hasKey()) {
                $key = $this->unserialize($token->getKey());
                $data[$key] = $value;
            } else {
                $data[] = $value;
            }
        }

        return $data;
    }
}
