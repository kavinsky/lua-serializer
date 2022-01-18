<?php

declare(strict_types=1);

use Kavinsky\Lua\AST\BoolASTNode;
use Kavinsky\Lua\AST\NilASTNode;
use Kavinsky\Lua\AST\NumberASTNode;
use Kavinsky\Lua\AST\StringASTNode;
use Kavinsky\Lua\AST\TableASTNode;
use Kavinsky\Lua\InputStream;
use Kavinsky\Lua\Parser;
use Kavinsky\Lua\TokenStream;

test('string', function () {
    $parser = new Parser(new TokenStream(new InputStream('"foo"')));

    $node = $parser->parse();

    $this->assertEquals(StringASTNode::NAME, $node->getName());
    $this->assertInstanceOf(StringASTNode::class, $node);
    $this->assertEquals("foo", $node->getValue());
});

test('string with spaces', function () {
    $parser = new Parser(new TokenStream(new InputStream('"foo bar."')));

    $node = $parser->parse();

    $this->assertEquals(StringASTNode::NAME, $node->getName());
    $this->assertInstanceOf(StringASTNode::class, $node);
    $this->assertEquals("foo bar.", $node->getValue());
});

test('alternate string', function () {
    $parser = new Parser(new TokenStream(new InputStream('[[foo]]')));

    $node = $parser->parse();

    $this->assertEquals(StringASTNode::NAME, $node->getName());
    $this->assertInstanceOf(StringASTNode::class, $node);
    $this->assertEquals("foo", $node->getValue());
});

test('alternate string with spaces', function () {
    $parser = new Parser(new TokenStream(new InputStream('[[foo bar.]]')));

    $node = $parser->parse();

    $this->assertEquals(StringASTNode::NAME, $node->getName());
    $this->assertInstanceOf(StringASTNode::class, $node);
    $this->assertEquals("foo bar.", $node->getValue());
});

test('number', function () {
    $parser = new Parser(new TokenStream(new InputStream('1337')));

    $node = $parser->parse();

    $this->assertEquals(NumberASTNode::NAME, $node->getName());
    $this->assertInstanceOf(NumberASTNode::class, $node);
    $this->assertEquals(1337, $node->getValue());
});

test('nil', function () {
    $parser = new Parser(new TokenStream(new InputStream('nil')));

    $node = $parser->parse();

    $this->assertEquals(NilASTNode::NAME, $node->getName());
    $this->assertInstanceOf(NilASTNode::class, $node);
});

test('bool true', function () {
    $parser = new Parser(new TokenStream(new InputStream('true')));

    $node = $parser->parse();

    $this->assertEquals(BoolASTNode::NAME, $node->getName());
    $this->assertInstanceOf(BoolASTNode::class, $node);
    $this->assertEquals(true, $node->getValue());
});

test('bool false', function () {
    $parser = new Parser(new TokenStream(new InputStream('false')));

    $node = $parser->parse();

    $this->assertEquals(BoolASTNode::NAME, $node->getName());
    $this->assertInstanceOf(BoolASTNode::class, $node);
    $this->assertEquals(false, $node->getValue());
});

test('table key', function () {
    $parser = new Parser(new TokenStream(new InputStream('["test"]')));

    $node = $parser->parse();

    $this->assertEquals(StringASTNode::NAME, $node->getName());
    $this->assertInstanceOf(StringASTNode::class, $node);
    $this->assertEquals("test", $node->getValue());
});

test('simple table', function () {
    $parser = new Parser(
        new TokenStream(
            new InputStream(
                '{
            foo = "bar"
        }'
            )
        )
    );

    $node = $parser->parse();

    $this->assertEquals(TableASTNode::NAME, $node->getName());
    $this->assertInstanceOf(TableASTNode::class, $node);

    $this->assertCount(1, $node->getEntries());
    $entry = $node->getEntries()[0];

    $this->assertTrue($entry->hasKey());
    $this->assertEquals(StringASTNode::NAME, $entry->getKey()->getName());
    $this->assertInstanceOf(StringASTNode::class, $entry->getKey());
    $this->assertEquals("foo", $entry->getKey()->getValue());

    $this->assertEquals(StringASTNode::NAME, $entry->getValue()->getName());
    $this->assertInstanceOf(StringASTNode::class, $entry->getValue());
    $this->assertEquals("bar", $entry->getValue()->getValue());
});

