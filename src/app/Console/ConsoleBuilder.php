<?php
/**
 * MagedIn Technology
 *
 * @category  MagedIn MageLab
 * @copyright Copyright (c) 2021 MagedIn Technology.
 *
 * @author    Tiago Sampaio <tiago.sampaio@magedin.com>
 */

declare(strict_types=1);

namespace MagedIn\Lab\Console;

use MagedIn\Lab\Config;
use Symfony\Component\Console\Command\Command;

class ConsoleBuilder
{
    /**
     * @var CommandsBuilder
     */
    private CommandsBuilder $commandsBuilder;

    /**
     * @var Application
     */
    private Application $application;

    public function __construct(
        CommandsBuilder $commandsBuilder,
        Application $application
    ) {
        $this->commandsBuilder = $commandsBuilder;
        $this->application = $application;
    }

    /**
     * @return Application
     */
    public function build(): Application
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
