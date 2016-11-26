# ConsoleKit

PHP 5.3+ library to create command line utilities.

[![Build Status](https://secure.travis-ci.org/maximebf/ConsoleKit.png)](http://travis-ci.org/maximebf/ConsoleKit)

## Example

In *cli.php*:

    <?php

    class HelloCommand extends ConsoleKit\Command
    {
        public function execute(array $args, array $options = array())
        {
            $this->writeln('hello world!', ConsoleKit\Colors::GREEN);
        }
    }

    $console = new ConsoleKit\Console();
    $console->addCommand('HelloCommand');
    $console->run();

In the shell:
    
    $ php cli.php hello
    hello world!

More examples in [example.php](https://github.com/maximebf/ConsoleKit/blob/master/example.php)

## Installation

The easiest way to install ConsoleKit is using [Composer](https://github.com/composer/composer)
with the following requirement:

    {
        "require": {
            "maximebf/consolekit": ">=1.0.0"
        }
    }

Alternatively, you can [download the archive](https://github.com/maximebf/ConsoleKit/zipball/master) 
and add the src/ folder to PHP's include path:

    set_include_path('/path/to/src' . PATH_SEPARATOR . get_include_path());

ConsoleKit does not provide an autoloader but follows the [PSR-0 convention](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md).  
You can use the following snippet to autoload ConsoleKit classes:

    spl_autoload_register(function($className) {
        if (substr($className, 0, 10) === 'ConsoleKit') {
            $filename = str_replace('\\', DIRECTORY_SEPARATOR, trim($className, '\\')) . '.php';
            require_once $filename;
        }
    });

## Usage

### Options parser

The default options parser parses an argv-like array.
Items can be of the form:

 -  --key=value
 -  --key
 -  -a
 -  -ab (equivalent of -a -b)

When an option has no value, true will be used. If multiple key/value pairs
with the same key are specified, the "key" value will be an array containing all the values.  
If "--" is detected, all folowing values will be treated as a single argument

Example: the string "-a -bc --longopt --key=value arg1 arg2 -- --any text" will produce the following two arrays:

    $args = array('arg1', 'arg2', '--any text');
    $options = array('a' => true, 'b' => true, 'c' => true, 'longopt' => true, 'key' => 'value');

### Creating commands

Any callbacks can be a command. It will receive three parameters: the 
arguments array, the options array and the console object.

    function my_command($args, $opts, $console) {
        $console->writeln("hello world!");
    }

Commands can also be defined as classes. In this case, they must inherit from `ConsoleKit\Command`
and override the `execute()` method.

    class MyCommand extends ConsoleKit\Command {
        public function execute(array $args, array $opts) {
            $this->writeln("hello world!");
        }
    }

The `ConsoleKit\Command` class offers helper methods, check it out for more info.

### Registering commands

Commands need to be registered in the console object using the `addCommand()` method (or `addCommands()`).

    $console = new ConsoleKit\Console();
    $console->addCommand('my_command'); // the my_command function
    $console->addCommand('MyCommand'); // the MyCommand class
    $console->addCommand(function() { echo 'hello!'; }, 'hello'); // using a closure
    // or:
    $console->addCommand('hello', function() { echo 'hello!'; }); // alternative when using a closure

Notice that in the last example we have provided a second argument which is an alias for a command.
As closures have no name, one must be specified.

The command name for functions is the same as the function name with underscores replaced 
by dashes (ie. my\_command becomes my-command).

The command name for command classes is the short class name without the `Command` 
suffix and "dashized" (ie. HelloWorldCommand becomes hello-world).

### Running

Simply call the `run()` method of the console object

    $console->run();
    $console->run(array('custom arg1', 'custom arg2')); // overrides $_SERVER['argv']

### Automatic help generation

The *help* command is automatically registered and provides help about available methods based on doc comments.  
Check out [example.php](https://github.com/maximebf/ConsoleKit/blob/master/example.php) for example of available tags

    $ php myscript.php help

## Formating text

### Colors

The `ConsoleKit\Colors::colorize()` method provides an easy way to colorize a text. 
Colors are defined as either a string or an integer (through constants of the `Colors` class).  
Available colors: black, red, green, yellow, blue, magenta, cyan, white.

Foreground colors are also available in a "bold" variant. Suffix the color name with "+bold" or use the OR bit operator with constants.

    echo Colors::colorize('my red text', Colors::RED);
    echo Colors::colorize('my red text', 'red');
    
    echo Colors::colorize('my red bold text', Colors::RED | Colors::BOLD);
    echo Colors::colorize('my red bold text', 'red+bold');
    
    echo Colors::colorize('my red text over yellow background', Colors::RED, Colors::YELLOW);
   
### TextFormater

The `ConsoleKit\TextFormater` class allows you to format text using the following options:

 -  indentation using `setIndent()` or the *indent* option
 -  quoting using `setQuote()` or the *quote* option
 -  foreground color using `setFgColor()` or the *fgcolor* option
 -  background color using `setBgColor()` or the *bgcolor* option

Options can be defined using `setOptions()` or as the first parameter of the constructor.

    $formater = new ConsoleKit\TextFormater(array('quote' => ' > '));
    echo $formater->format("hello!");
    // produces: " > hello"
    
## Widgets

### Dialog

Used to interact with the user

    $dialog = new ConsoleKit\Widgets\Dialog($console);
    $name = $dialog->ask('What is your name?');
    if ($dialog->confirm('Are you sure?')) {
        $console->writeln("hello $name");
    }
    
### Box

Wraps text in a box

    $box = new ConsoleKit\Widgets\Box($console, 'my text');
    $box->write();
    
Produces:

    ********************************************
    *                 my text                  *
    ********************************************

### Progress bar

Displays a progress bar

    $total = 100;
    $progress = new ConsoleKit\Widgets\ProgressBar($console, $total);
    for ($i = 0; $i < $total; $i++) {
        $progress->incr();
        usleep(10000);
    }
    $progress->stop();
    

