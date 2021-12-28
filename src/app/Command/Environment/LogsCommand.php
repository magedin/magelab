<?php

declare(strict_types=1);

namespace MageLab\Command\Environment;

use MageLab\CommandBuilder\DockerCompose;
use MageLab\Helper\DockerServiceState;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class LogsCommand extends Command
{
    /**
     * @var DockerCompose
     */
    private DockerCompose $dockerComposeCommandBuilder;

    public function __construct(
        DockerCompose $dockerComposeCommandBuilder,
        string $name = null
    ) {
        parent::__construct($name);
        $this->dockerComposeCommandBuilder = $dockerComposeCommandBuilder;
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this->addArgument(
            'service',
            InputArgument::IS_ARRAY | InputArgument::OPTIONAL,
            'You can specify specific services.'
        );

        $this->addOption(
            'follow',
            'f',
            InputOption::VALUE_NONE | InputOption::VALUE_OPTIONAL,
            'Follow log output.'
        );

        $this->addOption(
            'timestamps',
            't',
            InputOption::VALUE_NONE | InputOption::VALUE_OPTIONAL,
            'Show timestamps.'
        );

        $this->addOption(
            'tail',
            null,
            InputOption::VALUE_OPTIONAL,
            'Number of lines to show from the end of the logs for each container.'
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $command = $this->dockerComposeCommandBuilder->build();
        $command[] = 'logs';

        if ($service = $input->getArgument('service')) {
            $command = array_merge($command, $service);
        }

        if ($input->getOption('follow')) {
            $command[] = '-f';
        }

        if ($input->getOption('timestamps')) {
            $command[] = '-t';
        }

        if ($tail = $input->getOption('tail')) {
            $command[] = '--tail';
            $command[] = $tail;
        }

        $process = new Process($command);
        $process->run(function ($type, $buffer) use ($output) {
            $output->write($buffer);
        });

        return Command::SUCCESS;
    }
}
