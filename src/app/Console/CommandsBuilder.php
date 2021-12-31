<?php

declare(strict_types=1);

namespace MagedIn\Lab\Console;

use MagedIn\Lab\Config;
use MagedIn\Lab\Helper;
use MagedIn\Lab\ObjectManager;
use Symfony\Component\Console\Command\Command;

class CommandsBuilder
{
    private Helper\SpecialCommands $specialCommands;

    public function __construct(
        \MagedIn\Lab\Helper\SpecialCommands $specialCommands
    ) {
        $this->specialCommands = $specialCommands;
    }

    /**
     * @return array
     */
    public function build(): array
    {
        $commands = [];
        $commandsConfig = (array) Config::get('commands');
        foreach ($commandsConfig as $name => $commandConfig) {
            $command = $this->buildCommand($commandConfig);
            if (empty($command)) {
                continue;
            }
            $commands[$name] = $command;
        }
        return $commands;
    }

    /**
     * @param array $config
     * @return Command|null
     */
    private function buildCommand(array $config): ?Command
    {
        $currentClass = $config['class'] ?? null;
        if (!class_exists($currentClass)) {
            return null;
        }

        $code = $config['code'] ?? null;
        $aliases = (array) $config['aliases'] ?? null;
        $description = $config['description'] ?? null;
        $override = $config['override'] ?? false;

        /** @var Command $command */
        $command = ObjectManager::getInstance()->create($config['class'], ['name' => $code]);
        if (!$command instanceof Command) {
            return null;
        }

        if ($override) {
            $this->specialCommands->add($code)->addAliases($aliases);
        }

        $command->setAliases($aliases);
        $command->setDescription($description);
        return $command;
    }
}
