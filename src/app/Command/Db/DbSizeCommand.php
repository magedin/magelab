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
use MagedIn\Lab\Helper\Magento\DbConfig;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DbSizeCommand extends Command
{
    /**
     * @var Executor
     */
    private Executor $executor;

    /**
     * @var DbConfig
     */
    private DbConfig $dbConfig;

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
        parent::__construct($name);
        $this->executor = $executor;
        $this->dbConfig = $dbConfig;
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
        $this->executor->execute($this->dbConfig->get());
        return Command::SUCCESS;
    }
}