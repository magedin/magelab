<?php

declare(strict_types=1);

namespace MagedIn\Lab\Command\Environment;

use MagedIn\Lab\CommandBuilder\DockerComposeExec;
use MagedIn\Lab\Model\Process;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class XdebugCommand extends Command
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
        $this->addArgument(
            'service',
            InputArgument::REQUIRED,
            'Select a specific service container'
        );
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
        $command[] = 'bash';

        Process::run($command, function ($type, $buffer) use ($output) {
            $output->writeln($buffer);
        }, true);

        return Command::SUCCESS;
    }
}
