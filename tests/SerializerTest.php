<?php

declare(strict_types=1);

use Kavinsky\Lua\Serializer;

test('serialize null', function () {
    $serializer = new Serializer();

    expect($serializer->serialize(null))
        ->toBe('nil');
});

test('serialize string', function () {
    $serializer = new Serializer();

    expect($serializer->serialize('im a string'))
        ->toBe('"im a string"');

    expect($serializer->serialize("im a string\n im a string"))
        ->toBe(<<<LUA
"im a string\\
 im a string"
LUA);

    expect($serializer->serialize(chr(0x1F) . chr(0x7F)))
        ->toBe('"\\' . chr(0x1F) . '\\' . chr(0x7F) . '"');
});

test('serialize numbers', function () {
    $serializer = new Serializer();

    expect($serializer->serialize(0.69))
        ->toBe('0.69');

    expect($serializer->serialize(69))
        ->toBe('69');
});

test('serialize array', function () {
    $serializer = new Serializer();

    expect($serializer->serialize(['im', 'an', 'array']))
        ->toBe(<<<LUA
{
  "im",
  "an",
  "array",
}
LUA);

    expect($serializer->serialize([]))
        ->toBe('{}');
});

test('serialize stdObject', function () {
    $serializer = new Serializer();

    $obj = new \stdClass();
    $obj->foo = 'bar';
    $obj->{'foo.bar'} = 'baz';
    expect($serializer->serialize($obj))
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

    expect($serializer->serialize($obj))
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

    expect($serializer->serialize($obj))
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

    $serializer->serialize($obj);
})->throws(\InvalidArgumentException::class);


test('serialize boolean', function () {
    $serializer = new Serializer();

    expect($serializer->serialize(true))
        ->toBe('true');

    expect($serializer->serialize(false))
        ->toBe('false');
});

test('serialize resource', function () {
    $resource = @fopen('php://memory', 'r');

    $serializer = new Serializer();

    $serializer->serialize($resource);
})->throws(\InvalidArgumentException::class, 'Cannot encode type resource: NULL');

test('serialize array with flag FLAG_TABLE_KEY_AS_STRING', function () {
    $serializer = new Serializer([
        Serializer::FLAG_TABLE_KEY_AS_STRING,
    ]);

    expect($serializer->serialize(['test' => 'im', 'an', 'array']))
        ->toBe(<<<LUA
{
  "an",
  "array",
  [ "test" ] = "im",
}
LUA);

    expect($serializer->serialize([]))
        ->toBe('{}');
});

test('serialize array with flag FLAG_REMOVE_KEY_PADDING', function () {
    $serializer = new Serializer([
        Serializer::FLAG_REMOVE_KEY_PADDING,
    ]);

    expect($serializer->serialize(['test.test' => 'im', 'an', 'array']))
        ->toBe(<<<LUA
{
  "an",
  "array",
  ["test.test"] = "im",
}
LUA);

    expect($serializer->serialize([]))
        ->toBe('{}');
});
