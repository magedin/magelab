<?php

declare(strict_types=1);

namespace MagedIn\Lab\Command\Magento;

use MagedIn\Lab\Command\SpecialCommand;
use MagedIn\Lab\CommandBuilder\DockerComposePhpExec;
use MagedIn\Lab\Helper\Console\NonDefaultOptions;
use MagedIn\Lab\Model\Process;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class N98Command extends SpecialCommand
{
    /**
     * @var DockerComposePhpExec
     */
    private DockerComposePhpExec $dockerComposePhpExecCommandBuilder;

    public function __construct(
        DockerComposePhpExec $dockerComposePhpExecCommandBuilder,
        NonDefaultOptions $nonDefaultOptions,
        string $name = null
    ) {
        parent::__construct($nonDefaultOptions, $name);
        $this->dockerComposePhpExecCommandBuilder = $dockerComposePhpExecCommandBuilder;
    }

    protected function configure()
    {
        $this->addArgument(
            'magento-command',
            InputArgument::OPTIONAL,
            'The Magento 2 CLI Command'
        );

        $this->addArgument(
            'subcommand',
            InputArgument::OPTIONAL | InputArgument::IS_ARRAY,
            'The Magento 2 CLI Sub-command'
        );

        $this->addOption(
            'describe',
            null,
            InputOption::VALUE_NONE,
            'Show the Magento 2 CLI help'
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $command = $this->dockerComposePhpExecCommandBuilder->build();
        $command[] = 'n98';
        $argv = $this->getShiftedArgv();
        $command = array_merge($command, $argv);

        Process::run($command, [
            'tty' => true,
            'callback' => function ($type, $buffer) use ($output) {
                $output->writeln($buffer);
            },
        ]);

        return Command::SUCCESS;
    }
}
