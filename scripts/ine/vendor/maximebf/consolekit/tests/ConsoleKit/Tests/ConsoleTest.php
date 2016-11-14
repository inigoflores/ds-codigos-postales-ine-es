<?php

namespace ConsoleKit\Tests;

use ConsoleKit\Console,
    ConsoleKit\DefaultOptionsParser,
    ConsoleKit\EchoTextWriter,
    ConsoleKit\Colors;

class ConsoleTest extends ConsoleKitTestCase
{
    public function setUp()
    {
        $this->console = new Console();
        $this->console->setTextWriter(new EchoTextWriter());
        $this->console->setExitOnException(false);
    }

    public function testAddCommand()
    {
        $this->console->addCommand('ConsoleKit\Tests\TestCommand');
        $this->assertArrayHasKey('test', $this->console->getCommands());
        $this->assertEquals('ConsoleKit\Tests\TestCommand', $this->console->getCommand('test'));

        $this->console->addCommand('ConsoleKit\Tests\TestCommand', 'test-alias');
        $this->assertArrayHasKey('test-alias', $this->console->getCommands());
        $this->assertEquals('ConsoleKit\Tests\TestCommand', $this->console->getCommand('test-alias'));

        $this->console->addCommand('ConsoleKit\Tests\TestStatic::sayHello');
        $this->assertArrayHasKey('say-hello', $this->console->getCommands());
        $this->assertEquals('ConsoleKit\Tests\TestStatic::sayHello', $this->console->getCommand('say-hello'));

        $this->console->addCommand('var_dump');
        $this->assertArrayHasKey('var-dump', $this->console->getCommands());
        $this->assertEquals('var_dump', $this->console->getCommand('var-dump'));

        $this->console->addCommand(array(new TestCommand($this->console), 'execute'), 'test-callback');
        $this->assertArrayHasKey('test-callback', $this->console->getCommands());
        $this->assertInternalType('array', $this->console->getCommand('test-callback'));
        
        $this->console->addCommand(function($args, $opts) { echo 'hello!'; }, 'hello');
        $this->assertArrayHasKey('hello', $this->console->getCommands());
        $this->assertInstanceOf('Closure', $this->console->getCommand('hello'));
    }

    public function testAddCommands()
    {
        $this->console->addCommands(array(
            'ConsoleKit\Tests\TestCommand',
            'test-alias' => 'ConsoleKit\Tests\TestCommand'
        ));

        $this->assertArrayHasKey('test', $this->console->getCommands());
        $this->assertEquals('ConsoleKit\Tests\TestCommand', $this->console->getCommand('test'));
        $this->assertArrayHasKey('test-alias', $this->console->getCommands());
        $this->assertEquals('ConsoleKit\Tests\TestCommand', $this->console->getCommand('test-alias'));
    }

    public function testAddCommandsFromDir()
    {
        $this->console->addCommandsFromDir(__DIR__, 'ConsoleKit\Tests');
        $this->assertArrayHasKey('test', $this->console->getCommands());
        $this->assertEquals('ConsoleKit\Tests\TestCommand', $this->console->getCommand('test'));
    }

    public function testExecute()
    {
        $this->expectOutputString("hello unknown!\n");
        $this->console->addCommand('ConsoleKit\Tests\TestCommand');
        $this->console->execute('test');
    }

    public function testExecuteWithArgs()
    {
        $this->expectOutputString("hello foo bar!\n");
        $this->console->addCommand('ConsoleKit\Tests\TestCommand');
        $this->console->execute('test', array('foo', 'bar'));
    }

    public function testExecuteWithOption()
    {
        $this->expectOutputString("hello foobar!\n");
        $this->console->addCommand('ConsoleKit\Tests\TestCommand');
        $this->console->execute('test', array(), array('name' => 'foobar'));
    }

    public function testExecuteSubCommand()
    {
        $this->console->addCommand('ConsoleKit\Tests\TestSubCommand', 'test');
        $this->assertEquals('hello foobar!', $this->console->execute('test', array('say-hello', 'foobar')));
        $this->assertEquals('hi foobar!', $this->console->execute('test', array('say-hi', 'foobar')));
    }

    public function testExecuteFunction()
    {
        $this->expectOutputString("\033[31mhello foobar!\033[0m\n");
        $this->console->addCommand(function($args, $opts, $console) {
            $console->writeln(Colors::colorize(sprintf("hello %s!", $args[0]), $opts['color']));
        }, 'test');
        $this->console->addCommand('test2', function($args, $opts, $console) {
            return "success";
        });
        $this->console->execute('test', array('foobar'), array('color' => 'red'));
        $this->assertEquals("success", $this->console->execute('test2'));
    }

    public function testRun()
    {
        $this->expectOutputString("hello unknown!\n");
        $this->console->addCommand('ConsoleKit\Tests\TestCommand');
        $this->console->run(array('test'));
    }

    public function testDefaultCommand()
    {
        $this->expectOutputString("hello unknown!\n");
        $this->console->addCommand('ConsoleKit\Tests\TestCommand', null, true);
        $this->console->run(array());
    }

    public function testOneCommandWithArguments() {
        $this->expectOutputString("hello foobar!\n");
        $this->console->addCommand('ConsoleKit\Tests\TestCommand', null, true);
        $this->console->setSingleCommand(true);
        $this->console->run(array('foobar'));
    }
}
