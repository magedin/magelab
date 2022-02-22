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

namespace MagedIn\Lab\Command\Magento;

use MagedIn\Lab\Command\Command;
use MagedIn\Lab\CommandExecutor\Magento\Install;
use MagedIn\Lab\Model\Process;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class InstallCommand extends Command
{
    /**
     * @var Install
     */
    private Install $commandExecutor;

    /**
     * @var Install\DefaultOptions
     */
    private Install\DefaultOptions $defaultOptions;

    public function __construct(
        Install $commandExecutor,
        Install\DefaultOptions $defaultOptions,
        string $name = null
    ) {
        $this->commandExecutor = $commandExecutor;
        $this->defaultOptions = $defaultOptions;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->addOption(
            'base_url',
            'u',
            InputOption::VALUE_OPTIONAL,
            'The base URL Magento 2 will use.',
            $this->defaultOptions->getDefaultInstallBaseUrl(),
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $config = [
            'base_url' => $input->getOption('base_url'),
            'callback' => function ($type, $buffer) use ($output) {
                $output->writeln($buffer);
            },
        ];
        $this->commandExecutor->execute([], $config);
        return Command::SUCCESS;
    }
}
