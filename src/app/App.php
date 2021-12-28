<?php

declare(strict_types=1);

namespace MageLab;

use DI\DependencyException;
use DI\NotFoundException;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Command\Command;

class App
{
    /**
     * @return void
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function run()
    {
        $objectManager = ObjectManager::getInstance();

        $commandsBuilder = $objectManager->get(CommandsBuilder::class);
        $console = $objectManager->create(ConsoleApplication::class, [
            'name' => 'MageLab',
            'version' => Config::get('version'),
        ]);

        /** @var Command $command */
        foreach ($commandsBuilder->build() ?? [] as $command) {
            $console->add($command);
        }
        $console->run();
    }
}
