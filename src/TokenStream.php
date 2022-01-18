<?php

namespace Kavinsky\Lua;

/**
 * Class TokenStream
 *
 * @see     http://lisperator.net/pltut/parser/token-stream
 *
 * @author  Koen Vlaswinkel <koen@vlaswinkel.info>
 * @author  Ignacio Muñoz Fernandez <nmunozfernandez@gmail.com>
 * @package Kavinsky\Lua
 */
class TokenStream
{
    private $current = null;
    /**
     * @var InputStream
     */
    private $input;

    /**
     * TokenStream constructor.
     *
     * @param InputStream $input
     */
    public function __construct(InputStream $input)
    {
        $this->input = $input;
    }

    /**
     * @return Token
     */
    public function next()
    {
        $token         = $this->current;
        $this->current = null;
        if ($token) {
            return $token;
        }
        return $this->readNext();
    }

    /**
     * @return bool
     */
    public function eof()
    {
        return $this->peek() == null;
    }

    /**
     * @return Token
     */
    public function peek()
    {
        if ($this->current) {
            return $this->current;
        }
        $this->current = $this->readNext();
        return $this->current;
    }

    /**
     * @param string $msg
     *
     * @throws ParseException
     */
    public function error($msg)
    {
        $this->input->error($msg);
    }

    /**
     * @return Token|null
     * @throws ParseException
     */
    protected function readNext(): ?Token
    {
        $this->readWhile([$this, 'isWhitespace']);
        if ($this->input->eof()) {
            return null;
        }

        $char = $this->input->peek();
        if ($this->isComment()) {
            $this->skipComment();
            return $this->readNext();
        }

        if ($char == '"') {
            return $this->readDoubleQuotedString();
        }

        if ($char == '\'') {
            return $this->readSingleQuotedString();
        }

        if ($this->isDoubleBracketString()) {
            return $this->readDoubleBracketString();
        }

        if ($this->isDigit($char)) {
            return $this->readNumber();
        }

        if ($char === '-') {
            return $this->readNumber();
        }

        if ($this->isStartIdentifierCharacter($char)) {
            return $this->readIdentifier();
        }

        if ($this->isPunctuation($char)) {
            return $this->readPunctuation();
        }

        if ($char == ';') {
            $this->input->next(); // skip the semi-colon
            return $this->readNext(); // just move on to the next
        }

        $this->input->error('Cannot handle character: ' . $char . ' (ord: ' . ord($char) . ')');

        return null;
    }

    protected function skipComment()
    {
        $this->readWhile(
            function ($char) {
                return $char != "\n";
            }
        );
        if (!$this->input->eof()) {
            $this->input->next();
        }
    }

    /**
     * @return Token
     */
    protected function readDoubleQuotedString()
    {
        return new Token(Token::TYPE_STRING, $this->readEscaped('"'));
    }

    /**
     * @return Token
     */
    protected function readSingleQuotedString()
    {
        return new Token(Token::TYPE_STRING, $this->readEscaped('\''));
    }

    /**
     * @return Token
     */
    protected function readDoubleBracketString()
    {
        // we cannot use readEscaped because it only supports a single char as $end
        // and we do not support escaping in double bracke strings
        $str                      = "";
        $startNumberOfEqualsSigns = 0;
        // skip both
        $this->input->next();
        while ($this->input->peek() == '=') {
            $startNumberOfEqualsSigns++;
            $this->input->next();
        }
        if ($this->input->peek() != '[') {
            $this->error('Unexpected character \'' . $this->input->peek() . '\', expected \'[\'');
        }
        $this->input->next();
        while (!$this->input->eof()) {
            $char = $this->input->next();
            if ($char == ']') { // we might have reached the end
                if ($startNumberOfEqualsSigns != 0) {
                    if ($this->input->peek() == '=') {
                        $endNumberOfEqualsSigns = 0;
                        while ($this->input->peek() == '=') {
                            $endNumberOfEqualsSigns++;
                            $this->input->next();
                        }

                        // we have an equal number of equal signs
                        if ($endNumberOfEqualsSigns == $startNumberOfEqualsSigns) {
                            if ($this->input->peek() != ']') {
                                $this->error('Unexpected character \'' . $this->input->peek() . '\', expected \'[\'');
                            }
                            $this->input->next();
                            break;
                        } else {
                            $str .= $char . str_repeat('=', $endNumberOfEqualsSigns);
                        }
                    } else {
                        $str .= $char;
                    }
                } else {
                    if ($this->input->peek() == ']') {
                        $this->input->next();
                        break;
                    }
                }
            } else {
                $str .= $char;
            }
        }
        return new Token(Token::TYPE_STRING, $str);
    }

