<?php

declare(strict_types=1);

use Doctrine\Common\Annotations\AnnotationReader;
use Kavinsky\Lua\Tests\Symfony\DummyObject;
use Kavinsky\Lua\Tests\Symfony\DummySubObject;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\NameConverter\MetadataAwareNameConverter;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

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


test('symfony/serializer serialize', function () {
    $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));

    $metadataAwareNameConverter = new MetadataAwareNameConverter($classMetadataFactory);

    $sfSerializer = new \Symfony\Component\Serializer\Serializer(
        [new ObjectNormalizer($classMetadataFactory, $metadataAwareNameConverter, null)],
        [new \Kavinsky\Lua\Symfony\Encoder()]
    );

    $obj = new DummyObject();

    $obj->setDummyBool(false);
    $obj->setDummyFloat(0.5);
    $obj->setDummyInt(1);
    $obj->setDummyString('foo');
    $obj->setDummyArray([
        'test.test' => 'test',
    ]);
    $subObj = new DummySubObject();
    $subObj->setMegaString('TEST');
    $obj->setDummySubObject($subObj);

    $serialized = <<<LUA
{
  my_string = "foo",
  dummyInt = 1,
  dummyFloat = 0.5,
  dummyBool = false,
  dummyArray = {
    [ "test.test" ] = "test",
  },
  dummySubObject = {
    megaString = "TEST",
  },
}
LUA;


    expect($sfSerializer->serialize($obj, 'lua'))
        ->toBe($serialized);
});

test('symfony/serializer deserialize', function () {
    $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));

    $metadataAwareNameConverter = new MetadataAwareNameConverter($classMetadataFactory);

    $sfSerializer = new \Symfony\Component\Serializer\Serializer(
        [new ObjectNormalizer($classMetadataFactory, $metadataAwareNameConverter, null, new ReflectionExtractor())],
        [new \Kavinsky\Lua\Symfony\Encoder()]
    );

    $expected = new DummyObject();

    $expected->setDummyBool(false);
    $expected->setDummyFloat(0.5);
    $expected->setDummyInt(1);
    $expected->setDummyString('foo');
    $expected->setDummyArray([
        'test.test' => 'test',
    ]);

    $serialized = <<<LUA
{
  my_string = "foo",
  dummyInt = 1,
  dummyFloat = 0.5,
  dummyBool = false,
  dummyArray = {
      [ "test.test" ] = "test",
  },
  dummySubObject = {
    megaString = "megaString",
  }
}
LUA;
    $actual = $sfSerializer->deserialize($serialized, DummyObject::class, 'lua');

    expect($actual)
        ->toBeInstanceOf(DummyObject::class);

    expect($actual->getDummyBool())
        ->toBe($expected->getDummyBool());
    expect($actual->getDummyInt())
        ->toBe($expected->getDummyInt());
    expect($actual->getDummyFloat())
        ->toBe($expected->getDummyFloat());
    expect($actual->getDummyString())
        ->toBe($expected->getDummyString());
    expect($actual->getDummyArray())
        ->toBe($expected->getDummyArray());

    expect($actual->getDummySubObject())
        ->toBeInstanceOf(DummySubObject::class);
    expect($actual->getDummySubObject()->getMegaString())
        ->toBe('megaString');
});
