<?php

namespace ConsoleKit\Tests;

use ConsoleKit\Utils;

class UtilsTests extends ConsoleKitTestCase
{
    public function testGet()
    {
        $data = array('foo' => 'bar');
        $this->assertEquals('bar', Utils::get($data, 'foo'));
        $this->assertNull(Utils::get($data, 'unknown'));
        $this->assertEquals('default', Utils::get($data, 'unknown', 'default'));
    }

    public function testFind()
    {
        $this->assertEquals(realpath(__FILE__), Utils::find(basename(__FILE__), __DIR__));
    }

    public function testFilterFiles()
    {
        $this->assertEquals(array(__FILE__), Utils::filterFiles(array(__FILE__, 'not_existant_file')));
    }

    public function testJoin()
    {
        $path = "foo" . DIRECTORY_SEPARATOR . "bar";
        $this->assertEquals($path, Utils::join('foo', 'bar'));
    }

    public function testCamelize()
    {
        $this->assertEquals('fooBar', Utils::camelize('foo-bar'));
    }

    public function testDashized()
    {
        $this->assertEquals('foo-bar', Utils::dashized('fooBar'));
    }
}