<?php
/*
 * This file is part of the ConsoleKit package.
 *
 * (c) 2012 Maxime Bouroumeau-Fuseau
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ConsoleKit\Widgets;

class Dialog extends AbstractWidget
{
    /**
     * Writes $text and reads the user's input
     *
     * @param string $text
     * @param string $default
     * @param bool $displayDefault
     * @return string
     */
    public function ask($text, $default = '', $displayDefault = true)
    {
        if ($displayDefault && !empty($default)) {
            $defaultText = $default;
            if (strlen($defaultText) > 30) {
                $defaultText = substr($default, 0, 30) . '...';
            }
            $text .= " [$defaultText]";
        }
        $this->textWriter->write("$text ");
        return trim(fgets(STDIN)) ?: $default;
    }

    /**
     * Writes $text (followed by the list of choices) and reads the user response. 
     * Returns true if it matches $expected, false otherwise
     *
     * <code>
     * if($dialog->confirm('Are you sure?')) { ... }
     * if($dialog->confirm('Your choice?', null, array('a', 'b', 'c'))) { ... }
     * </code>
     *
     * @param string $text
     * @param string $expected
     * @param array $choices
     * @param string $default
     * @param string $errorMessage
     * @return bool
     */
    public function confirm($text, $expected = 'y', array $choices = array('Y', 'n'), $default = 'y', $errorMessage = 'Invalid choice')
    {
        $text = $text . ' [' . implode('/', $choices) . ']';
        $choices = array_map('strtolower', $choices);
        $expected = strtolower($expected);
        $default = strtolower($default);
        do {
            $input = strtolower($this->ask($text));
            if (in_array($input, $choices)) {
                return $input === $expected;
            } else if (empty($input) && !empty($default)) {
                return $default === $expected;
            }
            $this->textWriter->writeln($errorMessage);
        } while (true);
    }
}
