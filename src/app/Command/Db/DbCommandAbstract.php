<?php
/**
 * MagedIn Technology
 *
 * @category  MagedIn MageLab
 * @copyright Copyright (c) 2023 MagedIn Technology.
 *
 * @author    Tiago Sampaio <tiago.sampaio@magedin.com>
 */

declare(strict_types=1);

namespace MagedIn\Lab\Command\Db;

use MagedIn\Lab\Command\Command;
use MagedIn\Lab\CommandExecutor\Db\DbAbstract as Executor;
use MagedIn\Lab\Helper\Magento\DbConfig;

abstract class DbCommandAbstract extends Command
{
    /**
     * @var Executor
     */
    protected Executor $executor;

    /**
     * @var DbConfig
     */
    protected DbConfig $dbConfig;

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
}
