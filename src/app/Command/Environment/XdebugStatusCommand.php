<?php

declare(strict_types=1);

namespace MagedIn\Lab\Command\Environment;

use MagedIn\Lab\CommandBuilder\DockerComposeExec;
use MagedIn\Lab\Model\Process;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class XdebugStatusCommand extends Command
{
    /**
     * @var DockerComposeExec
     */
    private DockerComposeExec $dockerComposeExecCommandBuilder;

    public function __construct(
        DockerComposeExec $dockerComposeExecCommandBuilder,
        string $name = null
    ) {
        parent::__construct($name);
        $this->dockerComposeExecCommandBuilder = $dockerComposeExecCommandBuilder;
    }

    protected function configure()
    {
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $command = $this->dockerComposeExecCommandBuilder->build();
        $command[] = 'php';
        $command[] = 'php';
        $command[] = '--version';

        $process = Process::run($command, null, false, true);
        $result = $process->getOutput();

        preg_match("/.*Xdebug.*Copyright.*/", $result, $matches);
        if (empty($matches)) {
            $messages = [
                "<fg=yellow>Xdebug is currently DISABLED.</>",
            ];
        } else {
            $messages = [
                "<fg=green>Xdebug is currently ENABLED.</>",
                "<fg=white>Keeping Xdebug always enabled may affect PHP performance.</>",
                "<fg=white>Try to disabled when you do not need it.</>"
            ];
        }
        $output->writeln($messages);
        return Command::SUCCESS;
    }
}
