<?php

declare(strict_types=1);

test('encode', function () {
    $encoder = new \Kavinsky\Lua\Symfony\Encoder();

    expect($encoder->encode(['foo' => 'bar'], 'lua'))
        ->toBe('{
  foo = "bar",
}');
});

test('decode', function () {
    $encoder = new \Kavinsky\Lua\Symfony\Encoder();

    expect($encoder->decode('{foo = "bar",}', 'lua'))
        ->toBe(['foo' => 'bar']);
});

test('formatSupport', function () {
    $encoder = new \Kavinsky\Lua\Symfony\Encoder();

    expect($encoder->supportsDecoding('lua'))
        ->toBeTrue();

    expect($encoder->supportsEncoding('lua'))
        ->toBeTrue();
});
