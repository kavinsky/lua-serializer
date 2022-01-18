<?php

namespace Kavinsky\Lua;

/**
 * Class Lua
 *
 * @author  Koen Vlaswinkel <koen@vlaswinkel.info>
 * @author  Ignacio Muñoz Fernandez <nmunozfernandez@gmail.com>
 * @package Kavinsky\Lua
 */
class Lua
{
    public static $luaKeywords = [
        'and',
        'break',
        'do',
        'else',
        'elseif',
        'end',
        'false',
        'for',
        'function',
        'if',
        'in',
        'local',
        'nil',
        'not',
        'or',
        'repeat',
        'return',
        'then',
        'true',
        'until',
        'while',
    ];

    public static function serialize($data)
    {
        return Serializer::encode($data);
    }

    public static function deserialize($data)
    {
        $parser = new Parser(new TokenStream(new InputStream($data)));
        return LuaToPhpConverter::convertToPhpValue($parser->parse());
    }
}
