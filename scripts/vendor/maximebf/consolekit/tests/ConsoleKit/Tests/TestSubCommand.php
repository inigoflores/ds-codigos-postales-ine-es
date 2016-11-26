<?php

namespace ConsoleKit\Tests;

class TestSubCommand extends \ConsoleKit\Command
{
    public function executeSayHello(array $args, array $opts)
    {
        return sprintf("hello %s!", $args[0]);
    }

    /**
     * @compute-params
     */
    public function executeSayHi($name)
    {
        return sprintf("hi %s!", $name);
    }
}
