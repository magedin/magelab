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

use Exception;
use MagedIn\Lab\Command\Command;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\Console\Exception\CommandNotFoundException;
use Symfony\Component\Console\Exception\NamespaceNotFoundException;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class Application extends BaseApplication
{
    private string $logo = '
    __  ___                     ______         __  ___                 __          __  
   /  |/  /___ _____ ____  ____/ /  _/___     /  |/  /___ _____ ____  / /   ____ _/ /_ 
  / /|_/ / __ `/ __ `/ _ \/ __  // // __ \   / /|_/ / __ `/ __ `/ _ \/ /   / __ `/ __ \
 / /  / / /_/ / /_/ /  __/ /_/ // // / / /  / /  / / /_/ / /_/ /  __/ /___/ /_/ / /_/ /
/_/  /_/\__,_/\__, /\___/\__,_/___/_/ /_/  /_/  /_/\__,_/\__, /\___/_____/\__,_/_.___/ 
             /____/                                     /____/                         


';

    /**
     * @return string
     */
    public function getHelp(): string
    {
        return $this->logo . parent::getHelp();
    }

    /**
     * @return string
     */
    public function getLongVersion(): string
    {
        return parent::getLongVersion() . ' by <info>MagedIn Technology</info>';
    }

    /**
     * @param InputInterface|null $input
     * @param OutputInterface|null $output
     * @return int
     * @throws Exception
     */
    public function execute(InputInterface $input = null, OutputInterface $output = null): int
    {
        if (null === $input) {
            $input = new ArgvInput();
        }

        if (null === $output) {
            $output = new Output\ConsoleOutput();
        }

        $name = $this->getCommandName($input);
        if ($name) {
            $command = $this->findCommand($name);
            if ($command && $command->isProxyCommand()) {
                return $this->proxyRun($command, $input, $output);
            }
        }
        return $this->run($input, $output);
    }

    /**
     * @param string $name
     * @return Command|null
     */
    private function findCommand(string $name): ?Command
    {
        try {
            /** @var Command $command */
            $command = $this->find($name);
        }  catch (\Throwable $e) {
            return null;
        }
        return $command;
    }

    /**
     * @param Command $command
     * @param InputInterface|null $input
     * @param OutputInterface|null $output
     * @return int
     * @throws Exception
     */
    private function proxyRun(Command $command, InputInterface $input = null, OutputInterface $output = null): int
    {
        return (int) $command->run($input, $output);
    }
}
