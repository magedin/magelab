<?php

declare(strict_types=1);

namespace MagedIn\Lab;

use DI\DependencyException;
use DI\NotFoundException;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;

class App
{
    /**
     * @var CommandsBuilder
     */
    private CommandsBuilder $commandsBuilder;

    public function __construct(
        CommandsBuilder $commandsBuilder
    ) {
        $this->commandsBuilder = $commandsBuilder;
    }

    /**
     * @return void
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function run()
    {
        $console = ObjectManager::getInstance()->create(Application::class, [
            'name' => 'MagedIn Lab',
            'version' => Config::get('version'),
        ]);

        /** @var Command $command */
        foreach ($this->commandsBuilder->build() ?? [] as $command) {
            $console->add($command);
        }
        $console->run();
    }
}
