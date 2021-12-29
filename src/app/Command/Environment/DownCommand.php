<?php

declare(strict_types=1);

namespace MagedIn\Lab\Command\Environment;

use MagedIn\Lab\CommandBuilder\DockerCompose;
use MagedIn\Lab\Helper\DockerServiceState;
use MagedIn\Lab\Model\Process;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DownCommand extends Command
{
    /**
     * @var DockerServiceState
     */
    private DockerServiceState $dockerServiceState;

    /**
     * @var DockerCompose
     */
    private DockerCompose $dockerComposeCommandBuilder;

    public function __construct(
        DockerServiceState $dockerServiceState,
        DockerCompose $dockerComposeCommandBuilder,
        string $name = null
    ) {
        parent::__construct($name);
        $this->dockerServiceState = $dockerServiceState;
        $this->dockerComposeCommandBuilder = $dockerComposeCommandBuilder;
    }

    protected function configure()
    {
        $this->addOption(
            'force',
            'f',
            InputOption::VALUE_NONE,
            'Force to stop the containers.'
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$input->getOption('force') && !$this->dockerServiceState->isRunning()) {
            $output->writeln('The services are already down.');
            return Command::SUCCESS;
        }

        $command = $this->dockerComposeCommandBuilder->build();

        $command[] = 'down';

        $output->writeln('Stopping and removing the containers.');

        Process::run($command, function ($type, $buffer) use ($output) {
            $output->write($buffer);
        });

        $output->writeln('Containers has been removed.');

        return Command::SUCCESS;
    }
}
