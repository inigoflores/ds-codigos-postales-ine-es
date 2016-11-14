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

use ReflectionClass,
    ReflectionFunction;

/**
 * Generates help messages based on information in doc comments
 */
class Help
{
    /** @var string */
    protected $description = '';

    /** @var string */
    protected $usage = '';

    /** @var array */
    protected $args = array();

    /** @var array */
    protected $options = array();

    /** @var array */
    protected $flags = array();

    /** @var array */
    protected $subCommands = array();

    /**
     * Creates an Help object from a FQDN
     *
     * @param string $fqdn
     * @param string $subCommand
     * @return Help
     */
    public static function fromFQDN($fqdn, $subCommand = null)
    {
        if (function_exists($fqdn)) {
            return self::fromFunction($fqdn);
        }
        if (class_exists($fqdn) && is_subclass_of($fqdn, 'ConsoleKit\Command')) {
            return self::fromCommandClass($fqdn, $subCommand);
        }
        throw new ConsoleException("'$fqdn' is not a valid ConsoleKit FQDN");
    }

    /**
     * Creates an Help object from a function
     *
     * @param string $name
     * @return Help
     */
    public static function fromFunction($name)
    {
        $func = new ReflectionFunction($name);
        return new Help($func->getDocComment());
    }

    /**
     * Creates an Help object from a class subclassing Command
     *
     * @param string $name
     * @param string $subCommand
     * @return Help
     */
    public static function fromCommandClass($name, $subCommand = null)
    {
        $prefix = 'execute';
        $class = new ReflectionClass($name);

        if ($subCommand) {
            $method = $prefix . ucfirst(Utils::camelize($subCommand));
            if (!$class->hasMethod($method)) {
                throw new ConsoleException("Sub command '$subCommand' of '$name' does not exist");
            }
            return new Help($class->getMethod($method)->getDocComment());
        }

        $help = new Help($class->getDocComment());
        foreach ($class->getMethods() as $method) {
            if (strlen($method->getName()) > strlen($prefix) && 
                substr($method->getName(), 0, strlen($prefix)) === $prefix) {
                    $help->subCommands[] = Utils::dashized(substr($method->getName(), strlen($prefix)));
            }
        }
        return $help;
    }

    /**
     * @param string $text
     */
    protected function __construct($text = '')
    {
        $this->text = $text;
        $this->parse();
    }

    protected function parse()
    {
        $this->usage = '';
        $this->args = array();
        $this->options = array();
        $this->flags = array();

        $lines = explode("\n", substr(trim($this->text), 2, -2));
        $lines = array_map(function($v) { return ltrim(trim($v), '* '); }, $lines);

        $desc = array();
        foreach ($lines as $line) {
            if (preg_match('/@usage (.+)$/', $line, $matches)) {
                $this->usage = $matches[1];
            } else if (preg_match('/@arg ([^\s]+)( (.*)|)$/', $line, $matches)) {
                $this->args[$matches[1]] = isset($matches[3]) ? $matches[3] : '';
            } else if (preg_match('/@opt ([a-zA-Z\-_0-9=]+)( (.*)|)$/', $line, $matches)) {
                $this->options[$matches[1]] = isset($matches[3]) ? $matches[3] : '';
            } else if (preg_match('/@flag ([a-zA-Z0-9])( (.*)|)$/', $line, $matches)) {
                $this->flags[$matches[1]] = isset($matches[3]) ? $matches[3] : '';
            } else if (!preg_match('/^@([a-zA-Z\-_0-9]+)(.*)$/', $line)) {
                $desc[] = $line;
            }
        }
        
        $this->description = trim(implode("\n", $desc), "\n ");
    }

    /**
     * @return string
     */
    public function getDescrition()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getUsage()
    {
        return $this->usage;
    }

    /**
     * @return array
     */
    public function getArgs()
    {
        return $this->args;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return array
     */
    public function getFlags()
    {
        return $this->flags;
    }

    /**
     * @return bool
     */
    public function hasSubCommands()
    {
        return !empty($this->subCommands);
    }

    /**
     * @return array
     */
    public function getSubCommands()
    {
        return $this->subCommands;
    }

    /**
     * @return string
     */
    public function render()
    {
        $output = "{$this->description}\n\n";
        if (!empty($this->usage)) {
            $output .= "Usage: {$this->usage}\n\n";
        }
        if (!empty($this->args)) {
            $output .= Colors::colorize("Arguments:\n", Colors::BLACK | Colors::BOLD);
            foreach ($this->args as $name => $desc) {
                $output .= sprintf("  %s\t%s\n", $name, $desc);
            }
            $output .= "\n";
        }
        if (!empty($this->options)) {
            $output .= Colors::colorize("Available options:\n", Colors::BLACK | Colors::BOLD);
            foreach ($this->options as $name => $desc) {
                $output .= sprintf("  --%s\t%s\n", $name, $desc);
            }
            $output .= "\n";
        }
        if (!empty($this->flags)) {
            $output .= Colors::colorize("Available flags:\n", Colors::BLACK | Colors::BOLD);
            foreach ($this->flags as $name => $desc) {
                $output .= sprintf("  -%s\t%s\n", $name, $desc);
            }
            $output .= "\n";
        }
        if (!empty($this->subCommands)) {
            $output .= Colors::colorize("Available sub commands:\n", Colors::BLACK | Colors::BOLD);
            foreach ($this->subCommands as $name) {
                $output .= " - $name\n";
            }
            $output .= "\n";
        }
        return trim($output, "\n ");
    }

    public function __toString()
    {
        return $this->render();
    }
}
