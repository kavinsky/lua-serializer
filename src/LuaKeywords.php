<?php

declare(strict_types=1);

namespace Kavinsky\Lua;

enum LuaKeywords: string
{
    case AND = 'and';
    case BREAK = 'break';
    case DO = 'do';
    case ELSE = 'else';
    case ELSEIF = 'elseif';
    case END = 'end';
    case FALSE = 'false';
    case FOR = 'for';
    case FUNC = 'function';
    case IF = 'if';
    case IN = 'in';
    case LOCAL = 'local';
    case NIL = 'nil';
    case NOT = 'not';
    case OR = 'or';
    case REPEAT = 'repeat';
    case RETURN = 'return';
    case THEN = 'then';
    case TRUE = 'true';
    case UNTIL = 'until';
    case WHILE = 'while';

    /**
     * Returns an array of LuaKeywords values.
     *
     * @returns string[]
     */
    public static function values(): array
    {
        return array_map(fn (LuaKeywords $keyword) => $keyword->value, self::cases());
    }
}
