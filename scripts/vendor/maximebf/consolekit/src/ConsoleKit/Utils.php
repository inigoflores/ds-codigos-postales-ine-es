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

use ReflectionFunctionAbstract;

class Utils
{
    /**
     * Returns the value from $key in $array or $default
     *
     * @param array $array
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($array, $key, $default = null)
    {
        if (array_key_exists($key, $array)) {
            return $array[$key];
        }
        return $default;
    }

    /**
     * Finds the first file that match the filename in any of 
     * the specified directories.
     * 
     * @param string $filename
     * @param array $dirs
     * @return string
     */
    public static function find($filename, $dirs = array())
    {
        if (empty($dirs)) {
            if ($filename = realpath($filename)) {
                return $filename;
            }
        } else {
            foreach ((array) $dirs as $dir) {
                $pathname = self::join($dir, $filename);
                if ($pathname = realpath($pathname)) {
                    return $pathname;
                }
            }
        }
        return false;
    }

    /**
     * Extracts files from an array of args
     *
     * @param array $args
     * @param bool $allowWildcards Whether wildcards are allowed
     * @return array
     */
    public static function filterFiles($args, $allowWildcards = true)
    {
        $files = array();
        foreach ($args as $arg) {
            if (file_exists($arg)) {
                $files[] = $arg;
            } else if ($allowWildcards && strpos($arg, '*') !== false) {
                $files = array_merge($files, glob($arg));
            }
        }
        return $files;
    }

    /**
     * Joins paths together
     *
     * @param string $path1
     * @param string $path2
     * @param string ...
     * @return string
     */
    public static function join($path1, $path2) {
        $ds = DIRECTORY_SEPARATOR;
        return str_replace("$ds$ds", $ds, implode($ds, array_filter(func_get_args())));
    }

    /**
     * Creates a directory recursively
     *
     * @param string $dir
     * @param octal $mode
     */
    public static function mkdir($dir, $mode = 0777)
    {
        if (!file_exists($dir)) {
            mkdir($dir, $mode, true);
        }
    }

    /**
     * Creates a file and its directory
     *
     * @param string $filename
     * @param string $content
     */
    public static function touch($filename, $content = '')
    {
        self::mkdir(dirname($filename));
        file_put_contents($filename, $content);
    }

    /**
     * Returns piped in data
     *
     * @return string
     */
    public static function pipedIn()
    {
        return file_get_contents('php://stdin');
    }

    /**
     * Returns a dash-cased string into a camelCased string
     *
     * @param string $string
     * @return string
     */
    public static function camelize($string)
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('-', ' ', $string))));
    }

    /**
     * Returns a camelCased string into a dash-cased string
     *
     * @param string $string
     * @return string
     */
    public static function dashized($string)
    {
        return strtolower(preg_replace('/(?<=[a-z])([A-Z])/', '-$1', $string));
    }

    /**
     * Creates an array of parameters according to the function definition
     * 
     * @param ReflectionFunctionAbstract $reflection
     * @param array $args
     * @param array $options
     * @param bool $needTagInDocComment Whether the compute-params tag must be present in the doc comment
     * @return array
     */
    public static function computeFuncParams(ReflectionFunctionAbstract $reflection, array $args, array $options, $needTagInDocComment = true)
    {
        if ($needTagInDocComment && !preg_match('/@compute-params/', $reflection->getDocComment())) {
            return array($args, $options);
        }

        $nbRequiredParams = $reflection->getNumberOfRequiredParameters();
        if (count($args) < $nbRequiredParams) {
            throw new ConsoleException("Not enough parameters in '" . $reflection->getName() . "'");
        }

        $params = $args;
        if (count($args) > $nbRequiredParams) {
            $params = array_slice($args, 0, $nbRequiredParams);
            $args = array_slice($args, $nbRequiredParams);
        }

        foreach ($reflection->getParameters() as $param) {
            if ($param->isOptional() && substr($param->getName(), 0, 1) !== '_') {
                if (array_key_exists($param->getName(), $options)) {
                    $params[] = $options[$param->getName()];
                    unset($options[$param->getName()]);
                } else {
                    $params[] = $param->getDefaultValue();
                }
            }
        }

        $params[] = $args;
        $params[] = $options;
        return $params;
    }
}
