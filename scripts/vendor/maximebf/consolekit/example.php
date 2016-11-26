<?php

include __DIR__ . '/tests/bootstrap.php';

use ConsoleKit\Console,
    ConsoleKit\Command,
    ConsoleKit\Colors,
    ConsoleKit\Utils,
    ConsoleKit\Widgets\Dialog,
    ConsoleKit\Widgets\ProgressBar;

/**
 * Prints hello world
 */
class HelloWorldCommand extends Command
{
    public function execute(array $args, array $options = array())
    {
        $this->writeln('hello world!', Colors::GREEN);
    }
}

/**
 * Says hello to someone
 *
 * @arg name The name of the person to say hello to
 * @opt color The color in which to print the text
 */
class SayHelloCommand extends Command
{
    public function execute(array $args, array $options = array())
    {
        $this->context(array('fgcolor' => Utils::get($options, 'color')), function($c) use ($args) {
            $c->writeln(sprintf('hello %s!', $args[0]));
        });
    }
}

/**
 * Commands to say something to someone!
 */
class SayCommand extends Command
{
    /**
     * Says hello to someone
     *
     * @arg name The name of the person to say hello to
     */
    public function executeHello(array $args, array $options = array())
    {
        $name = 'unknown';
        if (empty($args)) {
            $dialog = new Dialog($this->console);
            $name = $dialog->ask('What is your name?', $name);
        } else {
            $name = $args[0];
        }
        $this->writeln(sprintf('hello %s!', $name));
    }

    /**
     * Says hi to someone
     *
     * @arg name The name of the person to say hello to
     */
    public function executeHi(array $args, array $options = array())
    {
        $this->writeln(sprintf('hi %s!', $args[0]));
    }
}

/**
 * Displays a progress bar
 *
 * @opt total Number of iterations
 * @opt usleep Waiting time in microsecond between each iteration
 */
function progress($args, $options, $console)
{
    $total = isset($options['total']) ? $options['total'] : 100;
    $usleep = isset($options['usleep']) ? $options['usleep'] : 10000;
    $progress = new ProgressBar($console, $total);
    for ($i = 0; $i < $total; $i++) {
        $progress->incr();
        usleep($usleep);
    }
    $progress->stop();
}

$console = new Console(array(
    'hello' => 'HelloWorldCommand',
    'SayHelloCommand',
    'SayCommand',
    'progress'
));

$console->run();
