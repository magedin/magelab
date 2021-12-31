<?php

declare(strict_types=1);

namespace MagedIn\Lab\Command\Environment;

use MagedIn\Lab\CommandBuilder\DockerCompose;
use MagedIn\Lab\Console\Output\OutputWrapperBuilder;
use MagedIn\Lab\Helper\DockerServiceState;
use MagedIn\Lab\Model\Process;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

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

    /**
     * @var OutputWrapperBuilder
     */
    private OutputWrapperBuilder $outputWrapperBuilder;

    public function __construct(
        DockerServiceState $dockerServiceState,
        DockerCompose $dockerComposeCommandBuilder,
        OutputWrapperBuilder $outputWrapperBuilder,
        string $name = null
    ) {
        parent::__construct($name);
        $this->dockerServiceState = $dockerServiceState;
        $this->dockerComposeCommandBuilder = $dockerComposeCommandBuilder;
        $this->outputWrapperBuilder = $outputWrapperBuilder;
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

        $output->writelnInfo('Starting the containers.');
        $outputWrapper = $this->outputWrapperBuilder->build($output);
        Process::run($command, [
            'callback' => function ($type, $buffer) use ($outputWrapper) {
                $outputWrapper->overwrite($buffer);
            }
        ]);
        $output->writelnInfo('Containers has been started.');

        return Command::SUCCESS;
    }
}
