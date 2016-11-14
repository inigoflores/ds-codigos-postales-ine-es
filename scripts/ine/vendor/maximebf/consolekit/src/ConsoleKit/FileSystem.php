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
 * Utility functions to create files
 */
class FileSystem
{
    public static function touch($filename, $content = '')
    {
        if (!self::mkdir(dirname($filename))) {
            return false;
        }
        return file_put_contents($filename, $content) !== false;
    }

    public static function copyFile($src, $dest)
    {
        if (!self::mkdir(dirname($dest))) {
            return false;
        }
        return copy($src, $dest);
    }

    public static function mkdir($dir)
    {
        if (file_exists($dir)) {
            return is_dir($dir);
        }
        return (bool) @mkdir($dir, 0777, true);
    }

    public static function join($p1, $p2)
    {
        return implode(DIRECTORY_SEPARATOR, func_get_args());
    }
}
