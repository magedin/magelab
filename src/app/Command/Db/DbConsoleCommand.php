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

namespace MagedIn\Lab\Command\Db;

use MagedIn\Lab\Command\Command;
use MagedIn\Lab\CommandExecutor\CommandExecutorInterface;
use MagedIn\Lab\ObjectManager;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DbConsoleCommand extends Command
{
    protected function configure()
    {
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var CommandExecutorInterface $executor */
        $executor = ObjectManager::getInstance()->get(\MagedIn\Lab\CommandExecutor\Db\DbConsole::class);

        /** @var \MagedIn\Lab\Helper\DockerLab\DirList $dirList */
        $dirList = ObjectManager::getInstance()->get(\MagedIn\Lab\Helper\DockerLab\DirList::class);
        $srcDir = $dirList->getSrcDir();
        $magentoEnvFile = $srcDir . DS . 'app' . '/' . 'etc' . '/' . 'env.php';
        $config = [];
        if (file_exists($magentoEnvFile)) {
            $magentoEnv = include_once $magentoEnvFile;
            $defaultConnection = $magentoEnv['db']['connection']['default'] ?? [];

            $config['username'] = $defaultConnection['username'] ?? null;
            $config['password'] = $defaultConnection['password'] ?? null;
            $config['database'] = $defaultConnection['dbname']   ?? null;
        }
        $executor->execute($config);
        return Command::SUCCESS;
    }
}
