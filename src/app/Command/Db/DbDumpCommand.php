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
use MagedIn\Lab\CommandExecutor\Db\DbDump as Executor;
use MagedIn\Lab\Helper\Magento\DbConfig;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DbDumpCommand extends DbCommandAbstract
{
    /**
     * @param Executor $executor
     * @param DbConfig $dbConfig
     * @param string|null $name
     */
    public function __construct(
        Executor $executor,
        DbConfig $dbConfig,
        string $name = null
    ) {
        parent::__construct($executor, $dbConfig, $name);
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this->addArgument(
            self::DB_NAME,
            InputArgument::OPTIONAL,
            'The database name.'
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $dbName = $input->getArgument(self::DB_NAME);
        if (!$dbName) {
            $dbName = $this->dbConfig->getDatabase();
        }
        $this->executor->setDbName($dbName);
        $this->executor->execute($this->dbConfig->get());
        return Command::SUCCESS;
    }
}
