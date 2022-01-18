<?php

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
class LuaToPhpConverter
{
    /**
     * @param ASTNode $input
     *
     * @return array
     * @throws ParseException
     */
    public static function convertToPhpValue($input)
    {
        return self::parseValue($input);
    }

    /**
     * @param ASTNode $input
     *
     * @return mixed
     * @throws ParseException
     */
    private static function parseValue($input)
    {
        if ($input instanceof TableASTNode) {
            return self::parseTable($input);
        }
        if (!($input instanceof ASTNode)) {
            throw new ParseException("Unexpected AST node: " . get_class($input));
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
     * @param $input
     *
     * @return array
     * @throws ParseException
     */
    private static function parseTable($input)
    {
        $data = [];
        if (!($input instanceof TableASTNode)) {
            throw new ParseException("Unexpected AST node: " . get_class($input));
        }
        foreach ($input->getEntries() as $token) {
            if (!($token instanceof TableEntryASTNode)) {
                throw new ParseException("Unexpected token: " . $token->getName());
            }
            $value = self::parseValue($token->getValue());
            if ($token->hasKey()) {
                $key        = self::parseValue($token->getKey());
                $data[$key] = $value;
            } else {
                $data[] = $value;
            }
        }
        return $data;
    }
}
