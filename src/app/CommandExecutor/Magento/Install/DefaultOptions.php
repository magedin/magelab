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

namespace MagedIn\Lab\CommandExecutor\Magento\Install;

use MagedIn\Lab\Config;

class DefaultOptions
{
    /**
     * @return string
     */
    public function getDefaultInstallBaseUrl(): string
    {
        return Config::get('config/default/installation_base_url');
    }

    /**
     * @param string $baseUrl
     * @return string[]
     */
    public function getInstallOptions(string $baseUrl): array
    {
        return [
            /* Database Options */
            "--db-host=" . getenv('MYSQL_HOST'),
            "--db-name=" . getenv('MYSQL_DATABASE'),
            "--db-user=" . getenv('MYSQL_USER'),
            "--db-password=" . getenv('MYSQL_PASSWORD'),

            /* Base URL */
            "--base-url=" . $baseUrl,
            "--backend-frontname=" . getenv('ADMIN_URI'),

            /* Admin Configuration */
            "--admin-firstname=" . getenv('ADMIN_FIRSTNAME'),
            "--admin-lastname=" . getenv('ADMIN_LASTNAME'),
            "--admin-email=" . getenv('ADMIN_EMAIL'),
            "--admin-user=" . getenv('ADMIN_USER'),
            "--admin-password=" . getenv('ADMIN_PASSWORD'),

            /* Cache Backend Configuration */
            "--cache-backend=" . getenv('CACHE_BACKEND'),
            "--cache-backend-redis-server=" . getenv('CACHE_BACKEND_REDIS_SERVER'),
            "--cache-backend-redis-db=" . getenv('CACHE_BACKEND_REDIS_DB'),

            /* Page Cache Configuration */
            "--page-cache=" . getenv('PAGE_CACHE'),
            "--page-cache-redis-server=" . getenv('PAGE_CACHE_REDIS_SERVER'),
            "--page-cache-redis-db=" . getenv('PAGE_CACHE_REDIS_DB'),

            /* Session Configuration */
            "--session-save=" . getenv('SESSION_SAVE'),
            "--session-save-redis-host=" . getenv('SESSION_SAVE_REDIS_HOST'),
            "--session-save-redis-log-level=" . getenv('SESSION_SAVE_REDIS_LOG_LEVEL'),
            "--session-save-redis-db=" . getenv('SESSION_SAVE_REDIS_DB'),

            /* Search Engine */
            "--search-engine=" . getenv('SEARCH_ENGINE'),
            "--elasticsearch-host=" . getenv('ES_HOST'),

            /* General Configuration */
            "--use-rewrites=1",
        ];
    }
}
