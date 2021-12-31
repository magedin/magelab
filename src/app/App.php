<?php

declare(strict_types=1);

namespace MagedIn\Lab;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArgvInput;
use MagedIn\Lab\Console\Input\InputDefinition;

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
     */
    public function run()
    {
        /** @var Application $console */
        $console = ObjectManager::getInstance()->create(Application::class, [
            'name' => Config::get('name'),
            'version' => Config::get('version'),
        ]);

        /** @var Command $command */
        foreach ($this->commandsBuilder->build() ?? [] as $command) {
            $console->add($command);
        }
        $console->run();
    }
}
