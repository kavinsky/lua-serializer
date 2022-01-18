<?php

namespace Kavinsky\Lua\Tests;

use Kavinsky\Lua\InputStream;


test('test simple next', function () {
    $obj = new InputStream("a");
    $this->assertEquals("a", $obj->next());
});

test('multiple lines', function () {
    $obj = new InputStream("a\nb\n");

    $this->assertEquals("a", $obj->next());
    $this->assertEquals("\n", $obj->next());
    $this->assertEquals("b", $obj->next());
    $this->assertEquals("\n", $obj->next());
    $this->assertTrue($obj->eof());
});

test('simple error', function () {
    $obj = new InputStream("a");
    $this->assertEquals("a", $obj->next());

    $obj->error("Simple error");
})->throws(\Kavinsky\Lua\ParseException::class, 'Simple error (1:1)');

test('multiple line error', function () {
    $obj = new InputStream("a\nb");
    $this->assertEquals("a", $obj->next());
    $this->assertEquals("\n", $obj->next());
    $this->assertEquals("b", $obj->next());

    $obj->error("Other error");
})->throws(\Kavinsky\Lua\ParseException::class, 'Other error (2:1)');

test('multiple column error', function () {
    $obj = new InputStream("ab");
    $this->assertEquals("a", $obj->next());
    $this->assertEquals("b", $obj->next());

    $obj->error("This error");
})->throws(\Kavinsky\Lua\ParseException::class, 'This error (1:2)');

test('multiple line and column error', function () {
    $obj = new InputStream("ab\nab\n");
    $this->assertEquals("a", $obj->next());
    $this->assertEquals("b", $obj->next());
    $this->assertEquals("\n", $obj->next());

    $this->assertEquals("a", $obj->next());
    $this->assertEquals("b", $obj->next());

    $obj->error("Complex error");
})->throws(\Kavinsky\Lua\ParseException::class, 'Complex error (2:2)');

test('test peek', function () {
    $stream = new InputStream("ab\ncd\nef");

    expect($stream->next())->toBe('a');
    expect($stream->peek())->toBe('b');
});

test('test peek out of limits', function () {
    $stream = new InputStream("ab");

    expect($stream->next())->toBe('a');
    expect($stream->next())->toBe('b');
    $stream->peek();
})->throws(\Kavinsky\Lua\ParseException::class, 'Unexpected end of input (1:2)');
