<?php

declare(strict_types=1);

use Kavinsky\Lua\ParseException;
use Kavinsky\Lua\Token;

test('double quoted string', function () {
    $obj = tokenStreamFromString('"foo"');

    $token = $obj->next();
    expect($token->getType())->toBe(Token::TYPE_STRING);
    expect($token->getValue())->toBe('foo');
});

test('single quoted string', function () {
    $obj = tokenStreamFromString("'foo'");

    $token = $obj->next();
    expect($token->getType())->toBe(Token::TYPE_STRING);
    expect($token->getValue())->toBe('foo');
});

test('nested string', function () {
    $obj = tokenStreamFromString('[=[ Like this ]=]');

    $token = $obj->next();
    expect($token->getType())->toBe(Token::TYPE_STRING);
    expect($token->getValue())->toBe(' Like this ');
});

test('escaped string', function () {
    $obj = tokenStreamFromString('" test \n\r\t\v\\\\\""');

    $token = $obj->next();
    expect($token->getType())->toBe(Token::TYPE_STRING);
    expect($token->getValue())->toBe(" test \n\r\t\v\\\"");
});

test('other nested string', function () {
    $obj = tokenStreamFromString('[=[one [[two]] one]=]');

    $token = $obj->next();
    expect($token->getType())->toBe(Token::TYPE_STRING);
    expect($token->getValue())->toBe('one [[two]] one');
});

test('nested nested string', function () {
    $obj = tokenStreamFromString('[=[one [==[two]==] one]=]');

    $token = $obj->next();
    expect($token->getType())->toBe(Token::TYPE_STRING);
    expect($token->getValue())->toBe('one [==[two]==] one');
});

test('complex nested string', function () {
    $obj = tokenStreamFromString('[===[one [ [==[ one]===]');

    $token = $obj->next();
    expect($token->getType())->toBe(Token::TYPE_STRING);
    expect($token->getValue())->toBe('one [ [==[ one');
});

test('number integer', function () {
    $obj = tokenStreamFromString('123');

    $token = $obj->next();
    expect($token->getType())->toBe(Token::TYPE_NUMBER);
    expect($token->getValue())->toBe(123);
});

test('number float', function () {
    $obj = tokenStreamFromString('1.23');

    $token = $obj->next();
    expect($token->getType())->toBe(Token::TYPE_NUMBER);
    expect($token->getValue())->toBe(1.23);
});

test('number negative', function () {
    $obj = tokenStreamFromString('-1');

    $token = $obj->next();
    expect($token->getType())->toBe(Token::TYPE_NUMBER);
    expect($token->getValue())->toBe(-1);
});

test('number hex', function () {
    $obj = tokenStreamFromString('0x1A');

    $token = $obj->next();
    expect($token->getType())->toBe(Token::TYPE_NUMBER);
    expect($token->getValue())->toBe(0x1A);
});

test('punctuation', function () {
    foreach ([',', '{', '}', '=', '[', ']'] as $punc) {
        $obj = tokenStreamFromString($punc);

        $token = $obj->next();
        expect($token->getType())
            ->toBe(Token::TYPE_PUNCTUATION);
        expect($token->getValue())
            ->toBe($punc);
    }
});

test('identifier', function () {
    $obj = tokenStreamFromString('foo');

    $token = $obj->next();
    expect($token->getType())
        ->toBe(Token::TYPE_IDENTIFIER);
    expect($token->getValue())
        ->toBe('foo');
});

test('keyword', function () {
    $obj = tokenStreamFromString('function');

    $token = $obj->next();
    expect($token->getType())
        ->toBe(Token::TYPE_KEYWORD);
    expect($token->getValue())
        ->toBe('function');
});

test('invalid character', function () {
    $obj = tokenStreamFromString('*');

    $token = $obj->next();
})->throws(ParseException::class, 'Cannot handle character: * (ord: 42)');

test('unclosed nested string', function () {
    $obj = tokenStreamFromString("[=[ test ]]");

    $obj->next();
})->throws(ParseException::class);

test('invalid double bracket close string', function () {
    $obj = tokenStreamFromString("[==[ test ]== ");

    $obj->next();
})->throws(ParseException::class);
