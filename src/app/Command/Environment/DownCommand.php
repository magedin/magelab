<?php

declare(strict_types=1);

namespace MageLab\Command\Environment;

use MageLab\CommandBuilder\DockerCompose;
use MageLab\Helper\DockerServiceState;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class DownCommand extends Command
{
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
        $serviceState = new DockerServiceState();
        if (!$input->getOption('force') && !$serviceState->isRunning()) {
            $output->writeln('The services are already down.');
            return Command::SUCCESS;
        }

        $commandBuilder = new DockerCompose();
        $command = $commandBuilder->build();

        $command[] = 'down';

        $output->writeln('Stopping and removing the containers.');

        $process = new Process($command);
        $process->run(function ($type, $buffer) use ($output) {
            $output->write($buffer);
        });

        $output->writeln('Containers has been removed.');

        return Command::SUCCESS;
    }
}
