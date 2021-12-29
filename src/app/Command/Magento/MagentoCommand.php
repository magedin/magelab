<?php

declare(strict_types=1);

namespace MagedIn\Lab\Command\Magento;

use MagedIn\Lab\CommandBuilder\Magento;
use MagedIn\Lab\Model\Process;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MagentoCommand extends Command
{
    /**
     * @var Magento
     */
    private Magento $magentoCommandBuilder;

    public function __construct(
        Magento $magentoCommandBuilder,
        string $name = null
    ) {
        parent::__construct($name);
        $this->magentoCommandBuilder = $magentoCommandBuilder;
    }

    protected function configure()
    {
        $this->addArgument(
            'magento-command',
            InputArgument::OPTIONAL,
            'The Magento 2 CLI Command'
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
        $command[] = $input->getArgument('magento-command');

        Process::run($command, function ($type, $buffer) use ($output) {
            $output->writeln($buffer);
        }, true);

        return Command::SUCCESS;
    }
}
