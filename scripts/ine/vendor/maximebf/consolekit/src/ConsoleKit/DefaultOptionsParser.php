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
 * Default parser for the $_SERVER['argv'] array
 *
 * Options can be of the form:
 *   --key=value
 *   --key
 *   -a
 *   -ab (equivalent of -a -b)
 *
 * When an option has no value, true will be used. If multiple key/value pairs
 * with the same key are specified, the "key" value will be an array containing all the values.
 * If "--" is detected, all folowing values will be treated as a single argument
 *
 */
class DefaultOptionsParser implements OptionsParser
{
    /**
     * Parses the array and returns a tuple containing the arguments and the options
     *
     * @param array $argv
     * @return array
     */
    public function parse(array $argv)
    {
        $args = array();
        $options = array();

        for ($i = 0, $c = count($argv); $i < $c; $i++) {
            $arg = $argv[$i];
            if ($arg === '--') {
                $args[] = implode(' ', array_slice($argv, $i + 1));
                break;
            }
            if (substr($arg, 0, 2) === '--') {
                $key = substr($arg, 2);
                $value = true;
                if (($sep = strpos($arg, '=')) !== false) {
                    $key = substr($arg, 2, $sep - 2);
                    $value = substr($arg, $sep + 1);
                }
                if (array_key_exists($key, $options)) {
                    if (!is_array($options[$key])) {
                        $options[$key] = array($options[$key]);
                    }
                    $options[$key][] = $value;
                } else {
                    $options[$key] = $value;
                }
            } else if (substr($arg, 0, 1) === '-') {
                foreach (str_split(substr($arg, 1)) as $key) {
                    $options[$key] = true;
                }
            } else {
                $args[] = $arg;
            }
        }

        return array($args, $options);
    }
}
