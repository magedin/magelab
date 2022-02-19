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
use MagedIn\Lab\Model\Config\ConfigFacade;
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

    private ConfigFacade $configFacade;

    public function __construct(
        CommandsBuilder $commandsBuilder,
        Application $application,
        ConfigFacade $configFacade
    ) {
        $this->commandsBuilder = $commandsBuilder;
        $this->application = $application;
        $this->configFacade = $configFacade;
    }

    /**
     * @return Application
     */
    public function build(): Application
    {
        $this->application->setName($this->configFacade->application()->getName());
        $this->application->setVersion($this->configFacade->application()->getVersion());

        /** @var Command $command */
        foreach ($this->commandsBuilder->build() ?? [] as $command) {
            $this->application->add($command);
        }
        return $this->application;
    }
}
