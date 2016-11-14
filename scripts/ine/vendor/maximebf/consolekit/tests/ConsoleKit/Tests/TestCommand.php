<?php

namespace ConsoleKit\Tests;

class TestCommand extends \ConsoleKit\Command
{
    public function execute(array $args, array $opts = array())
    {
        $name = 'unknown';
        if (!empty($args)) {
            $name = implode(' ', $args);
        } else if (isset($opts['name'])) {
            $name = $opts['name'];
        }
        $this->writeln(sprintf("hello %s!", $name));
    }
}
