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
    DirectoryIterator,
    ReflectionFunction,
    ReflectionMethod;

/**
 * Registry of available commands and command runner
 */
class Console implements TextWriter
{
    /** @var OptionsParser */
    protected $optionsParser;

    /** @var TextWriter */
    protected $textWriter;

    /** @var bool */
    protected $exitOnException = true;

    /** @var string */
    protected $helpCommand = 'help';

    /** @var string */
    protected $helpCommandClass = 'ConsoleKit\HelpCommand';

    /** @var array */
    protected $commands = array();

    /** @var string */
    protected $defaultCommand;

    /** @var bool */
    protected $verboseException = false;

    /** @var bool */
    protected $singleCommand = false;

    /**
     * @param array $commands
     */
    public function __construct(array $commands = array(), OptionsParser $parser = null, TextWriter $writer = null)
    {
        $this->optionsParser = $parser ?: new DefaultOptionsParser();
        $this->textWriter = $writer ?: new StdTextWriter();
        if ($this->helpCommandClass) {
            $this->addCommand($this->helpCommandClass, $this->helpCommand);
            $this->addCommands($commands);
        }
    }

    /**
     * @param OptionsParser $parser
     * @return Console
     */
    public function setOptionsParser(OptionsParser $parser)
    {
        $this->optionsParser = $parser;
        return $this;
    }

    /**
     * @return OptionsParser
     */
    public function getOptionsParser()
    {
        return $this->optionsParser;
    }

    /**
     * @param TextWriter $writer
     * @return Console
     */
    public function setTextWriter(TextWriter $writer)
    {
        $this->textWriter = $writer;
        return $this;
    }

    /**
     * @return TextWriter
     */
    public function getTextWriter()
    {
        return $this->textWriter;
    }

    /**
     * Sets whether to call exit(1) when an exception is caught
     *
     * @param bool $exit
     * @return Console
     */
    public function setExitOnException($exit = true)
    {
        $this->exitOnException = $exit;
        return $this;
    }

    /**
     * @return bool
     */
    public function exitsOnException()
    {
        return $this->exitOnException;
    }

    /**
     * Sets whether a detailed error message is displayed when exception are caught
     * 
     * @param boolean $enable
     */
    public function setVerboseException($enable = true)
    {
        $this->verboseException = $enable;
    }

    /**
     * @return bool
     */
    public function areExceptionsVerbose()
    {
        return $this->verboseException;
    }

    /**
     * Adds multiple commands at once
     *
     * @see addCommand()
     * @param array $commands
     * @return Console
     */
    public function addCommands(array $commands)
    {
        foreach ($commands as $name => $command) {
            $this->addCommand($command, is_numeric($name) ? null : $name);
        }
        return $this;
    }
    
    /**
     * Registers a command
     * 
     * @param callback $callback Associated class name, function name, Command instance or closure
     * @param string $alias Command name to be used in the shell
     * @param bool $default True to set the command as the default one
     * @return Console
     */
    public function addCommand($callback, $alias = null, $default = false)
    {
        if ($alias instanceof \Closure && is_string($callback)) {
            list($alias, $callback) = array($callback, $alias);
        }
        if (is_array($callback) && is_string($callback[0])) {
            $callback = implode('::', $callback);
        }
        
        $name = '';
        if (is_string($callback)) {
            $name = $callback;
            if (is_callable($callback)) {
                if (strpos($callback, '::') !== false) {
                    list($classname, $methodname) = explode('::', $callback);
                    $name = Utils::dashized($methodname);
                } else {
                    $name = strtolower(trim(str_replace('_', '-', $name), '-'));
                }
            } else {
                if (substr($name, -7) === 'Command') {
                    $name = substr($name, 0, -7);
                }
                $name = Utils::dashized(basename(str_replace('\\', '/', $name)));
            }
        } else if (is_object($callback) && !($callback instanceof Closure)) {
            $classname = get_class($callback);
            if (!($callback instanceof Command)) {
                throw new ConsoleException("'$classname' must inherit from 'ConsoleKit\Command'");
            }
            if (substr($classname, -7) === 'Command') {
                $classname = substr($classname, 0, -7);
            }
            $name = Utils::dashized(basename(str_replace('\\', '/', $classname)));
        } else if (!$alias) {
            throw new ConsoleException("Commands using closures must have an alias");
        }

        $name = $alias ?: $name;
        $this->commands[$name] = $callback;
        if ($default) {
            $this->defaultCommand = $name;
        }
        return $this;
    }

