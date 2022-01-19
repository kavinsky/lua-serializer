<?php

declare(strict_types=1);

namespace Kavinsky\Lua;

use Illuminate\Contracts\Support\Arrayable;

/**
 * Class Serializer
 *
 * @see     https://github.com/Sorroko/cclite/blob/62677542ed63bd4db212f83da1357cb953e82ce3/src/lua/rom/apis/textutils
 *
 * @author  Koen Vlaswinkel <koen@vlaswinkel.info>
 * @author  Ignacio Muñoz Fernandez <nmunozfernandez@gmail.com>
 * @package Kavinsky\Lua
 */
class Serializer
{
    /**
     * Force all table keys to be in [ "key" ] format.
     */
    public const FLAG_TABLE_KEY_AS_STRING = 2;

    /**
     * Remove spaces between [ "key" ] in tables.
     */
    public const FLAG_REMOVE_KEY_PADDING = 4;

    /**
     * Set of flags used by the serializer.
     *
     * @param int[] $flags
     */
    private array $flags = [];

    public function __construct(array $flags = [])
    {
        $this->flags = $flags;
    }

    public function serialize(mixed $data, $indent = ''): string
    {
        return match (gettype($data)) {
            'NULL' => $this->encodeNull(),
            'array' => $this->encodeArray((array) $data, $indent),
            'object' => $this->encodeObject($data, $indent),
            'string' => $this->encodeString($data),
            'double', 'integer' => $this->encodeNumber($data),
            'boolean' => $this->encodeBoolean($data),
            default => throw new \InvalidArgumentException(
                "Cannot encode type " . gettype($data) . ": " . var_export($data, true)
            )
        };
    }

    /**
     * @param  iterable|Arrayable|\stdClass|mixed  $data
     * @param string $indent
     * @return string
     */
    private function encodeObject(mixed $data, string $indent = ''): string
    {
        if (is_iterable($data)) {
            return $this->encodeArray(iterator_to_array($data), $indent);
        }

        if ($data instanceof \stdClass) {
            return $this->encodeArray((array) $data, $indent);
        }

        if ($data instanceof Arrayable) {
            return $this->encodeArray($data->toArray(), $indent);
        }

        throw new \InvalidArgumentException(
            "Cannot encode object " . get_class($data) . ": " . var_export($data, true)
        );
    }

    private function encodeArray(array $data, $indent): string
    {
        if (count($data) === 0) {
            return '{}';
        }

        $openBracket = '[ ';
        $closeBraket = ' ]';

        if ($this->hasFlag(self::FLAG_REMOVE_KEY_PADDING)) {
            $openBracket = '[';
            $closeBraket = ']';
        }


        $result = "{\n";
        $subIndent = $indent . '  ';
        $seen = [];
        foreach ($data as $key => $value) {
            if (is_int($key)) {
                $seen[$key] = true;
                $result .= $subIndent . $this->serialize($value, $subIndent) . ",\n";
            }
        }

        foreach ($data as $key => $value) {
            if (! array_key_exists($key, $seen)) {
                if ($this->hasFlag(self::FLAG_TABLE_KEY_AS_STRING) || ! $this->isTableKey($key)) {
                    $entry = $openBracket . $this->serialize($key, $subIndent) . $closeBraket . ' = ' . $this->serialize($value, $subIndent) . ",\n";
                } else {
                    $entry = $key . ' = ' . $this->serialize($value, $subIndent) . ",\n";
                }
                $result = $result . $subIndent . $entry;
            }
        }

        return $result . $indent . '}';
    }

    /**
     * @see http://luaj.cvs.sourceforge.net/viewvc/luaj/luaj-vm/src/core/org/luaj/vm2/lib/StringLib.java?view=markup
     * @return string
     */
    private function encodeString(string $data): string
    {
        $data = str_replace(["\n\r", "\r\n"], "\n", $data);
        $result = '"';
        for ($i = 0, $n = strlen($data); $i < $n; $i++) {
            $char = $data[$i];
            switch ($char) {
                case '"':
                case "\\":
                case "\n":
                    $result .= "\\" . $char;

                    break;
                default:
                    if (($char <= chr(0x1F) || $char == chr(0x7F)) && $char != chr(9)) {
                        $result .= "\\";
                        if ($i + 1 == $n || $data[$i + 1] < '0' || $data[$i + 1] > '9') {
                            $result .= $char;
                        } else {
                            $result .= '0';
                            $result .= chr(ord('0') + $char / 10);
                            $result .= chr(ord('0') + $char % 10);
                        }
                    } else {
                        $result .= $char;
                    }
            }
        }
        $result .= '"';

        return $result;
    }

    private function encodeNumber($data): string
    {
        return (string) $data;
    }

    private function encodeBoolean($data): string
    {
        return $data ? 'true' : 'false';
    }

    private function encodeNull(): string
    {
        return 'nil';
    }

    private function isTableKey(string $key): bool
    {
        return is_string($key)
            && ! LuaKeywords::tryFrom($key)
            && preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $key);
    }

    private function hasFlag(int $flag): bool
    {
        return in_array($flag, $this->flags);
    }
}
