# PHP Lua Serializer/Deserializer

__This fork is updated to PHP8.1^__

This is a very simple Lua serializer/deserializer for PHP, with support for [Symfony Serializer](https://github.com/symfony/serializer).

## Installation

Install with [Composer](http://getcomposer.org):

```
composer require koesie10/lua-serializer
```

## Usage

There is a facade for both serialization and deserialization:

```php
$result = \Kavinsky\Lua\Lua::serialize([ 'foo' => 'bar']);
/**
 * Returns:
 * {
 *      foo = "bar",
 * }
 */

$result = \Kavinsky\Lua\Lua::deserialize('{ foo = "bar" }')
/**
 * Returns:
 * [ 'foo' => 'bar' ]
 */
```

You can also use all the classes yourself:

```php
$result = \Kavinsky\Lua\Serializer::serialize([ 'foo' => 'bar' ]); // returns the same as above

$parser = new \Kavinsky\Lua\Parser(new \Kavinsky\Lua\TokenStream(new \Kavinsky\Lua\InputStream('{ foo = "bar" }')));
$result = \Kavinsky\Lua\Unserializer::convertToPhpValue($parser->parse()); // returns the same as above
```

### Integration with JMS Serializer in Symfony2

This serializer can easily be included into Symfony2 with JMSSerializer, by adding the following lines to your `services.yml`:

```yaml
services:
  app.lua.serialization_visitor:
    class: Kavinsky\Lua\JMS\LuaSerializationVisitor
    arguments: ["@jms_serializer.naming_strategy"]
    tags:
      - { name: jms_serializer.serialization_visitor, format: lua }
  app.lua.deserialization_visitor:
    class: Kavinsky\Lua\JMS\LuaDeserializationVisitor
    arguments: ["@jms_serializer.naming_strategy"]
    tags:
      - { name: jms_serializer.deserialization_visitor, format: lua }
  app.lua.array_handler:
    class: Kavinsky\Lua\JMS\LuaArrayCollectionHandler
    tags:
      - { name: jms_serializer.subscribing_handler }
  app.lua.date_handler:
    class: Kavinsky\Lua\JMS\LuaDateHandler
    tags:
      - { name: jms_serializer.subscribing_handler }
  app.lua_constraint_violation_handler:
    class: Kavinsky\Lua\JMS\LuaConstraintViolationHandler
    tags:
      - { name: jms_serializer.subscribing_handler }
  app.lua.form_handler:
    class: Kavinsky\Lua\JMS\LuaFormHandler
    arguments: ["@translator"]
    tags:
      - { name: jms_serializer.subscribing_handler }
  app.lua_php_collection_handler:
    class: Kavinsky\Lua\JMS\LuaPhpCollectionHandler
    tags:
      - { name: jms_serializer.subscribing_handler }
```

If you want Symfony to recognize Lua as a request/response format, also add the following to your `config.yml`:

```
framework:
    request:
        formats:
            lua: ['application/x-lua', 'application/lua']
```

In this case I've added `application/x-lua` and `application/lua` as MIME-types, but as there's no standard for Lua
content types, these can be whatever you like.

You can now use the format `lua` for serialization:

```php
public function indexAction() {
    return $this->get('serializer')->serialize([ 'foo' => 'bar' ], 'lua');
}
```

## Running tests

You can run automated unit tests using [PHPUnit](http://phpunit.de) after installing dependencies:

```
vendor/bin/phpunit
```

## License

This library is licensed under the MIT license. See the [LICENSE](LICENSE) file for details.