    /**
     * Registers commands from a directory
     * 
     * @param string $dir
     * @param string $namespace
     * @param bool $includeFiles
     * @return Console
     */
    public function addCommandsFromDir($dir, $namespace = '', $includeFiles = false)
    {
        foreach (new DirectoryIterator($dir) as $file) {
            $filename = $file->getFilename();
            if ($file->isDir() || substr($filename, 0, 1) === '.' || strlen($filename) <= 11 
                || strtolower(substr($filename, -11)) !== 'command.php') {
                    continue;
            }
            if ($includeFiles) {
                include $file->getPathname();
            }
            $className = trim($namespace . '\\' . substr($filename, 0, -4), '\\');
            $this->addCommand($className);
        }
        return $this;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasCommand($name)
    {
        return isset($this->commands[$name]);
    }

    /**
     * @param string $name
     * @return string
     */
    public function getCommand($name)
    {
        if (!isset($this->commands[$name])) {
            throw new ConsoleException("Command '$name' does not exist");
        }
        return $this->commands[$name];
    }

    /**
     * @return array
     */
    public function getCommands()
    {
        return $this->commands;
    }

    /**
     * @param string $name
     * @return Console
     */
    public function setDefaultCommand($name = null)
    {
        if ($name !== null && !isset($this->commands[$name])) {
            throw new ConsoleException("Command '$name' does not exist");
        }
        $this->defaultCommand = $name;
    }

    /**
     * @return string
     */
    public function getDefaultCommand()
    {
        return $this->defaultCommand;
    }

    /**
     * Turn off command parsing
     *
     * @param type $singleCommand
     * @return Console
     */
    public function setSingleCommand($singleCommand) {
        $this->singleCommand = $singleCommand;
        return $this;
    }
    
    /**
     * @param array $args
     * @return mixed Results of the command callback
     */
    public function run(array $argv = null)
    {
        try {
            if ($argv === null) {
                $argv = isset($_SERVER['argv']) ? array_slice($_SERVER['argv'], 1) : array();
            }

            list($args, $options) = $this->getOptionsParser()->parse($argv);
            if($this->defaultCommand && $this->singleCommand) {
                return $this->execute($this->defaultCommand, $args, $options);
            }
            
            if (!count($args)) {
                if ($this->defaultCommand) {
                    $args[] = $this->defaultCommand;
                } else {
                    $this->textWriter->writeln(Colors::red("Missing command name"));
                    $args[] = $this->helpCommand;
                }
            }

            $command = array_shift($args);
            return $this->execute($command, $args, $options);

        } catch (\Exception $e) {
            $this->writeException($e);
            if ($this->exitOnException) {
                exit(1);
            }
            throw $e;
        }
    }

    /**
     * Executes a command
     *
     * @param string $command
     * @param array $args
     * @param array $options
     * @return mixed
     */
    public function execute($command = null, array $args = array(), array $options = array())
    {
        $command = $command ?: $this->defaultCommand;
        if (!isset($this->commands[$command])) {
            throw new ConsoleException("Command '$command' does not exist");
        }
        
        $callback = $this->commands[$command];
        if (is_callable($callback)) {
            $params = array($args, $options);
            if (is_string($callback)) {
                if (strpos($callback, '::') !== false) {
                    list($classname, $methodname) = explode('::', $callback);
                    $reflection = new ReflectionMethod($classname, $methodname);
                } else {
                    $reflection = new ReflectionFunction($callback);
                }
                $params = Utils::computeFuncParams($reflection, $args, $options);
            }
            $params[] = $this;
            return call_user_func_array($callback, $params);
        }

        $method = new ReflectionMethod($callback, 'execute');
        $params = Utils::computeFuncParams($method, $args, $options);
        return $method->invokeArgs(new $callback($this), $params);
    }
    
    /**
     * Writes some text to the text writer
     * 
     * @see TextWriter::write()
     * @param string $text
     * @param array $formatOptions
     * @return Console
     */
    public function write($text, $pipe = TextWriter::STDOUT)
    {
        $this->textWriter->write($text, $pipe);
        return $this;
    }
    
    /**
     * Writes a line of text
     * 
     * @see TextWriter::writeln()
     * @param string $text
     * @param array $formatOptions
     * @return Console
     */
    public function writeln($text = '', $pipe = TextWriter::STDOUT)
    {
        $this->textWriter->writeln($text, $pipe);
        return $this;
    }

    /**
     * Writes an error message to stderr
     *
     * @param \Exception $e
     * @return Console
     */
    public function writeException(\Exception $e)
    {
        if ($this->verboseException) {
            $text = sprintf("[%s]\n%s\nIn %s at line %s\n%s", 
                get_class($e),
                $e->getMessage(),
                $e->getFile(),
                $e->getLine(),
                $e->getTraceAsString()
            );
        } else {
            $text = sprintf("\n[%s]\n%s\n", get_class($e), $e->getMessage());
        }

        $box = new Widgets\Box($this->textWriter, $text, '');
        $out = Colors::colorizeLines($box, Colors::WHITE, Colors::RED);
        $out = TextFormater::apply($out, array('indent' => 2));
        $this->textWriter->writeln($out);
        return $this;
    }
}
