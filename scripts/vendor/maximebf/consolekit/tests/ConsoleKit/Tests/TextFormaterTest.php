<?php

namespace ConsoleKit\Tests;

use ConsoleKit\TextFormater;

class TextFormaterTest extends ConsoleKitTestCase
{
    public function setUp()
    {
        $this->formater = new TextFormater();
        $this->formater->setOptions(array(
            'indentWidth' => 4,
            'indent' => 1,
            'quote' => '>',
            'fgcolor' => 'black',
            'bgcolor' => 'white'
        ));
    }

    public function testSetOptions()
    {
        $this->assertEquals(4, $this->formater->getIndentWidth());
        $this->assertEquals(1, $this->formater->getIndent());
        $this->assertEquals('>', $this->formater->getQuote());
        $this->assertEquals('black', $this->formater->getFgColor());
        $this->assertEquals('white', $this->formater->getBgColor());
    }

    public function testFormat()
    {
        $expected = "\033[47m\033[30m>    my text\033[0m";
        $this->assertEquals($expected, $this->formater->format('my text'));
    }

    public function testFormatMultiline()
    {
        $expected = "\033[47m\033[30m>    line1\n>    line2\033[0m";
        $this->assertEquals($expected, $this->formater->format("line1\nline2"));
    }
}