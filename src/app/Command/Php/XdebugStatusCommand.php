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

use MagedIn\Lab\Command\Command;
use MagedIn\Lab\CommandBuilder\DockerComposeExec;
use MagedIn\Lab\CommandExecutor\Php\XdebugStatus;
use MagedIn\Lab\Model\Process;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class XdebugStatusCommand extends Command
{
    /**
     * @var DockerComposeExec
     */
    private DockerComposeExec $dockerComposeExecCommandBuilder;

    /**
     * @var XdebugStatus
     */
    private XdebugStatus $xdebugStatusCommandExecutor;

    public function __construct(
        DockerComposeExec $dockerComposeExecCommandBuilder,
        XdebugStatus $xdebugStatusCommandExecutor,
        string $name = null
    ) {
        parent::__construct($name);
        $this->dockerComposeExecCommandBuilder = $dockerComposeExecCommandBuilder;
        $this->xdebugStatusCommandExecutor = $xdebugStatusCommandExecutor;
    }

    protected function configure()
    {
        $this->addOption(
            'silent',
            's',
            InputOption::VALUE_NONE,
            'Run command silently.'
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $isEnabled = $this->xdebugStatusCommandExecutor->execute();
        if ($isEnabled === false) {
            $messages = [
                "<fg=yellow>Xdebug is currently DISABLED.</>",
            ];
            $result = Command::FAILURE;
        } else {
            $messages = [
                "<fg=green>Xdebug is currently ENABLED.</>",
                "<fg=white>Keeping Xdebug always enabled may affect PHP performance.</>",
                "<fg=white>Try to disabled when you do not need it.</>"
            ];
            $result = Command::SUCCESS;
        }

        if (!$input->getOption('silent')) {
            $output->writeln($messages);
        }
        return $result;
    }
}
