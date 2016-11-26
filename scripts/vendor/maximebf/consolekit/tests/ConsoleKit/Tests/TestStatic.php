<?php

namespace ConsoleKit\Tests;

class TestStatic
{
    public function sayHello(array $args, array $opts = array(), $console)
    {
        $name = 'unknown';
        if (!empty($args)) {
            $name = implode(' ', $args);
        } else if (isset($opts['name'])) {
            $name = $opts['name'];
        }
        $console->writeln(sprintf("hello %s!", $name));
    }
}
