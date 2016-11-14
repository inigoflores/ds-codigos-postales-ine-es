<?php

namespace ConsoleKit\Tests;

use ConsoleKit\DefaultOptionsParser;

class DefaultOptionsParserTest extends ConsoleKitTestCase
{
    public function testParse()
    {
        $parser = new DefaultOptionsParser();

        list($args, $options) = $parser->parse(array('arg1'));
        $this->assertContains('arg1', $args);
        $this->assertEmpty($options);

        list($args, $options) = $parser->parse(array('arg1', 'arg2'));
        $this->assertContains('arg1', $args);
        $this->assertContains('arg2', $args);
        $this->assertEmpty($options);

        list($args, $options) = $parser->parse(array('--foo', 'arg1'));
        $this->assertContains('arg1', $args);
        $this->assertArrayHasKey('foo', $options);

        list($args, $options) = $parser->parse(array('--foo=bar', '--foobar'));
        $this->assertCount(2, $options);
        $this->assertArrayHasKey('foo', $options);
        $this->assertEquals('bar', $options['foo']);
        $this->assertArrayHasKey('foobar', $options);

        list($args, $options) = $parser->parse(array('--foo=bar', '--foo=baz'));
        $this->assertArrayHasKey('foo', $options);
        $this->assertInternalType('array', $options['foo']);
        $this->assertContains('bar', $options['foo']);
        $this->assertContains('baz', $options['foo']);

        list($args, $options) = $parser->parse(array('-a', '-bc'));
        $this->assertCount(3, $options);
        $this->assertArrayHasKey('a', $options);
        $this->assertArrayHasKey('b', $options);
        $this->assertArrayHasKey('c', $options);
    }
}
