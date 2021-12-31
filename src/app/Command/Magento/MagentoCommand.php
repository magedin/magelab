<?php

declare(strict_types=1);

namespace MagedIn\Lab\Command\Magento;

use MagedIn\Lab\Command\SpecialCommand;
use MagedIn\Lab\CommandBuilder\Magento;
use MagedIn\Lab\Console\NonDefaultOptions;
use MagedIn\Lab\Model\Process;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MagentoCommand extends SpecialCommand
{
    /**
     * @var Magento
     */
    private Magento $magentoCommandBuilder;

    public function __construct(
        Magento $magentoCommandBuilder,
        NonDefaultOptions $nonDefaultOptions,
        string $name = null
    ) {
        parent::__construct($nonDefaultOptions, $name);
        $this->magentoCommandBuilder = $magentoCommandBuilder;
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
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $command = $this->magentoCommandBuilder->build();
        $command = array_merge($command, $this->getShiftedArgv());

        Process::run($command, [
            'tty' => true,
            'callback' => function ($type, $buffer) use ($output) {
                $output->writeln($buffer);
            },
        ]);

        return Command::SUCCESS;
    }
}