test('nested table', function () {
    $parser = new Parser(
        new TokenStream(
            new InputStream(
                '{
            foo = {
                ["test"] = {
                    1337,
                    "bar"
                }
            }
        }'
            )
        )
    );

    $node = $parser->parse();

    $this->assertEquals(TableASTNode::NAME, $node->getName());
    $this->assertInstanceOf(TableASTNode::class, $node);

    $this->assertCount(1, $node->getEntries());
    $entry = $node->getEntries()[0];

    $this->assertTrue($entry->hasKey());
    $this->assertEquals(StringASTNode::NAME, $entry->getKey()->getName());
    $this->assertInstanceOf(StringASTNode::class, $entry->getKey());
    $this->assertEquals("foo", $entry->getKey()->getValue());

    $this->assertEquals(TableASTNode::NAME, $entry->getValue()->getName());
    $this->assertInstanceOf(TableASTNode::class, $entry->getValue());
    $this->assertCount(1, $entry->getValue()->getEntries());

    $nestedEntry = $entry->getValue()->getEntries()[0];

    $this->assertTrue($nestedEntry->hasKey());
    $this->assertEquals(StringASTNode::NAME, $nestedEntry->getKey()->getName());
    $this->assertInstanceOf(StringASTNode::class, $nestedEntry->getKey());
    $this->assertEquals("test", $nestedEntry->getKey()->getValue());

    $this->assertEquals(TableASTNode::NAME, $nestedEntry->getValue()->getName());
    $this->assertInstanceOf(TableASTNode::class, $nestedEntry->getValue());
    $this->assertCount(2, $nestedEntry->getValue()->getEntries());

    $nestedNestedEntry1 = $nestedEntry->getValue()->getEntries()[0];

    $this->assertFalse($nestedNestedEntry1->hasKey());

    $this->assertEquals(NumberASTNode::NAME, $nestedNestedEntry1->getValue()->getName());
    $this->assertInstanceOf(NumberASTNode::class, $nestedNestedEntry1->getValue());
    $this->assertEquals(1337, $nestedNestedEntry1->getValue()->getValue());

    $nestedNestedEntry2 = $nestedEntry->getValue()->getEntries()[1];

    $this->assertFalse($nestedNestedEntry2->hasKey());

    $this->assertEquals(StringASTNode::NAME, $nestedNestedEntry2->getValue()->getName());
    $this->assertInstanceOf(StringASTNode::class, $nestedNestedEntry2->getValue());
    $this->assertEquals("bar", $nestedNestedEntry2->getValue()->getValue());
});

test('table with nested alternate strings', function () {
    $parser = new Parser(
        new TokenStream(
            new InputStream(
                '{
            foo = [[bar]]
        }'
            )
        )
    );

    $node = $parser->parse();

    $this->assertEquals(TableASTNode::NAME, $node->getName());
    $this->assertInstanceOf(TableASTNode::class, $node);

    $this->assertCount(1, $node->getEntries());
    $entry = $node->getEntries()[0];

    $this->assertTrue($entry->hasKey());
    $this->assertEquals(StringASTNode::NAME, $entry->getKey()->getName());
    $this->assertInstanceOf(StringASTNode::class, $entry->getKey());
    $this->assertEquals("foo", $entry->getKey()->getValue());

    $this->assertEquals(StringASTNode::NAME, $entry->getValue()->getName());
    $this->assertInstanceOf(StringASTNode::class, $entry->getValue());
    $this->assertEquals("bar", $entry->getValue()->getValue());
});

test('invalid', function () {
    $parser = new Parser(new TokenStream(new InputStream('{ test[bar }')));

    $parser->parse();
})->throws(\Kavinsky\Lua\ParseException::class);

test('invalid keyword', function () {
    $parser  = new Parser(new TokenStream(new InputStream('function')));

    $node = $parser->parse();
    $this->assertEquals('test', $node->getName());
})->throws(\Kavinsky\Lua\ParseException::class);

test('comments', function () {
    $parser = new Parser(new TokenStream(new InputStream('{
        -- comment
    foo = {
        test = 123
    }
}')));

    /** @var TableASTNode $node */
    $node = $parser->parse();
    expect($node)
        ->toBeInstanceOf(TableASTNode::class);

    expect($node->getEntries())
        ->toBeArray();

    expect($node->getEntries())
        ->toHaveCount(1);
});