    /**
     * @param string $end
     *
     * @return string
     */
    protected function readEscaped($end)
    {
        $escaped = false;
        $str     = "";
        $this->input->next();
        while (!$this->input->eof()) {
            $char = $this->input->next();
            if ($escaped) {
                switch ($char) {
                    case 'n':
                        $str .= "\n";
                        break;
                    case 'r':
                        $str .= "\r";
                        break;
                    case 't':
                        $str .= "\t";
                        break;
                    case 'v':
                        $str .= "\v";
                        break;
                    default:
                        $str .= $char;
                        break;
                }
                $escaped = false;
            } else {
                if ($char == "\\") {
                    $escaped = true;
                } else {
                    if ($char == $end) {
                        break;
                    } else {
                        $str .= $char;
                    }
                }
            }
        }
        return $str;
    }

    /**
     * @return Token
     */
    protected function readNumber()
    {
        $isNegative = false;

        if ($this->input->peek() === '-') {
            $isNegative = true;
            $this->input->next();
        }

        if ($this->input->peek() === '0') {
            // there is no octal support according to to https://www.lua.org/manual/5.3/manual.html#3.1,
            // so we can just skip a 0

            if ($this->input->nextAndPeek() === 'x') {
                $this->input->next();

                $number = $this->readHexValue();

                return new Token(Token::TYPE_NUMBER, $number);
            }
        }

        $number = $this->readNumberValue();

        if ($isNegative) {
            $number *= -1;
        }

        return new Token(Token::TYPE_NUMBER, $number);
    }

    private function readNumberValue()
    {
        $hasDot = false;
        $number = $this->readWhile(
            function ($char) use (&$hasDot) {
                if ($char === '.') {
                    if ($hasDot) {
                        return false;
                    }
                    $hasDot = true;
                    return true;
                }
                return $this->isDigit($char);
            }
        );

        return $hasDot ? floatval($number) : intval($number);
    }

    private function readHexValue()
    {
        $number = $this->readWhile([$this, 'isHexDigit']);

        return hexdec($number);
    }

    /**
     * @return Token
     */
    protected function readIdentifier()
    {
        $identifier = $this->readWhile(fn ($char) => $this->isIdentifierCharacter($char));

        if ($this->isKeyword($identifier)) {
            return new Token(Token::TYPE_KEYWORD, $identifier);
        }

        return new Token(Token::TYPE_IDENTIFIER, $identifier);
    }

    /**
     * @return Token
     */
    protected function readPunctuation()
    {
        return new Token(Token::TYPE_PUNCTUATION, $this->input->next());
    }

    /**
     * @param callable $predicate
     *
     * @return string
     */
    protected function readWhile(callable $predicate)
    {
        $str = "";
        while (!$this->input->eof() && call_user_func($predicate, $this->input->peek())) {
            $str .= $this->input->next();
        }
        return $str;
    }

    /**
     * @param string $char
     *
     * @return bool
     */
    protected function isWhitespace($char)
    {
        return strpos(" \t\n\r", $char) !== false;
    }

    /**
     * @param string $char
     *
     * @return bool
     */
    protected function isDigit($char)
    {
        return is_numeric($char);
    }

    /**
     * @param string $char
     *
     * @return bool
     */
    protected function isHexDigit($char)
    {
        return $this->isDigit($char) || preg_match('/[a-fA-F]/', $char) === 1;
    }

    /**
     * @return bool
     */
    protected function isDoubleBracketString()
    {
        return $this->input->peek() == '[' && !$this->input->eof(1) && ($this->input->peek(1) == '[' || $this->input->peek(1) == '=');
    }

    /**
     * @return bool
     */
    protected function isComment()
    {
        return $this->input->peek() == '-' && !$this->input->eof(1) && $this->input->peek(1) == '-';
    }

    /**
     * @param string $char
     *
     * @return bool
     */
    protected function isStartIdentifierCharacter($char)
    {
        return preg_match('/[a-zA-Z_]/', $char) === 1;
    }

    /**
     * @param string $char
     *
     * @return bool
     */
    protected function isIdentifierCharacter($char)
    {
        return preg_match('/[a-zA-Z0-9_]/', $char) === 1;
    }

    /**
     * @param string $char
     *
     * @return bool
     */
    protected function isPunctuation($char)
    {
        return strpos(",{}=[]", $char) !== false;
    }

    /**
     * @param string $text
     *
     * @return bool
     */
    protected function isKeyword($text)
    {
        return in_array($text, Lua::$luaKeywords);
    }
}
