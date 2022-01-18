<?php

namespace Kavinsky\Lua;

/**
 * Class InputStream
 *
 * @see     http://lisperator.net/pltut/parser/input-stream
 *
 * @author  Koen Vlaswinkel <koen@vlaswinkel.info>
 * @author  Ignacio Muñoz Fernandez <nmunozfernandez@gmail.com>
 * @package Kavinsky\Lua
 */
class InputStream
{
    /**
     * @var string
     */
    private string $input;

    /**
     * @var int
     */
    private int $position = 0;
    /**
     * @var int
     */
    private int $line = 1;
    /**
     * @var int
     */
    private int $column = 0;

    /**
     * InputStream constructor.
     *
     * @param string $input
     */
    public function __construct(string $input)
    {
        $this->input = $input;
    }

    public function next(): string
    {
        $char = $this->input[$this->position++];
        if ($char == "\n") {
            $this->line++;
            $this->column = 0;
        } else {
            $this->column++;
        }
        return $char;
    }

    /**
     * @throws ParseException
     */
    public function peek($pos = 0): string
    {
        if ($this->eof($pos)) {
            $this->error('Unexpected end of input');
        }
        return $this->input[$this->position + $pos];
    }

    public function eof($pos = 0)
    {
        return $this->position + $pos >= strlen($this->input);
    }

    public function error($msg)
    {
        throw new ParseException($msg . ' (' . $this->line . ':' . $this->column . ')');
    }
}
