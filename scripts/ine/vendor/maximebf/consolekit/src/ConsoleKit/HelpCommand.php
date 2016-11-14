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

class HelpCommand extends Command
{
    public function execute(array $args, array $options = array())
    {
        if (empty($args)) {
            $formater = new TextFormater(array('quote' => ' * '));
            $this->writeln('Available commands:', Colors::BLACK | Colors::BOLD);
            foreach ($this->console->getCommands() as $name => $fqdn) {
                if ($fqdn !== __CLASS__) {
                    $this->writeln($formater->format($name));
                }
            }
            $scriptName = basename($_SERVER['SCRIPT_FILENAME']);
            $this->writeln("Use './$scriptName help command' for more info");
        } else {
            $commandFQDN = $this->console->getCommand($args[0]);
            $help = Help::fromFQDN($commandFQDN, Utils::get($args, 1));
            $this->writeln($help);
        }
    }
}
