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

namespace MagedIn\Lab\Command\Project;

use MagedIn\Lab\Command\Command;
use MagedIn\Lab\CommandExecutor\CommandExecutorInterface;
use MagedIn\Lab\Config;
use MagedIn\Lab\ObjectManager;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NameCommand extends Command
{
    protected function configure()
    {
        $this->addArgument(
            'name',
            InputArgument::OPTIONAL,
            'Set the project name.'
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');
        if (empty($name)) {
            $projectName = Config::get('project/name');
            if (empty($projectName)) {
                $output->writelnInfo("You haven't set the project name yet.");
                return Command::SUCCESS;
            }
            $output->writeln("The current project name is the following:");
            $output->writelnInfo($projectName);
            return Command::SUCCESS;
        }

        /** @var CommandExecutorInterface $executor */
        $executor = ObjectManager::getInstance()->get(\MagedIn\Lab\CommandExecutor\Project\NameExecutor::class);
        $executor->execute([], ['name' => $name]);

        return Command::SUCCESS;
    }
}
