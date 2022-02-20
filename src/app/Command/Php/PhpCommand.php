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

namespace MagedIn\Lab\Command\Php;

use MagedIn\Lab\Command\ProxyCommand;
use MagedIn\Lab\CommandExecutor\CommandExecutorInterface;
use MagedIn\Lab\CommandExecutor\Php\Php;
use MagedIn\Lab\Helper\Console\NonDefaultOptions;
use MagedIn\Lab\ObjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PhpCommand extends ProxyCommand
{
    private Php $commandExecutor;

    public function __construct(
        Php $commandExecutor,
        NonDefaultOptions $nonDefaultOptions,
        string $name = null
    ) {
        $this->commandExecutor = $commandExecutor;
        parent::__construct($nonDefaultOptions, $name);
    }


    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->commandExecutor->execute();
        return Command::SUCCESS;
    }
}
