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

use Closure,
    ReflectionMethod;

/**
 * Base class for commands
 *
 * Can be used to represent a single command:
 *
 * <code>
 *     class MyCommand extends Command {
 *         public function execute(array $args, array $options = array()) {
 *             $this->writeln('hello world');
 *         }
 *     }
 * </code>
 *
 * If "execute()" is not overriden, the execution will be forwarded to subcommand
 * methods. The subcommand name will be the first argument value and the associated
 * method must be prefixed with "execute".
 *
 * <code>
 *     class MyCommand extends Command {
 *         public function executeSub1(array $args, array $options = array()) {
 *             $this->writeln('hello world');
 *         }
 *         public function executeSub2(array $args, array $options = array()) {
 *             $this->writeln('hello world');
 *         }
 *     }
 * </code>
 *
 */
abstract class Command
{
    /** @var Console */
    protected $console;

    /** @var array */
    protected $defaultFormatOptions = array();

    /**
     * @param Console $console
     */
    public function __construct(Console $console)
    {
        $this->console = $console;
    }

    /** 
     * @return Console
     */
    public function getConsole()
    {
        return $this->console;
    }

    /**
     * If not overriden, will execute the command specified
     * as the first argument
     * 
     * Commands must be defined as methods named after the
     * command, prefixed with execute (eg. create -> executeCreate)
     * 
     * @param array $args
     * @param array $options
     */
    public function execute(array $args, array $options = array())
    {
        if (!count($args)) {
            throw new ConsoleException("Missing subcommand name");
        }
        
        $command = ucfirst(Utils::camelize(array_shift($args)));
        $methodName = "execute$command";
        if (!method_exists($this, $methodName)) {
            throw new ConsoleException("Command '$command' does not exist");
        }

        $method = new ReflectionMethod($this, $methodName);
        $params = Utils::computeFuncParams($method, $args, $options);
        return $method->invokeArgs($this, $params);
    }

    /**
     * Formats text using a {@see TextFormater}
     *
     * @param string $text
     * @param int|array $formatOptions Either an array of options for TextFormater or a color code
     * @return string
     */
    public function format($text, $formatOptions = array())
    {
        if (!is_array($formatOptions)) {
            $formatOptions = array('fgcolor' => $formatOptions);
        }
        $formatOptions = array_merge($this->defaultFormatOptions, $formatOptions);
        $formater = new TextFormater($formatOptions);
        return $formater->format($text);
    }

    /**
     * Executes the closure with a {@see FormatedWriter} object as the first
     * argument, initialized with the $formatOptions array
     *
     * <code>
     * $this->context(array('quote' => ' * '), function($f) {
     *     $f->writeln('quoted text');
     * })
     * </code>
     *
     * @param array $formatOptions
     * @param Closure $closure
     */
    public function context(array $formatOptions, Closure $closure)
    {
        $formater = new FormatedWriter($this->console, $formatOptions);
        return $closure($formater);
    }
    
    /**
     * Writes some text to the text writer
     * 
     * @see format()
     * @param string $text
     * @param int|array $formatOptions
     * @param int $pipe
     * @return Command
     */
    public function write($text, $formatOptions = array(), $pipe = TextWriter::STDOUT)
    {
        $this->console->write($this->format($text, $formatOptions), $pipe);
        return $this;
    }

    /**
     * Writes a message in bold red to STDERR
     *
     * @param string $text
     * @return Command
     */
    public function writeerr($text)
    {
        return $this->write($text, Colors::RED | Colors::BOLD, TextWriter::STDERR);
    }
    
    /**
     * Writes a line of text
     * 
     * @param string $text
     * @param int|array $formatOptions
     * @param int $pipe
     * @return Command
     */
    public function writeln($text, $formatOptions = array(), $pipe = TextWriter::STDOUT)
    {
        $this->console->writeln($this->format($text, $formatOptions), $pipe);
        return $this;
    }
}
