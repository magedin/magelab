<?php

declare(strict_types=1);

namespace MagedIn\Lab\Console;

use MagedIn\Lab\Config;
use MagedIn\Lab\Console\Input\ArgvInput;
use Symfony\Component\Console\Command\Command;

class ConsoleBuilder
{
    private CommandsBuilder $commandsBuilder;

    private Application $application;

    private ArgvInput $argvInput;

    public function __construct(
        CommandsBuilder $commandsBuilder,
        Application $application,
        ArgvInput $argvInput
    ) {
        $this->commandsBuilder = $commandsBuilder;
        $this->application = $application;
        $this->argvInput = $argvInput;
    }

    public function build()
    {
        $this->application->setName(Config::get('name'));
        $this->application->setVersion(Config::get('version'));

        /** @var Command $command */
        foreach ($this->commandsBuilder->build() ?? [] as $command) {
            $this->application->add($command);
        }
        return $this->application;
    }
}
