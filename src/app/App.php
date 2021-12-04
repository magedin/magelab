<?php

declare(strict_types=1);

namespace MageLab;

use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Command\Command;

class App
{
    public function run()
    {
        $console = new ConsoleApplication('Magento DockerLab', Config::get('version'));
        $commandsBuilder = new CommandsBuilder();
        /** @var Command $command */
        foreach ($commandsBuilder->build() ?? [] as $command) {
            $console->add($command);
        }
        $console->run();
    }
}
