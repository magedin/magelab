<?php

declare(strict_types=1);

namespace MagedIn\Lab\Command\Magento;

use MagedIn\Lab\CommandBuilder\Magento;
use MagedIn\Lab\Model\Process;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class N98Command extends Command
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
        $command = $this->magentoCommandBuilder->build();
        array_pop($command);
        $command[] = 'n98';
        $argv = $argv ?? $_SERVER['argv'] ?? [];
        array_shift($argv);
        array_shift($argv);

        $command = array_merge($command, $argv);

        if ($input->getOption('describe')) {
            $command[] = '--help';
        }

        Process::run($command, [
            'tty' => true,
            'callback' => function ($type, $buffer) use ($output) {
                $output->writeln($buffer);
            },
        ]);

        return Command::SUCCESS;
    }

    public function getDefinition()
    {
        $options = $this->filterOptions();
        foreach ($options as $option) {
            $this->addOption($option);
        }
        return parent::getDefinition();
    }

    /**
     * @return array
     */
    private function filterOptions(): array
    {
        $argv = $argv ?? $_SERVER['argv'] ?? [];
        array_shift($argv);

        $options = array_map(function (&$option) {
            if (str_starts_with($option, '--')) {
                return substr($option, 2);
            }
            if (str_starts_with($option, '-')) {
                return substr($option, 1);
            }
            return false;
        }, $argv);
        $options = array_filter($options, function ($option) {
            $restricted = ['version', 'help'];
            if (!$option || in_array($option, $restricted)) {
                return false;
            }
            return $option;
        });
        return $options;
    }
}
