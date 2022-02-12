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
use MagedIn\Lab\ObjectManager;
use Symfony\Component\Console\Command\Command;

class CommandsBuilder
{
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

        /** @var Command $command */
        $command = ObjectManager::getInstance()->create($config['class'], ['name' => $code]);
        if (!$command instanceof Command) {
            return null;
        }

        $command->setAliases($aliases);
        $command->setDescription($description);

        return $command;
    }
}
