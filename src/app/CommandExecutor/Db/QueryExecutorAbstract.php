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

abstract class QueryExecutorAbstract extends DbAbstract
{
    /**
     * @return string|null
     */
    abstract protected function getQuery(): ?string;

    /**
     * @var string|null
     */
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
        // $this->resetDbName();
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
     * @return array
     */
    protected function getCommand(): array
    {
        $command = $this->getBaseCommand();
        if ($this->getQuery()) {
            $command[] = "-e";
            $command[] = $this->getQuery();
        }
        return $command;
    }
}
