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

class DbSize extends QueryExecutorAbstract
{
    /**
     * @return string
     */
    protected function getQuery(): string
    {
        return "SELECT table_schema \"Database\", ROUND(SUM(data_length + index_length) / 1024 / 1024, 1) \"Size in MB\"
FROM information_schema.tables GROUP BY table_schema;";
    }
}
