<?php
/**
 * MagedIn Technology
 *
 * @category  MagedIn MageLab
 * @copyright Copyright (c) 2022 MagedIn Technology.
 *
 * @author    Tiago Sampaio <tiago.sampaio@magedin.com>
 */

declare(strict_types=1);

namespace MagedIn\Lab\CommandExecutor\Db;

class DbCreate extends QueryExecutorAbstract
{
    protected ?string $dbName = null;

    /**
     * @param string $dbName
     * @return void
     */
    public function setDbName(string $dbName): void
    {
        $this->dbName = $dbName;
    }

    /**
     * @return string|null
     */
    public function getDbName(): ?string
    {
        $dbName = $this->dbName;
        $this->resetDbName();
        return $dbName;
    }

    /**
     * @return void
     */
    protected function resetDbName(): void
    {
        $this->dbName = null;
    }

    /**
     * @return string
     */
    protected function getQuery(): string
    {
        return "CREATE DATABASE `{$this->getDbName()}` DEFAULT CHARACTER SET utf8mb4 DEFAULT COLLATE utf8mb4_general_ci;";
    }
}
