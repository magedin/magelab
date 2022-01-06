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

namespace MagedIn\Lab\Command\Environment;

use MagedIn\Lab\Command\Command;
use MagedIn\Lab\CommandBuilder\DockerCompose;
use MagedIn\Lab\Model\Process;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

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
            InputOption::VALUE_NONE,
            'Follow log output.'
        );

        $this->addOption(
            'timestamps',
            't',
            InputOption::VALUE_NONE,
            'Show timestamps.'
        );

        $this->addOption(
            'tail',
            null,
            InputOption::VALUE_NONE,
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

        $subcommands[] = 'logs';

        if ($input->getOption('follow')) {
            $subcommands[] = '-f';
        }

        if ($input->getOption('timestamps')) {
            $subcommands[] = '-t';
        }

        if ($tail = $input->getOption('tail')) {
            $subcommands[] = '--tail';
            $subcommands[] = $tail;
        }

        if ($service = $input->getArgument('service')) {
            $subcommands = array_merge($subcommands, $service);
        }

        $command = $this->dockerComposeCommandBuilder->build($subcommands);
        Process::run($command, [
            'callback' => function ($type, $buffer) use ($output) {
                $output->write($buffer);
            }
        ]);

        return Command::SUCCESS;
    }
}
