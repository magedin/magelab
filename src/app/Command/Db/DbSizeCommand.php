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
use MagedIn\Lab\CommandExecutor\Db\DbSize as Executor;
use MagedIn\Lab\Helper\DockerLab\DirList;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DbSizeCommand extends Command
{
    /**
     * @var Executor
     */
    private Executor $executor;

    /**
     * @var DirList
     */
    private DirList $dirList;

    /**
     * @param Executor $executor
     * @param DirList $dirList
     * @param string|null $name
     */
    public function __construct(
        Executor $executor,
        DirList $dirList,
        string $name = null
    ) {
        parent::__construct($name);
        $this->executor = $executor;
        $this->dirList = $dirList;
    }

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
        $config = [];
        if (file_exists($this->getMagentoEnvFile())) {
            $magentoEnv = include_once $this->getMagentoEnvFile();
            $defaultConnection = $magentoEnv['db']['connection']['default'] ?? [];

            $config['username'] = $defaultConnection['username'] ?? null;
            $config['password'] = $defaultConnection['password'] ?? null;
            $config['database'] = $defaultConnection['dbname']   ?? null;
        }
        $this->executor->execute($config);
        return Command::SUCCESS;
    }

    /**
     * @return string
     */
    private function getMagentoEnvFile(): string
    {
        $srcDir = $this->dirList->getSrcDir();
        return $srcDir . DS . implode(DS, ['app', 'etc', 'env.php']);
    }
}
