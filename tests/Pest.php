<?php

use Kavinsky\Lua\InputStream;
use Kavinsky\Lua\TokenStream;

function tokenStreamFromString(string $string): TokenStream
{
    return new TokenStream(new InputStream($string));
}
