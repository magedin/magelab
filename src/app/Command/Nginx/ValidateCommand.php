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

namespace MagedIn\Lab\Command\Nginx;

use MagedIn\Lab\Command\Command;
use MagedIn\Lab\CommandExecutor\Nginx\Nginx as CommandExecutor;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ValidateCommand extends Command
{
    private CommandExecutor $commandExecutor;

    public function __construct(
        CommandExecutor $commandExecutor,
        string $name = null
    ) {
        $this->commandExecutor = $commandExecutor;
        parent::__construct($name);
    }


    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->commandExecutor->execute(['-t']);
        return Command::SUCCESS;
    }
}
