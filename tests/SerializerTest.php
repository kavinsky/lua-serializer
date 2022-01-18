<?php

declare(strict_types=1);

use Kavinsky\Lua\Serializer;

test('serialize null', function () {
    $serializer = new Serializer();

    expect($serializer->encode(null))
        ->toBe('nil');
});

test('serialize string', function () {
    $serializer = new Serializer();

    expect($serializer->encode('im a string'))
        ->toBe('"im a string"');

    expect($serializer->encode("im a string\n im a string"))
        ->toBe(<<<LUA
"im a string\\
 im a string"
LUA);

    expect($serializer->encode(chr(0x1F) . chr(0x7F)))
        ->toBe('"\\' . chr(0x1F) . '\\' . chr(0x7F) . '"');
});

test('serialize numbers', function () {
    $serializer = new Serializer();

    expect($serializer->encode(0.69))
        ->toBe('0.69');

    expect($serializer->encode(69))
        ->toBe('69');
});

test('serialize array', function () {
    $serializer = new Serializer();

    expect($serializer->encode(['im', 'an', 'array']))
        ->toBe(<<<LUA
{
  "im",
  "an",
  "array",
}
LUA);

    expect($serializer->encode([]))
        ->toBe('{}');
});

test('serialize stdObject', function () {
    $serializer = new Serializer();

    $obj = new \stdClass();
    $obj->foo = 'bar';
    $obj->{'foo.bar'} = 'baz';
    expect($serializer->encode($obj))
        ->toBe(<<<LUA
{
  foo = "bar",
  [ "foo.bar" ] = "baz",
}
LUA);
});

test('serialize iterables', function () {
    $serializer = new Serializer();

    $obj = new ArrayIterator([
        'im' => 'an',
        'array' => 'of',
        'iterables',
    ]);

    expect($serializer->encode($obj))
        ->toBe(<<<LUA
{
  "iterables",
  im = "an",
  array = "of",
}
LUA);
});

test('serialize Arrayable', function () {
    $serializer = new Serializer();

    $obj = new class () implements \Illuminate\Contracts\Support\Arrayable {
        public function toArray()
        {
            return [
                'im' => 'an',
                'array' => 'of',
                'iterables',
            ];
        }
    };

    expect($serializer->encode($obj))
        ->toBe(<<<LUA
{
  "iterables",
  im = "an",
  array = "of",
}
LUA);
});

test('serialize not supported object', function () {
    $serializer = new Serializer();

    $obj = new class () {
    };

    $serializer->encode($obj);
})->throws(\InvalidArgumentException::class);


test('serialize boolean', function () {
    $serializer = new Serializer();

    expect($serializer->encode(true))
        ->toBe('true');

    expect($serializer->encode(false))
        ->toBe('false');
});

test('serialize resource', function () {
    $resource = @fopen('php://memory', 'r');

    $serializer = new Serializer();

    $serializer->encode($resource);
})->throws(\InvalidArgumentException::class, 'Cannot encode type resource: NULL');
