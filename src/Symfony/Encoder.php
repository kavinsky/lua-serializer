<?php

namespace Kavinsky\Lua\Symfony;

use Kavinsky\Lua\InputStream;
use Kavinsky\Lua\Parser;
use Kavinsky\Lua\Serializer;
use Kavinsky\Lua\TokenStream;
use Kavinsky\Lua\Unserializer;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;

class Encoder implements EncoderInterface, DecoderInterface
{
    public function encode(mixed $data, string $format, array $context = []): string
    {
        $serializer = new Serializer();

        return $serializer->serialize($data);
    }

    public function supportsEncoding(string $format): bool
    {
        return 'lua' === $format;
    }

    public function decode(string $data, string $format, array $context = []): array
    {
        $parser = new Parser(
            new TokenStream(
                new InputStream(
                    $data
                )
            )
        );

        return (new Unserializer())
            ->unserialize($parser->parse());
    }

    public function supportsDecoding(string $format): bool
    {
        return 'lua' === $format;
    }
}
