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

use MagedIn\Lab\Command\ProxyCommand;
use MagedIn\Lab\CommandBuilder\Magento;
use MagedIn\Lab\Helper\Console\NonDefaultOptions;
use MagedIn\Lab\Model\Process;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MagentoCommand extends ProxyCommand
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
