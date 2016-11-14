<?php
/*
 * This file is part of the ConsoleKit package.
 *
 * (c) 2012 Maxime Bouroumeau-Fuseau
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ConsoleKit;

/**
 * Simple writer that prints text using php's "echo()" function
 *
 * Note: Pipes are ignored
 */
class EchoTextWriter implements TextWriter
{
    public function write($text, $pipe = TextWriter::STDOUT)
    {
        echo $text;
    }

    public function writeln($text = '', $pipe = TextWriter::STDOUT)
    {
        $this->write("$text\n", $pipe);
    }
}
