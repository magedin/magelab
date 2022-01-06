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

use MagedIn\Lab\CommandBuilder\DockerCompose;
use MagedIn\Lab\Model\Process;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class StatusCommand extends Command
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

    protected function configure()
    {
        $this->addArgument(
            'service',
            InputArgument::IS_ARRAY | InputArgument::OPTIONAL,
            'Select a specific service(s)'
        );

        /** Can't be --quiet|-q due to a conflict with Symfony Command */
        $this->addOption(
            'silent',
            null,
            InputOption::VALUE_NONE,
            'Only display IDs'
        );

        $this->addOption(
            'services',
            's',
            InputOption::VALUE_NONE,
            'Display services'
        );

        $this->addOption(
            'all',
            'a',
            InputOption::VALUE_NONE,
            'Show all stopped containers (including those created by the run command)'
        );

        $this->addOption(
            'filter',
            'f',
            InputOption::VALUE_OPTIONAL,
            'Filter services by a property (KEY=VAL)'
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $subcommands = ['ps'];
        if ($input->getOption('silent')) {
            $subcommands[] = '-q';
        }

        if ($input->getOption('services')) {
            $subcommands[] = '--services';
        }

        if ($input->getOption('all')) {
            $subcommands[] = '-a';
        }

        if ($filter = $input->getOption('filter')) {
            $subcommands[] = '--filter';
            $subcommands[] = $filter;
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
