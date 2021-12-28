<?php

declare(strict_types=1);

namespace MageLab\Command\Environment;

use MageLab\CommandBuilder\DockerCompose;
use MageLab\Helper\DockerServiceState;
use MageLab\Model\ProcessFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class StartCommand extends Command
{
    /**
     * @var DockerServiceState
     */
    private DockerServiceState $dockerServiceState;

    /**
     * @var DockerCompose
     */
    private DockerCompose $dockerComposeCommandBuilder;

    private ProcessFactory $processFactory;

    public function __construct(
        DockerServiceState $dockerServiceState,
        DockerCompose $dockerComposeCommandBuilder,
        ProcessFactory $processFactory,
        string $name = null
    ) {
        parent::__construct($name);
        $this->dockerServiceState = $dockerServiceState;
        $this->dockerComposeCommandBuilder = $dockerComposeCommandBuilder;
        $this->processFactory = $processFactory;
    }

    protected function configure()
    {
        $this->addOption(
            'force',
            'f',
            InputOption::VALUE_NONE,
            'Force to run the containers.'
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$input->getOption('force') && $this->dockerServiceState->isRunning()) {
            $output->writeln('The services are already running.');
            return Command::SUCCESS;
        }

        $command = $this->dockerComposeCommandBuilder->build();

        $command[] = 'up';
        $command[] = '-d';

        $output->writeln('Starting the containers.');

        $process = $this->processFactory->create($command);
        $process->run(function ($type, $buffer) use ($output) {
            $output->write($buffer);
        });

        $output->writeln('Containers has been started.');

        return Command::SUCCESS;
    }
}
