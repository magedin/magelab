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

namespace MagedIn\Lab\Command\Php;

use Symfony\Component\Console\Command\Command;
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
        $isEnabled = $this->xdebugStatusCommandExecutor->execute();

        /** Xdebug is Currently DISABLED */
        if ($isEnabled === false) {
            /** Enable Xdebug */
            $xdebugCommand = $this->getApplication()->find('xdebug:enable');
        } else {
            /** Disable Xdebug */
            $xdebugCommand = $this->getApplication()->find('xdebug:disable');
        }

        $arrayInput = $this->arrayInputFactory->create(['--skip-checks' => true]);
        $xdebugCommand->run($arrayInput, $output);
        return Command::SUCCESS;
    }
}
