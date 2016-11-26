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

interface OptionsParser
{
    /**
     * Parses the array and returns a tuple containing the arguments and the options
     *
     * @param array $argv
     * @return array
     */
    function parse(array $argv);
}
