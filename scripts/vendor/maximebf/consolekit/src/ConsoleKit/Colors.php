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
 * Functions to colorize text
 *
 * Text can be colorized (foreground only) by using a static method named after the color code.
 *
 * <code>
 * $text = Colors::colorize('hello world', Colors::RED)
 * $text = Colors::colorize('hello world', 'red')
 * $text = Colors::colorize('hello world', Colors::RED | Colors::BOLD)
 * $text = Colors::colorize('hello world', 'red+bold')
 * $text = Colors::red('hello world');
 * </code>
 */
class Colors
{
    const RESET = "\033[0m";

    const BLACK = 1;
    const RED = 2;
    const GREEN = 4;
    const YELLOW = 8;
    const BLUE = 16;
    const MAGENTA = 32;
    const CYAN = 64;
    const WHITE = 128;

    const BOLD = 256;
    const UNDERSCORE = 512;
    const BLINK = 1024;
    const REVERSE = 2048;
    const CONCEAL = 4096;

    /** @var array */
    private static $colors = array(
        'black' => self::BLACK, 
        'red' => self::RED, 
        'green' => self::GREEN, 
        'yellow' => self::YELLOW, 
        'blue' => self::BLUE, 
        'magenta' => self::MAGENTA, 
        'cyan' => self::CYAN, 
        'white' => self::WHITE
    );

    /** @var array */
    private static $options = array(
        'bold' => self::BOLD,
        'underscore' => self::UNDERSCORE,
        'blink' => self::BLINK,
        'reverse' => self::REVERSE,
        'conceal' => self::CONCEAL
    );

    /** @var array */
    private static $codes = array(
        self::BLACK => 0, 
        self::RED => 1, 
        self::GREEN => 2, 
        self::YELLOW => 3, 
        self::BLUE => 4, 
        self::MAGENTA => 5, 
        self::CYAN => 6, 
        self::WHITE => 7,
        self::BOLD => 1,
        self::UNDERSCORE => 4,
        self::BLINK => 5,
        self::REVERSE => 7,
        self::CONCEAL => 8
    );

    /**
     * Returns a colorized string
     *
     * @param string $text
     * @param string $fgcolor (a key from the $foregroundColors array)
     * @param string $bgcolor (a key from the $backgroundColors array)
     * @return string
     */
    public static function colorize($text, $fgcolor = null, $bgcolor = null)
    {
        $colors = '';
        if ($bgcolor) {
            $colors .= self::getBgColorString(self::getColorCode($bgcolor));
        }
        if ($fgcolor) {
            $colors .= self::getFgColorString(self::getColorCode($fgcolor));
        }
        if ($colors) {
            $text = $colors . $text . self::RESET;
        }
        return $text;
    }

    /**
     * Returns a text with each lines colorized independently
     * 
     * @param string $text
     * @param string $fgcolor
     * @param string $bgcolor
     * @return string
     */
    public static function colorizeLines($text, $fgcolor = null, $bgcolor = null)
    {
        $lines = explode("\n", $text);
        foreach ($lines as &$line) {
            $line = self::colorize($line, $fgcolor, $bgcolor);
        }
        return implode("\n", $lines);
    }

    /**
     * Returns a color code
     *
     * $color can be a string with the color name, or one of the color constants.
     *
     * @param int|string $color
     * @param array $options
     * @return int
     */
    public static function getColorCode($color, $options = array())
    {
        $code = (int) $color;
        if (is_string($color)) {
            $options = array_merge(explode('+', strtolower($color)), $options);
            $color = array_shift($options);
            if (!isset(self::$colors[$color])) {
                throw new ConsoleException("Unknown color '$color'");
            }
            $code = self::$colors[$color];
        }
        foreach ($options as $opt) {
            $opt = strtolower($opt);
            if (!isset(self::$options[$opt])) {
                throw new ConsoleException("Unknown option '$color'");
            }
            $code = $code | self::$options[$opt];
        }
        return $code;
    }

    /**
     * Returns a foreground color string
     *
     * @param int $color
     * @return string
     */
    public static function getFgColorString($colorCode)
    {
        list($color, $options) = self::extractColorAndOptions($colorCode);
        $codes = array_filter(array_merge($options, array("3{$color}")));
        return sprintf("\033[%sm", implode(';', $codes));
    }

    /**
     * Returns a background color string
     *
     * @param int $color
     * @return string
     */
    public static function getBgColorString($colorCode)
    {
        list($color, $options) = self::extractColorAndOptions($colorCode);
        $codes = array_filter(array_merge($options, array("4{$color}")));
        return sprintf("\033[%sm", implode(';', $codes));
    }

    /**
     * Extracts the options and the color from a color code
     * 
     * @param int $colorCode
     * @return array
     */
    private static function extractColorAndOptions($colorCode)
    {
        $options = array();
        foreach (self::$options as $name => $bit) {
            if (($colorCode & $bit) === $bit) {
                $options[] = self::$codes[$bit];
                $colorCode = $colorCode & ~$bit;
            }
        }
        if (!isset(self::$codes[$colorCode])) {
            throw new ConsoleException("Cannot parse color code");
        }
        return array(self::$codes[$colorCode], $options);
    }
    
    public static function __callStatic($method, $args)
    {
        return self::colorize($args[0], $method);
    }
}
