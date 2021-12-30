<?php

declare(strict_types=1);

namespace MagedIn\Lab\Command\Environment;

use MagedIn\Lab\ObjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class XdebugToggleCommand extends XdebugAbstractCommand
{
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $resultCode = $this->checkXdebugStatus($output);

        /** Xdebug is Currently DISABLED */
        if ($resultCode === Command::FAILURE) {
            /** Enable Xdebug */
            $xdebugCommand = $this->getApplication()->find('xdebug-enable');
        } else {
            /** Disable Xdebug */
            $xdebugCommand = $this->getApplication()->find('xdebug-disable');
        }

        $emptyInput = ObjectManager::getInstance()->create(ArrayInput::class, [
            'parameters' => ['--skip-checks' => true]
        ]);
        $xdebugCommand->run($emptyInput, $output);
        return Command::SUCCESS;
    }
}
