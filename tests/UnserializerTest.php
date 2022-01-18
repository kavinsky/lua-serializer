<?php

use Kavinsky\Lua\InputStream;
use Kavinsky\Lua\Parser;
use Kavinsky\Lua\TokenStream;
use Kavinsky\Lua\Unserializer;

test('string', function () {
    $parser = new Parser(new TokenStream(new InputStream('"foo"')));

    $node = $parser->parse();

    $unserializer = new Unserializer();

    expect($unserializer->unserialize($node))
        ->toBe('foo');
});

test('number', function () {
    $parser = new Parser(new TokenStream(new InputStream('1337')));

    $node = $parser->parse();

    $unserializer = new Unserializer();

    expect($unserializer->unserialize($node))
        ->toBe(1337);
});

test('nill', function () {
    $parser = new Parser(new TokenStream(new InputStream('nil')));

    $node = $parser->parse();

    $unserializer = new Unserializer();

    expect($unserializer->unserialize($node))
        ->toBe(null);
});

test('simple table', function () {
    $parser = new Parser(new TokenStream(new InputStream('{ foo = "bar" }')));

    $node = $parser->parse();

    $unserializer = new Unserializer();

    expect($unserializer->unserialize($node))
        ->toBe(['foo' => 'bar']);
});

test('nested table', function () {
    $parser = new Parser(new TokenStream(new InputStream(
        '{ foo = { "bar" = { 1337 } } }'
    )));

    $node = $parser->parse();

    $unserializer = new Unserializer();

    expect($unserializer->unserialize($node))
        ->toBe([
            'foo' => [
                'bar' => [
                    1337,
                ],
            ],
        ]);
});

test('empty table', function () {
    $parser = new Parser(new TokenStream(new InputStream(
        '{}'
    )));

    $node = $parser->parse();

    $unserializer = new Unserializer();

    expect($unserializer->unserialize($node))
        ->toBe([]);
});

test('simple table with comments', function () {
    $parser = new Parser(new TokenStream(new InputStream(
        '{
        foo = "bar" -- comment
        }'
    )));

    $node = $parser->parse();

    $unserializer = new Unserializer();

    expect($unserializer->unserialize($node))
        ->toBe([
            'foo' => 'bar',
        ]);
});

test('hex number table', function () {
    $parser = new Parser(new TokenStream(new InputStream(
        '{0x0ef15a66,0xf10e0e66,0x3e4c5266}'
    )));

    $node = $parser->parse();

    $unserializer = new Unserializer();

    expect($unserializer->unserialize($node))
        ->toBe([0x0ef15a66, 0xf10e0e66, 0x3e4c5266]);
});

test('boolean number table', function () {
    $parser = new Parser(new TokenStream(new InputStream(
        '{boolTrue = true, boolFalse = false}'
    )));

    $node = $parser->parse();

    $unserializer = new Unserializer();

    expect($unserializer->unserialize($node))
        ->toBe([
            'boolTrue' => true,
            'boolFalse' => false,
        ]);
});

test('unregistered ASTNode', function () {
    $node = new class () extends \Kavinsky\Lua\AST\ASTNode {
        public function getName(): string
        {
            return 'random_type';
        }
    };

    $unserializer = new Unserializer();

    $unserializer->unserialize($node);
})->throws(\Kavinsky\Lua\ParseException::class);

test('invalid item in TableASTNode', function () {
    $node = new \Kavinsky\Lua\AST\TableASTNode([
        'test',
    ]);

    $unserializer = new Unserializer();

    $unserializer->unserialize($node);
})->throws(\Kavinsky\Lua\ParseException::class);
